<?php

namespace App\Http\Controllers;

use App\Models\Skate;
use App\Models\SkateModel;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $skateModels = SkateModel::with('skates')->get();
        return view('home', compact('skateModels'));
    }
}