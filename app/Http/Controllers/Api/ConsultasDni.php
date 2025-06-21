<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ConsultasDni extends Controller{
    /*public function consultar($dni = null){
        if (empty($dni)) {
            return response()->json(['error' => 'Debe proporcionar un DNI válido'], 400);
        }
        $token = '7384|Suf8VcDn6ysyvz194pk4mKEmeidGBWcaNrlVgRJF';
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://apis.aqpfact.pe/api/dni/' . $dni,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Referer: https://apis.net.pe/consulta-dni-api',
                'Authorization: Bearer ' . $token,
            ],
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $persona = json_decode($response);
        if (!$persona) {
            return response()->json(['error' => 'No se encontraron datos para el DNI proporcionado'], 404);
        }
        return response()->json($persona);
    }*/
    public function consultar($dni){
        if (!preg_match('/^\d{8}$/', $dni)) {
            return response()->json([
                'error' => 'DNI inválido. Debe tener 8 dígitos.'
            ], 422);
        }
        $token = env('API_RENIEC_TOKEN');
        $response = Http::withHeaders([
            'Referer' => 'https://apis.net.pe/consulta-dni-api',
            'Authorization' => 'Bearer ' . $token,
        ])->get('https://api.apis.net.pe/v2/reniec/dni', [
            'numero' => $dni
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json([
                'error' => 'No se pudo obtener información del DNI.'
            ], $response->status());
        }
    }
}
