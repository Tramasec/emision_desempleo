<?php
namespace Tramasec\EmisionVehiculosSesa;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use \Throwable;

/**
 * Class IngresoInformacion
 * @package Tramasec\EmisionVehiculosSesa
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
     * @var
     */
    private $logs;

    /**
     * IngresoInformacion constructor.
     * @param string $url
     * @param bool $logs
     */
    public function __construct(string $url, bool $logs = false)
    {
        $this->url = $url;
        $this->logs = $logs;
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

        $logger = new Logger('vehiculos_ingreso_info');
        $logger->pushHandler(new StreamHandler(__DIR__.'/emision_vehiculos.log', Logger::DEBUG));
        $logger->pushHandler(new FirePHPHandler());

        //Primero verificar si el arreglo cumple con los parámetros básicos
        $estructura = new Estructura();

        //Si es válida enviamos la información al servicio web
        if ($estructura->validate($data)) {
            $client = new Client([
                'base_uri' => $this->url,
                'timeout'  => 20.0, //timeout después de 20 segundos
            ]);

            try {
                $response = $client->post('ingresoInfo', [ 'json' => $data ]);
                $end_time = microtime(true);
                if ($this->logs) {
                    $logger->info('Finaliza ingreso de info', ['elapsed' => $end_time - $start_time]);
                }
                $data = json_decode($response->getBody()->getContents());

                if ($data->sn_error === '0') {
                    $result->error = false;
                    $result->errorCode = $data->sn_error;
                    $result->errorMessage = empty($data->txt_mensaje) ? 'Información ingresada' : $data->txt_mensaje;
                    $result->response = $data;
                    $result->proceso = $data->proceso;

                    if ($this->logs) {
                        $logger->info('Respuesta de ingreso de info', [
                            'proceso' => $data->proceso,
                            'message' => $data->txt_mensaje
                        ]);
                    }
                } else {
                    $result->error = true;
                    $result->errorCode = $data->sn_error;
                    $result->errorMessage = trim($data->txt_mensaje);
                    $result->response = $data;

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
}
