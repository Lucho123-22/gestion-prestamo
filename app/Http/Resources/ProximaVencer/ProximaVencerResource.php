<?php

namespace App\Http\Resources\ProximaVencer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ProximaVencerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $diasTranscurridos = Carbon::parse($this->fecha_inicio)
            ->diffInDays(now());

        $diasRestantes = 30 - $diasTranscurridos;

        return [
            'prestamo_id' => $this->prestamo->id,
            'prestamo_referencia' => $this->prestamo->referencia,

            'cliente_id' => $this->prestamo->cliente->id ?? null,
            'cliente_nombre' => $this->prestamo->cliente->nombre ?? null,
            'cliente_telefono' => $this->prestamo->cliente->telefono ?? null,

            'cuota_id' => $this->id,
            'numero_cuota' => $this->numero_cuota,

            'saldo_capital' => (float) $this->saldo_capital,
            'interes_pendiente' => (float) $this->monto_interes_pagar,
            'total_deuda' => (float) ($this->saldo_capital + $this->monto_interes_pagar),

            'dias_transcurridos' => $diasTranscurridos,
            'dias_para_vencer' => $diasRestantes,

            'fecha_inicio' => $this->fecha_inicio,
            'estado' => $this->estado,
        ];
    }
}
