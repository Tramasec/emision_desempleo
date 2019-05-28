<?php
namespace Tramasec\EmisionDesempleo;

/**
 * Class Response
 * @package Tramasec\EmisionVehiculos
 */
class Response
{
    public $error = null;
    public $errorCode = null;
    public $errorMessage = null;
    public $response = null;
    public $proceso = null;
    public $retry = false;
    public $errors = [];
    public $elapsed = null;
}
