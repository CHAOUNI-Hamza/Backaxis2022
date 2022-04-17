<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Service;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ServiceResource;

class FrontController extends Controller
{
    public function axis() {}
    public function clients() {
        $clients = Client::all();
        return ClientResource::collection($clients);
    }
    public function produits() {}
    public function services() {
        $services = Service::all();
        return ServiceResource::collection($services);
    }
}
