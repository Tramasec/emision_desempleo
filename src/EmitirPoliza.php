<?php
namespace Tramasec\EmisionVehiculosSesa;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Throwable;

/**
 * Class EmitirPoliza
 * @package Tramasec\EmisionVehiculosSesa
 */
class EmitirPoliza
{
    private $url;
    private $logs;

    /**
     * EmitirPoliza constructor.
     * @param string $url
     * @param bool $logs
     */
    public function __construct(string $url, bool $logs = false)
    {
        $this->url = $url;
        $this->logs = $logs;
    }

    public function send(array $data)
    {
        $start_time = microtime(true);
        $result = new ResponseE2();

        $logger = new Logger('vehiculos_generar_poliza');
        $logger->pushHandler(new StreamHandler(__DIR__.'/emision_vehiculos.log', Logger::DEBUG));
        $logger->pushHandler(new FirePHPHandler());

        $client = new Client([
            'base_uri' => $this->url,
            'timeout'  => 60.0, //timeout después de 60 segundos
        ]);

        try {
            $response = $client->post('generaPoliza', [ 'json' => $data ]);
            $end_time = microtime(true);

            if ($this->logs) {
                $logger->info('Finaliza emisión de póliza', ['elapsed' => $end_time - $start_time]);
            }
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

                if ($this->logs) {
                    $logger->info('Respuesta de generación de póliza', [
                        'message' => $data->txt_mensaje
                    ]);
                }
            } else {
                $result->error = true;
                $result->errorCode = $data->sn_error;
                $result->errorMessage = trim($data->txt_mensaje);
                $result->response = $data;

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


                if ($this->logs) {
                    $logger->error('Error al ingresar la info', [
                        'proceso' => $data->proceso,
                        'message' => $data->txt_mensaje
                    ]);
                }
            }

            return $result;
            //}
        } catch (ConnectException $e) {
            $err = (object) $e->getHandlerContext();
            $end_time = microtime(true);

            if ($this->logs) {
                $logger->error($err->error, ['elapsed' => $end_time - $start_time]);
            }

            $result->error = true;
            $result->errorCode = $err->errno;
            $result->errorMessage = $err->error;
            $result->response = [];
            $result->retry = true;

            return $result;
        } catch (Throwable $e) {
            $end_time = microtime(true);
            $err = (object) $e->getHandlerContext();

            if ($this->logs) {
                $logger->error($err->error, ['elapsed' => $end_time - $start_time]);
            }

            $result->error = true;
            $result->errorCode = $e->getCode();
            $result->errorMessage = $e->getMessage();
            $result->response = [];
            $result->retry = true;

            return $result;
        }
    }
}
