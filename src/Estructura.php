<?php
namespace Tramasec\EmisionVehiculos;

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
        $this->validations = new Assert\Collection([
            'grupo_endoso'      => [ new Assert\Range(['min' => 1, 'max' => 3]), new Assert\NotBlank() ],
            'id_certificado'    => [ new Assert\NotBlank() ],
            'item'              => [ new Assert\NotBlank(), new Assert\EqualTo(['value' => '1']) ],
            'cod_usuario'       => [ new Assert\NotBlank() ],
            'poliza'            => [ new Assert\Type([ 'type' => 'numeric' ]) ],


            // ------------------- INFORMACION TITULAR ------------------

            'cedula_ruc'        => [],
            'ap_paterno'        => [ new Assert\Regex(['pattern' => '/^[\pL\pM\p{Zs}.-]+$/u']) ], //Alpha Sapaces and Tildes
            'ap_materno'        => [ new Assert\Regex(['pattern' => '/^[\pL\pM\p{Zs}.-]+$/u']) ],
            'nombres'           => [ new Assert\Regex(['pattern' => '/^[\pL\pM\p{Zs}.-]+$/u']), new Assert\NotBlank() ],
            'nombre_asegurado'  => [ new Assert\Regex(['pattern' => '/^[\pL\pM\p{Zs}.-]+$/u']), new Assert\NotBlank() ],
            'direccion'         => [ new Assert\NotBlank() ],
            'sector'            => [],
            'ciudad2'           => [ new Assert\Regex(['pattern' => '/^[\pL\pM\p{Zs}.-]+$/u']) ],
            'direccion2'        => [],
            'direccion3'        => [],
            'telf_particular'   => [ new Assert\Type([ 'type' => 'numeric' ]) ],
            'telefono3'         => [],
            'telefono4'         => [],
            'fecha_nacim'       => [ new Assert\Date() ],
            'telf_oficina'      => [ new Assert\Type([ 'type' => 'numeric' ]) ],
            'indicativo'        => [ new Assert\NotBlank(), new Assert\Email() ],
            'ciudad'            => [ new Assert\Regex(['pattern' => '/^[\pL\pM\p{Zs}.-]+$/u']), new Assert\NotBlank() ],
            'provincia'         => [ new Assert\Regex(['pattern' => '/^[\pL\pM\p{Zs}.-]+$/u']), new Assert\NotBlank() ],
            'cod_estado_clte'   => [],

            'fec_exp_pas_aseg'  => [],
            'fec_ven_pas_aseg'  => [],
            'fec_ing_pais_aseg' => [],
            'cod_est_mig_aseg'  => [],

            // ---------------------   COTIZADOR -------------------

            'tasa'              => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'prima_neta'        => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'super'             => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'seg_camp'          => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'der_emi'           => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'iva'               => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'prima_total'       => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'nro_polizas'       => [],
            'PjeComisAgente'    => [],

            // --------------------- POLIZA ---------------------

            'suma_aseg'    => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'suma_aseg2'        => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ], //debe tener lo mismo que arriba
            'inicio_vigencia'   => [ new Assert\NotBlank(), new Assert\Date() ],
            'fin_vigencia'      => [ new Assert\NotBlank(), new Assert\Date() ],
            'producto'          => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'subproducto'       => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'sucursal'          => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'pto_vta'           => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'zona_del_producto' => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'aclaratorio_poliza'=> [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'aclaratorio_item'  => [],
            'companyname'       => [ new Assert\Regex(['pattern' => '/^[\pL\pM\p{Zs}.-]+$/u']), new Assert\NotBlank() ],
            'fecha_compra'      => [],
            'certificado'       => [ new Assert\NotBlank() ],
            'fec_procreso'      => [],


            // --------------------- VEHICULO ---------------------

            'marca'             => [ new Assert\Type(['type' => 'numeric']) ],
            'modelo'            => [ new Assert\Type(['type' => 'numeric']) ],
            'chasis'            => [ new Assert\NotBlank() ],
            'anio_modelo'       => [ new Assert\GreaterThanOrEqual(['value' => 1950]),
                new Assert\LessThanOrEqual(['value' => intval(date('Y')) + 2]) ] ,
            'color'             => [ new Assert\Type(['type' => 'numeric']) ],
            'motor'             => [ new Assert\NotBlank() ],
            'cod_tipo_vehiculo' => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'cod_placa'         => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'placa'             => [ new Assert\NotBlank() ],
            'cod_estado_veh'    => [],

            // --------------------- PAGO ----------------------

            'nro_cuenta_tarjeta'=> [], //DEPENDIENTE
            'aaaamm_vto_tarj'   => [],//DEPENDIENTE

            // --------------------- CODIGOS ---------------------

            'cod_concesionario' => [],
            'cod_item'          => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'cod_plan_pago'     => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'codigo_conducto'   => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'codigo_deducible'  => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'num_convenio'      => [],
            'quincena'          => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'documento'         => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],

            'cod_pais'          => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'id_pv_plano'       => [],
            'fact_grupal'       => [ new Assert\NotBlank(), new Assert\EqualTo(['value' => 0]) ],
            'imp_otroscargos_con_iva' => [],
            'txt_origen'        => [],
            'sn_campania'       => [],
            'sn_inspeccion'     => [],
            'cod_estado_procesado' => [],
            'id_proceso_slx'    => [],
            'NroPolLider'       => [],

            // --------------------- PAGADOR ---------------------

            'txt_apellido1_pag' => [],
            'txt_apellido2_pag' => [],
            'txt_nombre_pag'    => [],
            'cod_tipo_doc_pag'  => [],
            'cedula_ruc_pag'    => [],
            'txt_email_pag'     => [],
            'fec_nac_pag'       => [],
            'tel_casa_pag'      => [],
            'tel_oficina_pag'   => [],
            'tel_celular_pag'   => [],
            'txt_direccion_pag' => [],
            'sector_pag'        => [],
            'provincia_pag'     => [],
            'ciudad_pag'        => [],

            // --------------------- SIN CATEGORIA ---------------------

            'status'            => [],
            'cero'              => [],
            'uno'               => [],
            'operacion'         => [],
            'tipo_doc'          => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],
            'cod_estado'        => [],
            'log_error'         => [],
            'flag'              => [ new Assert\NotBlank(), new Assert\Type(['type' => 'numeric']) ],



        ]);
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
