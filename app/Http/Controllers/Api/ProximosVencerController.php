<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProximaVencer\ProximaVencerResource;
use App\Models\Cuotas;
use Illuminate\Http\Request;

class ProximosVencerController extends Controller
{
    public function index(Request $request)
    {
        $query = Cuotas::proximasAVencer()
            ->with(['prestamo.cliente'])
            ->orderBy('fecha_inicio', 'asc');

        if ($request->filled('cliente')) {
            $query->whereHas('prestamo.cliente', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->cliente . '%');
            });
        }

        return ProximaVencerResource::collection(
            $query->paginate(20)
        );
    }
}
