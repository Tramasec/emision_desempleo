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

        $logger = new Logger('vehiculos_ingreso_info');
        $logger->pushHandler(new StreamHandler(__DIR__.'/emision_vehiculos.log', Logger::DEBUG));
        $logger->pushHandler(new FirePHPHandler());

        $client = new Client([
            'base_uri' => $this->url,
            'timeout'  => 20.0, //timeout después de 20 segundos
        ]);

        try {
            $response = $client->post('emitePoliza', [ 'json' => $data ]);
            $end_time = microtime(true);
            if ($this->logs) {
                $logger->info('Finaliza emisión de póliza', ['elapsed' => $end_time - $start_time]);
            }
            $data = json_decode($response->getBody()->getContents());

            if ($data->errorCode !== -1) {

            } else {

            }

            return $result;
            //}
        } catch (ConnectException $e) {
            $end_time = microtime(true);

            if ($this->logs) {
                $logger->error('Timeout', ['elapsed' => $end_time - $start_time]);
            }
            $result->error = true;
            $result->errorCode = $e->getCode();
            $result->errorMessage = $e->getMessage();
            $result->response = [];

            return $result;
        } catch (Throwable $e) {
            $end_time = microtime(true);

            if ($this->logs) {
                $logger->error('Error', ['elapsed' => $end_time - $start_time]);
            }

            $result->error = true;
            $result->errorCode = $e->getCode();
            $result->errorMessage = $e->getMessage();
            $result->response = [];

            return $result;
        }
    }
}
