<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Http\Resources\CompanyResource;
use Illuminate\Support\Facades\Storage;
use Validator;

class CompanyController extends Controller
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
        if( $request->expand ) {
            return new CompanyResource(Company::findOrFail($request->expand));
        } else {
            $company = Company::first();        
            return new CompanyResource($company);
        }


        
        
        
        
    }
    
    // store
    public function store(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'social' => 'required',
            'description_agency' => 'required',
            'address' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'localisation' => 'required',
            'logo' => 'required|mimes:jpg,bmp,png',
            'photo_carousel' => 'required|mimes:jpg,bmp,png',
            'photo_agency' => 'required|mimes:jpg,bmp,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation

        $company = new Company;
        $company->description_agency = $request->description_agency;
        $company->address = $request->address;
        $company->email = $request->email;
        $company->phone = $request->phone;
        $company->localisation = $request->localisation;
        $company->social = $request->social;


        $hasFileLogo = $request->hasFile('logo');
        $hasFilePhotoCarousel = $request->hasFile('photo_carousel');
        $hasFilePhotoAgency = $request->hasFile('photo_agency');
        
        if( $hasFileLogo ) {
            $path = $request->file('logo')->store('public/clients');  
            $company->logo = Storage::url($path);
        }
        if( $hasFilePhotoCarousel ) {
            $path = $request->file('photo_carousel')->store('public/clients');  
            $company->photo_carousel = Storage::url($path);
        }
        if( $hasFilePhotoAgency ) {
            $path = $request->file('photo_agency')->store('public/clients');  
            $company->photo_agency = Storage::url($path);
        }
        
        $company->save();

        return 'created';
    }

    //update
    public function update(Request $request, $id) {

        $company = Company::find($id);
        $company->description_agency = $request->description_agency;
        $company->address = $request->address;
        $company->email = $request->email;
        $company->phone = $request->phone;
        $company->localisation = $request->localisation;
        $company->social = $request->social;


        $hasFileLogo = $request->hasFile('logo');
        $hasFilePhotoCarousel = $request->hasFile('photo_carousel');
        $hasFilePhotoAgency = $request->hasFile('photo_agency');
        
        if( $hasFileLogo ) {
            $path = $request->file('logo')->store('public/clients');  
            $company->logo = Storage::url($path);
        }else{
            unset($request['logo']);
        }
        if( $hasFilePhotoCarousel ) {
            $path = $request->file('photo_carousel')->store('public/clients');  
            $company->photo_carousel = Storage::url($path);
        }else{
            unset($request['photo_carousel']);
        }
        if( $hasFilePhotoAgency ) {
            $path = $request->file('photo_agency')->store('public/clients');  
            $company->photo_agency = Storage::url($path);
        }else{
            unset($request['photo_agency']);
        }
        
        $company->save();

        return 'update';
        
    }

    // trashed
    public function trashed(Request $request) {
        $company = Company::onlyTrashed()->get();
        /*if( $request->created_at ) {
            $company = Company::withTrashed()
            ->orderby( $request->sortby , $request->orderby )
                            ->whereDate( 'created_at', $request->created_at )
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        }
        elseif( $request->updated_at ) {
            $company = Company::withTrashed()
            ->orderby( $request->sortby , $request->orderby )
                            ->whereDate( 'updated_at', $request->updated_at )
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        }
        elseif( $request->date_from && $request->date_to ) {
            $company = Company::withTrashed()
            ->orderby( $request->sortby , $request->orderby )
                            //->whereBetween('created_at', [$request->date_from, $request->date_to])
                            ->where('created_at', '>=', $request->date_from)
                        ->where('created_at', '<=', $request->date_to)
                            ->where( $request->filter, 'LIKE', "%$request->filtervalue%" )
                            ->paginate($request->paginate);
        } elseif( $request->expand ) {
            return new CompanyResource(Company::withTrashed()->findOrFail($request->expand));
        }
        else {
            $company = Company::onlyTrashed()/*->orderby( $request->sortby , $request->orderby )
                            ->orWhere( $request->filter, 'LIKE', "%$request->filtervalue%" )*/
                            //->paginate($request->paginate);
        //}
        return CompanyResource::collection($company);
    }

    // delete
    public function destroy($id) {
       $company = Company::withTrashed()->where('id', $id);
        $company->delete();
        return 'delete';

        /*$single_user_id = explode(',' , $id);

       foreach($single_user_id as $id) {
           \App\User::findOrFail($id)->delete();
       }*/
    }

    // restore
    public function restore($id) {
        $company = Company::onlyTrashed()
        ->where('id', $id);
        $company->restore();
        return 'restore';
    }

    // forced
    public function forced($id) {
        $company = Company::onlyTrashed()
        ->where('id', $id);
        $company->forceDelete();
        return 'forced';
    }
}
