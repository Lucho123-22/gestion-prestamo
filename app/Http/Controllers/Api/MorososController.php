<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Moroso\MorosoResource;
use App\Models\Cuotas;
use Illuminate\Http\Request;

class MorososController extends Controller
{
    public function index(Request $request)
    {
        $query = Cuotas::morosas()
            ->with(['prestamo.cliente'])
            ->orderBy('fecha_inicio', 'asc');

        if ($request->filled('cliente')) {
            $query->whereHas('prestamo.cliente', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->cliente . '%');
            });
        }

        return MorosoResource::collection($query->get());
    }
}