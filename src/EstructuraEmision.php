<?php
namespace Tramasec\EmisionDesempleo;

use Rakit\Validation\Validator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Class EstructuraEmision
 * @package Tramasec\EmisionVehiculos
 */
class EstructuraEmision
{
    public $errors;
    private $validations;

    public function __construct()
    {
        $this->validations = [
            'ID_PROCESO'        => 'required|numeric',
            'COD_USUARIO'       => 'required'
        ];
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array $data)
    {
        $validate = new Validator;
        $response = $validate->validate($data, $this->validations);

        try{
            $this->errors = $response->errors()->toArray();
        }catch (\Throwable $e){
            $this->errors = [];
        }

        return $response->errors()->count() == 0 ? true : false;
    }
}
