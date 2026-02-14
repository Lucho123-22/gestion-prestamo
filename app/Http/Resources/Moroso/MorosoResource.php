<?php

namespace App\Http\Resources\Moroso;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class MorosoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $diasVencidos = Carbon::parse($this->fecha_inicio)
            ->diffInDays(now());

        return [
            'prestamo_id' => $this->prestamo->id,
            'referencia_prestamo' => $this->prestamo->referencia,
            'cliente' => [
                'id' => $this->prestamo->cliente->id ?? null,
                'nombre' => $this->prestamo->cliente->nombre ?? null,
                'telefono' => $this->prestamo->cliente->telefono ?? null,
            ],
            'cuota_id' => $this->id,
            'numero_cuota' => $this->numero_cuota,
            'capital' => $this->capital,
            'saldo_capital' => $this->saldo_capital,
            'monto_interes_pagar' => $this->monto_interes_pagar,
            'total_deuda' => $this->saldo_capital + $this->monto_interes_pagar,
            'dias_vencidos' => $diasVencidos,
            'fecha_inicio' => $this->fecha_inicio,
            'estado' => $this->estado,
        ];
    }
}
