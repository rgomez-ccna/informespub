<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importa el facade DB para consultas
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Verificar si el usuario es un vendedor y redirigir
        if (Auth::user()->role === 'vendedor') {
            return redirect()->route('ventas.create');
        }
    
        // Devuelve la vista con los datos obtenidos
        return view('home');  
      }
    

    
  
    
}
