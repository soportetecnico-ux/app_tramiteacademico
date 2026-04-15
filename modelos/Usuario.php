<?php

require_once(__DIR__ . '/../config/conexion.php');


class Usuario
{

    public function __construct() {}

    //UsuarioLogeo
    public function verificarLogeo($email)
    {
        $sql = "SELECT e.*, d.id_car 
            FROM estudiante e
            INNER JOIN detestudiante d ON e.id_estu = d.id_estu
            WHERE e.email_estu = '$email' 
            LIMIT 1";
        return ejecutarConsulta2($sql);
    }

    //Mostrar Datos Usuario

    public function obtenerDatosUsuario($id_estu)
    {
        $sql = "SELECT 
            e.id_estu, 
            e.nivel, 
            e.cod_estu, 
            e.tipo_docu, 
            e.dni_estu, 
            e.apepa_estu, 
            e.apema_estu, 
            e.nom_estu, 
            e.sexo_estu, 
            e.domi_estu, 
            e.email_estu, 
            e.celu_estu,
            d.nom_depar AS depar, 
            p.nom_pro AS provi, 
            dist.nom_dis AS dist
        FROM estudiante e
        LEFT JOIN departamento d ON e.id_depar = d.id_depar
        LEFT JOIN provincia p ON e.id_pro = p.id_pro
        LEFT JOIN distrito dist ON e.id_dis = dist.id_dis
        WHERE e.id_estu = '$id_estu'";

        return ejecutarConsultaSimpleFila2($sql);
    }
}
