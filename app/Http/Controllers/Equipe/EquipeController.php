<?php

namespace App\Http\Controllers\Equipe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EquipeController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
