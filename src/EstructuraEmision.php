<?php
namespace Tramasec\EmisionVehiculos;

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
        $this->validations = new Assert\Collection(
            [
                'id_proceso'        => [ new Assert\Type([ 'type' => 'numeric']), new Assert\NotBlank() ],
                'id_certificado'    => [ new Assert\NotBlank() ],
                'cod_usuario'       => [ new Assert\NotBlank() ]
            ]
        );
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array $data)
    {
        $validate = Validation::createValidator();
        $response = $validate->validate($data, $this->validations);
        $errors = [];

        foreach ($response as $error) {
            $key = str_replace(['[', ']'], '', $error->getPropertyPath());

            if (!isset($errors[$key])) {
                $errors[$key] = [];
            }

            $errors[$key][] = $error->getMessage();
        }

        $this->errors = $errors;

        return $response->count() == 0 ? true : false;
    }
}
