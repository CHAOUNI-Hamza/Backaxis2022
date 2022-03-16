<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Validator;

class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'forgotpassword', 'resetpassword']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:10|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    // forgot password
    public function forgotpassword(Request $request) {

        // validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation
    
        $status = Password::sendResetLink(
            $request->only('email')
        );    
        if ($status == Password::RESET_LINK_SENT) {
            return [
                'status' => __($status)
            ];
        };
    
        throw ValidationException::withMessages([
            'email' => [trans($status)]
        ]);
    }

    // reset password
    public function resetpassword(Request $request) {

        // validation
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
    
                $user->tokens()->delete();
    
                event(new PasswordReset($user));
            }
        );
    
        if ($status == Password::PASSWORD_RESET) {
            return response([
                'message'=> 'Password reset successfully'
            ]);
        }
    
        return response([
            'message'=> __($status)
        ], 500);
    }

    // store
    public function store(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|min:5|max:20',
            'password' => 'required|min:10|max:30',
            'role' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation

        $user = new User;
        $this->authorize("store", $user);
        $user->name = $request->name;
        $user->role = $request->role;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response('created', 200)
                  ->header('Content-Type', 'text/plain');
    }

    // User
    public function index(request $request) {

        if( $request->created_at ) {
            $user = User::Orderby( $request->sortby , $request->orderby )->whereDate( 'created_at', $request->created_at )->paginate($request->paginate);
            // authorize
            $this->authorize("index", $user);
        }

        elseif( $request->updated_at ) {
            $user = User::Orderby( $request->sortby , $request->orderby )->whereDate( 'updated_at', $request->updated_at )->paginate($request->paginate);
            // authorize
            $this->authorize("index", $user);
        }

        elseif( $request->date_from && $request->date_to ) {
            $user = User::Orderby( $request->sortby , $request->orderby )->whereDate('created_at', [$request->date_from, $request->date_to])->paginate($request->paginate);
            // authorize
            $this->authorize("index", $user);
        }

        elseif( $request->expand ) {
            return new UserResource(User::findOrFail($request->expand));
            // authorize
            $this->authorize("index", $user);
        }
        elseif( $request->filter ) {
            $user = User::Orderby( $request->sortby , $request->orderby )->where( $request->filter, 'LIKE', "%$request->filtervalue%" )->get();
            // authorize
            $this->authorize("index", $user);
        } else {
            $user = User::Orderby( $request->sortby , $request->orderby )->paginate($request->paginate);
            // authorize
            $this->authorize("index", $user);
        }

        
        
        
        
        return UserResource::collection($user);

    }

    // update
    public function update(request $request, $id) {

        // validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|min:5|max:20',
            'password' => 'required|min:10|max:30',
            'role' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation
        
        $user = User::find($id);

        // authorize
        $this->authorize("update", $user);

        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return 'Updated';

    }

    // trashed
    public function trashed() {
        $user = User::onlyTrashed()->get();

        // authorize
        $this->authorize("destroy", $user);

        return UserResource::collection($user);
    }

    // delete
    public function destroy($id) {
        $user = User::withTrashed()
        ->where('id', $id);

        // authorize
        $this->authorize("destroy", $user);

        $user->delete();
        return 'delete';
    }

    // restore
    public function restore($id) {
        $user = User::onlyTrashed()
        ->where('id', $id);

        // authorize
        $this->authorize("restore", $user);

        $user->restore();
        return 'restore';
    }

    // forced
    public function forced($id) {
        $user = User::onlyTrashed()
        ->where('id', $id);

        // authorize
        $this->authorize("forced", $user);

        $user->forceDelete();
        return 'forced';
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 6000000
        ]);
    }
}
