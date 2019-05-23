<?php

class EmisionVehiculosTest extends \PHPUnit\Framework\TestCase
{
    public function testIngresoInfoOKDup()
    {
        $dotenv = Dotenv\Dotenv::create(__DIR__);
        $dotenv->load();

        $url = getenv('EMISION_URL');
        $ingreso = new \Tramasec\EmisionVehiculos\IngresoInformacion($url);
        $dato = (array) json_decode(getenv('ESTRUCTURA_INGRESO'));
        $faker = Faker\Factory::create();

        $dato['chasis'] = $faker->text(45);

        $response = $ingreso->send($dato);

        $this->assertFalse($response->error);
        $this->assertEquals($response->errorCode, 0);
        $this->assertEquals($response->errorMessage, "Información ingresada");
        $this->assertFalse($response->retry);
        $this->assertGreaterThan(1, $response->proceso);

        //Emitir
        $emitir = new \Tramasec\EmisionVehiculos\EmitirPoliza($url);
        $response_temp = $response;

        $response = $emitir->send([
            'id_proceso'        => $response->proceso,
            'id_certificado'    => $dato['certificado'],
            'cod_usuario'       => $dato['cod_usuario'],
        ]);

        $this->assertFalse($response->error);
        $this->assertEquals($response->errorCode, 0);
        $this->assertEquals($response->errorMessage, "Se generó correctamente la poliza");
        $this->assertFalse($response->retry);
        $this->assertGreaterThan(1, $response->idpv);
        $this->assertGreaterThan(1, $response->numero_poliza);
        $this->assertGreaterThan(1, $response->codigo_asegurado);
        $this->assertGreaterThan(1, $response->codigo_pagador);

        //POLIZA YA GENERADA

        $response = $emitir->send([
            'id_proceso'        => $response_temp->proceso,
            'id_certificado'    => $dato['certificado'],
            'cod_usuario'       => $dato['cod_usuario'],
        ]);

        $this->assertFalse($response->error);
        $this->assertEquals($response->errorCode, -1);
        $this->assertEquals($response->errorMessage, "Poliza ya generada");
        $this->assertFalse($response->retry);
        $this->assertGreaterThan(1, $response->idpv);
        //TODO AGREGAR IDPV Y DEMÁS CODIGOS

        //CHASIS DUPLICADO
        $response = $ingreso->send($dato);

        $this->assertTrue($response->error);
        $this->assertEquals($response->errorCode, -1);
        $this->assertEquals($response->errorMessage, "Chasis Duplicado");
        $this->assertFalse($response->retry);
    }
}
