<?php
namespace Tramasec\EmisionVehiculos;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Throwable;

/**
 * Class EmitirPoliza
 * @package Tramasec\EmisionVehiculos
 */
class EmitirPoliza
{
    /**
     * @var string
     */
    private $url;

    /**
     * EmitirPoliza constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @param array $data
     * @return ResponseE2
     */
    public function send(array $data)
    {
        $start_time = microtime(true);
        $result = new ResponseE2();

        $estructura = new EstructuraEmision();

        if (!$estructura->validate($data)) {
            $result->errors = $estructura->errors;
            $result->retry = false;
            $result->errorMessage = 'Error en estructura de información';

            return $result;
        }

        $client = new Client([
            'base_uri' => $this->url,
            'timeout'  => 60.0, //timeout después de 60 segundos
            'force_ip_resolve' => 'v4'
        ]);

        try {
            $response = $client->post('generaPoliza', [ 'json' => $data ]);
            $end_time = microtime(true);
            $data = json_decode($response->getBody()->getContents());

            if ($data->sn_error === '0') {
                $result->error = false;
                $result->errorCode = $data->sn_error;
                $result->errorMessage = empty($data->txt_mensaje) ? 'Póliza generada' : $data->txt_mensaje;
                $result->response = $data;

                $result->codigo_asegurado = $data->cod_aseg;
                $result->numero_operacion = $data->numero_operacion;
                $result->numero_poliza = $data->numero_poliza;
                $result->numero_endoso = $data->numero_endoso;
                $result->idpv = $data->id_pv;
                $result->fecha_emision = $data->fecha_emision;
                $result->codigo_pagador = $data->cod_pagador;
                $result->numero_factura = $data->numero_factura;
                $result->elapsed = $end_time - $start_time;
            } else {
                $result->error = true;
                $result->errorCode = $data->sn_error;
                $result->errorMessage = trim($data->txt_mensaje);
                $result->response = $data;
                $result->elapsed = $end_time - $start_time;

                if ($result->errorMessage === 'Poliza ya generada') {
                    $result->error = false;
                    $result->retry = false;

                    $result->codigo_asegurado = $data->cod_aseg;
                    $result->numero_operacion = $data->numero_operacion;
                    $result->numero_poliza = $data->numero_poliza;
                    $result->numero_endoso = $data->numero_endoso;
                    $result->idpv = $data->id_pv;
                    $result->fecha_emision = $data->fecha_emision;
                    $result->codigo_pagador = $data->cod_pagador;
                } else {
                    $result->retry = true;
                }
            }

            return $result;
            //}
        } catch (ConnectException $e) {
            $err = (object) $e->getHandlerContext();
            $end_time = microtime(true);

            $result->error = true;
            $result->errorCode = $err->errno;
            $result->errorMessage = $err->error;
            $result->response = [];
            $result->retry = true;
            $result->elapsed = $end_time - $start_time;

            return $result;
        } catch (Throwable $e) {
            $end_time = microtime(true);

            $result->error = true;
            $result->errorCode = $e->getCode();
            $result->errorMessage = $e->getMessage();
            $result->response = [];
            $result->retry = true;
            $result->elapsed = $end_time - $start_time;

            return $result;
        }
    }
}
