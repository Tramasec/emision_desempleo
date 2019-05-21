<?php
namespace Tramasec\EmisionVehiculosSesa;

/**
 * Class ResponseE2
 * @package Tramasec\EmisionVehiculosSesa
 */
class ResponseE2
{
    public $error = null;
    public $errorCode = null;
    public $errorMessage = null;
    public $response = null;
    public $retry = false;

    public $idpv = null;
    public $numero_factura = null;
    public $numero_poliza = null;
    public $numero_endoso = null;
    public $numero_operacion = null;
    public $fecha_emision = null;
    public $codigo_asegurado = null;
    public $codigo_pagador = null;
}
