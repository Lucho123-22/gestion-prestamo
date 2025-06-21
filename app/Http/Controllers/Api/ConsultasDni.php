<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

class ConsultasDni extends Controller{
    public function consultar($dni = null){
        if (empty($dni)) {
            return response()->json(['error' => 'Debe proporcionar un DNI válido'], 400);
        }

        $token = 'apis-token-16163.Sk38iXNP1s4r28QRRs5BbWzN1uA62Z5J';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
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
    }
    /*public function consultar(){
        $token = 'apis-token-16163.Sk38iXNP1s4r28QRRs5BbWzN1uA62Z5J';
        $dni = '76393671';

        // Iniciar llamada a API
        $curl = curl_init();

        // Buscar dni
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 2,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Referer: https://apis.net.pe/consulta-dni-api',
            'Authorization: Bearer ' . $token
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // Datos listos para usar
        $persona = json_decode($response);
        var_dump($persona);
    }*/
}
