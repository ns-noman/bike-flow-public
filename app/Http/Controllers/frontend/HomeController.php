<?php

namespace App\Http\Controllers\frontend;

use App\Models\FrontendMenu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    public function index()
    {
        return view("");
    }
}