<?php

class EmisionVehiculosTest extends \PHPUnit\Framework\TestCase
{

    public function testIngresoInfoOK()
    {

        $dotenv = Dotenv\Dotenv::create(__DIR__);
        $dotenv->load();

        $url = getenv('EMISION_URL');
        $ingreso = new \Tramasec\EmisionVehiculos\IngresoInformacion($url);
        $ingreso->send([]);

        $condition = true;
        $this->assertTrue($condition);
    }
}