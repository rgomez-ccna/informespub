<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableroController extends Controller
{
     public function index()
    {
        return view('tablero.index');
    }
}
