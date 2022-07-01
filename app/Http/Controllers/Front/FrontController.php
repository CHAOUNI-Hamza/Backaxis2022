<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Service;
use App\Models\Produit;
use App\Models\Company;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ProduitResource;
use App\Http\Resources\CompanyResource;

class FrontController extends Controller
{
    public function axis() {
        $axis = Company::first();
        return new CompanyResource($axis);
    }
    public function clients() {
        $clients = Client::limit(5)->get();
        return ClientResource::collection($clients);
    }
    public function produits() {

        $produits = Produit::with('service')->get();
        return ProduitResource::collection($produits);

    }
    public function services() {
        $services = Service::all();
        return ServiceResource::collection($services);
        //return new ServiceResource($services);
    }
}
