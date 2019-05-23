<?php
namespace Tramasec\EmisionVehiculos;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use \Throwable;

/**
 * Class IngresoInformacion
 * @package Tramasec\EmisionVehiculos
 *
 * Acceso al servicio web para ingreso de información previa a la creación de una póliza
 *
 */
class IngresoInformacion
{
    /**
     * @var
     * Variable que determina la url del endpoint para el ingreso de información
     */
    private $url;

    /**
     * IngresoInformacion constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @param array $data
     * @return Response
     * @throws \Exception
     */
    public function send(array $data)
    {
        $start_time = microtime(true);
        $result = new Response();

        //Primero verificar si el arreglo cumple con los parámetros básicos
        $estructura = new Estructura();

        //Si es válida enviamos la información al servicio web
        if ($estructura->validate($data)) {
            $client = new Client([
                'base_uri' => $this->url,
                'timeout'  => 20.0, //timeout después de 20 segundos
                'force_ip_resolve' => 'v4'
            ]);

            try {
                $response = $client->post('ingresoInfo', [ 'json' => $data ]);
                $end_time = microtime(true);
                $data = json_decode($response->getBody()->getContents());

                if ($data->sn_error === '0') {
                    $result->error = false;
                    $result->errorCode = $data->sn_error;
                    $result->errorMessage = empty($data->txt_mensaje) ? 'Información ingresada' : $data->txt_mensaje;
                    $result->response = $data;
                    $result->proceso = $data->proceso;
                    $result->elapsed = $end_time - $start_time;
                } else {
                    $result->error = true;
                    $result->errorCode = $data->sn_error;
                    $result->errorMessage = trim($data->txt_mensaje);
                    $result->response = $data;
                    $result->elapsed = $end_time - $start_time;
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
        } else {
            $result->errors = $estructura->errors;
            $result->errorMessage = 'Estructura no válida';
            return $result;
        }
    }
}
