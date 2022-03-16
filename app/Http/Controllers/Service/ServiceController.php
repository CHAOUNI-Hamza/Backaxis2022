<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\Storage;
use Validator;

class ServiceController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }
    
    // index
    public function index(Request $request)
    {
        //$Service = json_decode($request->filter);
        /*return $service->emaile;*/
        //return $service[1];

        if( $request->created_at ) {
            $service = Service::Orderby( $request->sortby , $request->orderby )->whereDate( 'created_at', $request->created_at )->paginate($request->paginate);
        }

        elseif( $request->updated_at ) {
            $service = Service::Orderby( $request->sortby , $request->orderby )->whereDate( 'updated_at', $request->updated_at )->paginate($request->paginate);
        }

        elseif( $request->date_from && $request->date_to ) {
            $service = Service::Orderby( $request->sortby , $request->orderby )->whereDate('created_at', [$request->date_from, $request->date_to])->paginate($request->paginate);
        }

        elseif( $request->expand ) {
            return new ServiceResource(Service::findOrFail($request->expand));
        }
        elseif( $request->filter ) {
            $service = Service::Orderby( $request->sortby , $request->orderby )->where( $request->filter, 'LIKE', "%$request->filtervalue%" )->get();
        } else {
            $service = Service::Orderby( $request->sortby , $request->orderby )->paginate($request->paginate);
        }
        
        
        
        return ServiceResource::collection($service);
    }
    
    // store
    public function store(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'photo' => 'required|mimes:jpg,bmp,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation

        $service = new Service;
        $service->title = $request->title;


        $hasFile = $request->hasFile('photo');
        
        if( $hasFile ) {
            $path = $request->file('photo')->store('public/clients');  
            $service->photo = Storage::url($path);
        }

        
        $service->save();

        return 'created';
    }

    //update
    public function update(Request $request, $id)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'photo' => 'required|mimes:jpg,bmp,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation

        $service = new Service;
        $service->title = $request->title;

        
        $hasFile = $request->hasFile('photo');
        
        if( $hasFile ) {
            $path = $request->file('photo')->store('public/clients');  
            $service->photo = Storage::url($path);
        }

        
        $service->save();

        return 'update';
        
    }

    // trashed
    public function trashed() {
        $service = Service::onlyTrashed()->get();
        return ServiceResource::collection($service);
    }

    // delete
    public function destroy($id) {
        $service = Service::withTrashed()->where('id', $id);
        $service->delete();
        return 'delete';
    }

    // restore
    public function restore($id) {
        $service = Service::onlyTrashed()
        ->where('id', $id);
        $service->restore();
        return 'restore';
    }

    // forced
    public function forced($id) {
        $service = Service::onlyTrashed()
        ->where('id', $id);
        $service->forceDelete();
        return 'forced';
    }
}
