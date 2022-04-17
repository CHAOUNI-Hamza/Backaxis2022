<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Http\Resources\ClientResource;
use Illuminate\Support\Facades\Storage;
use Validator;

class ClientController extends Controller
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
        if( $request->created_at ) {
            $client = Client::Orderby( $request->sortby , $request->orderby )
                            ->whereDate( 'created_at', $request->created_at )
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        }
        elseif( $request->updated_at ) {
            $client = Client::Orderby( $request->sortby , $request->orderby )
                            ->whereDate( 'updated_at', $request->updated_at )
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        }
        elseif( $request->date_from && $request->date_to ) {
            $client = Client::Orderby( $request->sortby , $request->orderby )
                            //->whereBetween('created_at', [$request->date_from, $request->date_to])
                            ->where('created_at', '>=', $request->date_from)
                        ->where('created_at', '<=', $request->date_to)
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        } elseif( $request->expand ) {
            return new ClientResource(Client::findOrFail($request->expand));
        }
        else {
            $client = Client::Orderby( $request->sortby , $request->orderby )
                            ->orWhere( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
                            
        //$client = json_decode($request->filter);
        /*return $client->emaile;*/
        //return $client[1];

        /*if( $request->created_at ) {
            $client = Client::Orderby( $request->sortby , $request->orderby )->whereDate( 'created_at', $request->created_at )->paginate($request->paginate);
        }

        elseif( $request->updated_at ) {
            $client = Client::Orderby( $request->sortby , $request->orderby )->whereDate( 'updated_at', $request->updated_at )->paginate($request->paginate);
        }

        elseif( $request->date_from && $request->date_to ) {
            $client = Client::Orderby( $request->sortby , $request->orderby )->whereDate('created_at', [$request->date_from, $request->date_to])->paginate($request->paginate);
        }

        elseif( $request->expand ) {
            return new ClientResource(Client::findOrFail($request->expand));
        }
        elseif( $request->filter ) {
            $client = Client::Orderby( $request->sortby , $request->orderby )->where( $request->filter, 'LIKE', "%$request->filtervalue%" )->get();
        } else {
            $client = Client::Orderby( $request->sortby , $request->orderby )->paginate($request->paginate);*/
        }
        
        
        
        return ClientResource::collection($client);
    }
    
    // store
    public function store(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:20',
            'photo' => 'required|mimes:jpg,bmp,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation
        
        $client = new Client;
        $client->name = $request->name;
        $hasFile = $request->hasFile('photo');
        
        if( $hasFile ) {
            $path = $request->file('photo')->store('public/clients');  
            $client->photo = Storage::url($path);
        }
        
        $client->save();

        return 'created';
    }

    //update
    public function update(Request $request, $id) {
        // validation
        /*$validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:20',
            'photo' => 'required|mimes:jpg,bmp,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }*/
        // end validation

        $client = Client::find($id);
        $client->name = $request->name;



        $hasFileLogo = $request->hasFile('photo');
        
        if( $hasFileLogo ) {
            $path = $request->file('photo')->store('public/clients');  
            $client->photo = Storage::url($path);
        }else{
            unset($request['photo']);
        }
        
        $client->save();

        return 'update';
        ////////////////////

        /*$client = new Client;
        $client->name = $request->name;
        $hasFile = $request->hasFile('photo');
        
        if( $hasFile ) {
            $path = $request->file('photo')->store('public/clients');  
            $client->photo = Storage::url($path);
        }
        
        $client->save();

        return 'update';*/
        
    }

    // trashed
    public function trashed() {
        $clients = Client::onlyTrashed()->get();
        return ClientResource::collection($clients);
    }

    // delete
    public function destroy($id) {
        $client = Client::withTrashed()->where('id', $id);
        $client->delete();
        return 'delete';
    }

    // restore
    public function restore($id) {
        $client = Client::onlyTrashed()
        ->where('id', $id);
        $client->restore();
        return 'restore';
    }

    // forced
    public function forced($id) {
        $client = Client::onlyTrashed()
        ->where('id', $id);
        $client->forceDelete();
        return 'forced';
    }
}
