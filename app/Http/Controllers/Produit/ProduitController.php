<?php

namespace App\Http\Controllers\Produit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produit;
use App\Http\Resources\ProduitResource;
use Illuminate\Support\Facades\Storage;
use Validator;

class ProduitController extends Controller
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
        //$Produit = json_decode($request->filter);
        /*return $produit->emaile;*/
        //return $produit[1];

        /*if( $request->created_at ) {
            $produit = Produit::Orderby( $request->sortby , $request->orderby )->whereDate( 'created_at', $request->created_at )->paginate($request->paginate);
        }

        elseif( $request->updated_at ) {
            $produit = Produit::Orderby( $request->sortby , $request->orderby )->whereDate( 'updated_at', $request->updated_at )->paginate($request->paginate);
        }

        elseif( $request->date_from && $request->date_to ) {
            $produit = Produit::Orderby( $request->sortby , $request->orderby )->whereDate('created_at', [$request->date_from, $request->date_to])->paginate($request->paginate);
        }

        elseif( $request->expand ) {
            return new ProduitResource(Produit::findOrFail($request->expand));
        }
        elseif( $request->filter ) {
            $produit = Produit::Orderby( $request->sortby , $request->orderby )->where( $request->filter, 'LIKE', "%$request->filtervalue%" )->get();
        } else {
            $produit = Produit::Orderby( $request->sortby , $request->orderby )->paginate($request->paginate);
        }*/
        if( $request->created_at ) {
            $produit = Produit::Orderby( $request->sortby , $request->orderby )
                            ->whereDate( 'created_at', $request->created_at )
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        }
        elseif( $request->updated_at ) {
            $produit = Produit::Orderby( $request->sortby , $request->orderby )
                            ->whereDate( 'updated_at', $request->updated_at )
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        }
        elseif( $request->date_from && $request->date_to ) {
            $produit = Produit::Orderby( $request->sortby , $request->orderby )
                            //->whereBetween('created_at', [$request->date_from, $request->date_to])
                            ->where('created_at', '>=', $request->date_from)
                        ->where('created_at', '<=', $request->date_to)
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        } elseif( $request->expand ) {
            return new ProduitResource(Produit::findOrFail($request->expand));
        }
        else {
            $produit = Produit::Orderby( $request->sortby , $request->orderby )
                            ->orWhere( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        }
        
        
        
        return ProduitResource::collection($produit);
    }
    
    // store
    public function store(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'social' => 'required',
            'service' => 'required',
            'photo' => 'required|mimes:jpg,bmp,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation

        $produit = new Produit;
        $produit->title = $request->title;
        $produit->description = $request->description;
        $produit->social = $request->social;
        $produit->service = $request->service;


        $hasFile = $request->hasFile('photo');
        
        if( $hasFile ) {
            $path = $request->file('photo')->store('public/clients');  
            $produit->photo = Storage::url($path);
        }

        
        $produit->save();

        return 'created';
    }

    //update
    public function update(Request $request, $id)
    {
        // validation
       /* $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'social' => 'required',
            'service' => 'required',
            'photo' => 'required|mimes:jpg,bmp,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation

        $produit = new Produit;
        $produit->title = $request->title;
        $produit->description = $request->description;
        $produit->social = $request->social;
        $produit->service = $request->service;

        
        $hasFile = $request->hasFile('photo');
        
        if( $hasFile ) {
            $path = $request->file('photo')->store('public/clients');  
            $produit->photo = Storage::url($path);
        }

        
        $produit->save();

        return 'update';*/
        $produit = Produit::find($id);
        $produit->title = $request->title;
        $produit->description = $request->description;
        $produit->social = $request->social;
        $produit->service = $request->service;



        $hasFileLogo = $request->hasFile('photo');
        
        if( $hasFileLogo ) {
            $path = $request->file('photo')->store('public/clients');  
            $produit->photo = Storage::url($path);
        }else{
            unset($request['photo']);
        }
        
        $produit->save();

        return 'update';
        
    }

    // trashed
    public function trashed() {
        $produit = Produit::onlyTrashed()->get();
        return ProduitResource::collection($produit);
    }

    // delete
    public function destroy($id) {
        $produit = Produit::withTrashed()->where('id', $id);
        $produit->delete();
        return 'delete';
    }

    // restore
    public function restore($id) {
        $produit = Produit::onlyTrashed()
        ->where('id', $id);
        $produit->restore();
        return 'restore';
    }

    // forced
    public function forced($id) {
        $produit = Produit::onlyTrashed()
        ->where('id', $id);
        $produit->forceDelete();
        return 'forced';
    }
}
