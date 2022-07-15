<?php

namespace App\Http\Controllers\Sabonner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sabonner;
use App\Http\Resources\SabonnerResource;
use Validator;

class SabonnerController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
   /* public function __construct()
    {
        $this->middleware('auth:api');
    }*/
    
    // index
    public function index(Request $request)
    {
        
        if( $request->created_at ) {
            $sabonner = Sabonner::Orderby( $request->sortby , $request->orderby )
                            ->whereDate( 'created_at', $request->created_at )
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        }
        elseif( $request->updated_at ) {
            $sabonner = Sabonner::Orderby( $request->sortby , $request->orderby )
                            ->whereDate( 'updated_at', $request->updated_at )
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        }
        elseif( $request->date_from && $request->date_to ) {
            $sabonner = Sabonner::Orderby( $request->sortby , $request->orderby )
                            //->whereBetween('created_at', [$request->date_from, $request->date_to])
                            ->where('created_at', '>=', $request->date_from)
                        ->where('created_at', '<=', $request->date_to)
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        } elseif( $request->expand ) {
            return new SabonnerResource(Sabonner::findOrFail($request->expand));
        }
        else {
            $sabonner = Sabonner::Orderby( $request->sortby , $request->orderby )
                            ->orWhere( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        }
        
        
        
        return SabonnerResource::collection($sabonner);
    }
    
    // store
    public function store(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation

        $sabonner = new Sabonner;
        $sabonner->email = $request->email;

        
        $sabonner->save();

        return 'created';
    }

    //update
    public function update(Request $request, $id)
    {
        $sabonner = Sabonner::find($id);
        $sabonner->email = $request->email;

        
        $sabonner->save();

        return 'update';
        
    }

    // trashed
    public function trashed() {
        $sabonner = Sabonner::onlyTrashed()->get();
        return SabonnerResource::collection($sabonner);
    }

    // delete
    public function destroy($id) {
        $sabonner = Sabonner::withTrashed()->where('id', $id);
        $sabonner->delete();
        return 'delete';
    }

    // restore
    public function restore($id) {
        $sabonner = Sabonner::onlyTrashed()
        ->where('id', $id);
        $sabonner->restore();
        return 'restore';
    }

    // forced
    public function forced($id) {
        $sabonner = Sabonner::onlyTrashed()
        ->where('id', $id);
        $sabonner->forceDelete();
        return 'forced';
    }
}
