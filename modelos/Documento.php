<?php

require_once(__DIR__ . '/../config/conexion.php');

class Documento
{
    public function __construct() {}

public function seleccionarTramite($id_car_sesion)
{
    $sql = "SELECT 
                t.id_tupa, 
                t.denominacion, 
                t.requisitos, 
                t.monto,
                o.cod_oficina,
                o.nombre AS nombre_oficina,
                -- Traemos los nombres de anexos configurados
                (SELECT GROUP_CONCAT(nombre_anexo SEPARATOR '|') 
                 FROM tb_tupa_anexo_config 
                 WHERE id_tupa = t.id_tupa AND estado = 1) as nombres_anexos
            FROM tb_tupa t
            LEFT JOIN tb_tupa_oficina v ON v.id_tupa = t.id_tupa 
                AND (v.id_car = '$id_car_sesion' OR v.id_car = 0)
            LEFT JOIN oficina o ON v.cod_oficina = o.cod_oficina
            WHERE t.estado = 1
            GROUP BY t.id_tupa
            ORDER BY v.id_car DESC";

    return ejecutarConsulta($sql);
}

    /*     public function seleccionarTramite()
    {
        $sql = "SELECT 
                t.id_tupa, 
                t.denominacion, 
                t.requisitos, 
                t.monto, 
                o.cod_oficina, 
                o.nombre 
            FROM tb_tupa t
            INNER JOIN tb_tupa_oficina v ON t.id_tupa = v.id_tupa
            INNER JOIN oficina o ON v.cod_oficina = o.cod_oficina
            WHERE v.estado = 1"; // Solo trámites con vinculación activa

        return ejecutarConsulta($sql);
    } */

    public function registrarMPV($data)
    {
        global $bd;

        $sql = "INSERT INTO mesa_de_partes 
                (cod_remitente, email, celular, direccion, cod_tipo_documento, folio, numero, asunto, mensaje, nombre_archivo, fecha, estudiante, cod_web,id_usuario)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?,?)";

        $stmt = mysqli_prepare($bd, $sql);
        if (!$stmt) {
            return ['status' => 'error', 'mensaje' => 'Error en la consulta: ' . mysqli_error($bd)];
        }

        mysqli_stmt_bind_param(
            $stmt,
            "sssssiisssssi",
            $data['cod_remitente'],
            $data['email'],
            $data['celular'],
            $data['direccion'],
            $data['cod_tipo_documento'],
            $data['folio'],
            $data['numero'],
            $data['asunto'],
            $data['mensaje'],
            $data['nombre_archivo'],
            $data['estudiante'],
            $data['cod_web'],
            $data['id_usuario']
        );

        $ok = mysqli_stmt_execute($stmt);

        if ($ok) {
            return [
                'status' => 'success',
                'mensaje' => 'Documento registrado correctamente',
                'cod_web' => $data['cod_web']
            ];
        } else {
            return ['status' => 'error', 'mensaje' => 'Error al registrar: ' . mysqli_stmt_error($stmt)];
        }
    }

    public function listarMisTramites($id_usuario)
    {
        global $bd;

        $sql = "SELECT 
                mp.fecha,
                mp.cod_web,
                mp.cod_tipo_documento,
                tp.descripcion AS tipo_documento,
                mp.folio,
                mp.numero,
                mp.asunto,
                mp.nombre_archivo
            FROM mesa_de_partes mp
            INNER JOIN tipo_documento tp 
                ON mp.cod_tipo_documento = tp.cod_tipo_documento
            WHERE mp.id_usuario='$id_usuario' 
            ORDER BY mp.fecha DESC;";

        $consulta = mysqli_query($bd, $sql);
        if (!$consulta) return false;
        return $consulta;
    }
}
