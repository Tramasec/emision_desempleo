<?php
namespace Tramasec\EmisionDesempleo;

use Rakit\Validation\Validator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class Estructura
{
    private $validations;
    public $grupo_endoso;
    public $validator;
    public $errors;

    /**
     * Estructura constructor.
     */
    public function __construct()
    {

        $this->validations = [
            'COD_USUARIO'       => 'required',
            'COD_RAMO'          => 'required',
            'FEC_VIG_DESDE'     => 'required',
            'FEC_VIG_HASTA'     => 'required',
            'IMP_DEREMI'        => 'required|numeric',
            'COD_PPAGO'         => 'required|numeric',
            'COD_SUC'           => 'required|numeric',
            'COD_PTO_VTA'       => 'required|numeric',
            'COD_CONDUCTO'      => 'required|numeric',
            'TXT_TARJETA'       => '',
            'AAAAMM_VTO_TARJ'   => '',

            'ITEM'                  => 'required|array',
            'ITEM.*.COD_ITEM'       => 'required|numeric',
            'ITEM.*.COD_GIRO_NEGOCIO'  => 'required|numeric',
            'ITEM.*.COD_PAIS'       =>  'required|numeric',
            'ITEM.*.COD_DPTO'       => 'required|numeric',
            'ITEM.*.COD_MUNICIPIO'  => 'required|numeric',
            'ITEM.*.TXT_DIRECCION'  => '',
            'ITEM.*.COD_PRODUCTO'   => 'required|numeric',
            'ITEM.*.COD_SUBPRODUCTO_CIAL' => 'required|numeric',
            'ITEM.*.ACLAR_ITEM'     => 'required',

            'PAGADOR'               => 'required|array',
            'PAGADOR.*.COD_ITEM'    => 'required|numeric|in:1',
            'PAGADOR.*.TXT_APELLIDO1' => 'required',
            'PAGADOR.*.TXT_APELLIDO2' => 'required',
            'PAGADOR.*.TXT_NOMBRE' => 'required',
            'PAGADOR.*.COD_TIPO_DOC' => 'required',
            'PAGADOR.*.NRO_DOC' => 'required',
            'PAGADOR.*.FEC_NAC' => 'required',
            'PAGADOR.*.TXT_LUGAR_NAC' => '',
            'PAGADOR.*.TXT_SEXO' => 'required',
            'PAGADOR.*.COD_EST_CIVIL' => 'required',
            'PAGADOR.*.TXT_NOM_FACTURA' => 'required',
            'PAGADOR.*.COD_TIPO_DIR' => 'required',
            'PAGADOR.*.TXT_DIRECCION' => 'required',
            'PAGADOR.*.COD_MUNICIPIO' => 'required',
            'PAGADOR.*.COD_DPTO' => 'required',
            'PAGADOR.*.COD_PAIS' => 'required',
            'PAGADOR.*.COD_TIPO_DIR1' => '',
            'PAGADOR.*.TXT_DIRECCION1' => '',
            'PAGADOR.*.COD_MUNICIPIO1' => '',
            'PAGADOR.*.COD_DPTO1' => '',
            'PAGADOR.*.COD_PAIS1' => '',
            'PAGADOR.*.COD_TIPO_TELEF' => 'required',
            'PAGADOR.*.TXT_TELEFONO' => 'required',
            'PAGADOR.*.COD_TIPO_TELEF1' => '',
            'PAGADOR.*.TXT_TELEFONO1' => '',
            'PAGADOR.*.TXT_MAIL' => 'required|email',
            'PAGADOR.*.COD_TIPO_TELEF3' => '',
            'PAGADOR.*.TXT_TELEFONO3' => '',
            'PAGADOR.*.COD_TIPO_TELEF4' => '',
            'PAGADOR.*.TXT_TELEFONO4' => '',
            'PAGADOR.*.COD_TIPO_TELEF_OFIC' => '',
            'PAGADOR.*.TXT_TELEF_OFIC' => '',
            'PAGADOR.*.COD_TIPO_DIR2' => '',
            'PAGADOR.*.TXT_DIRECCION2' => '',
            'PAGADOR.*.COD_TIPO_DIR3' => '',
            'PAGADOR.*.TXT_DIRECCION3' => '',
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
