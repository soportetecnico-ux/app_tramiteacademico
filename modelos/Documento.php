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
                o.nombre AS nombre_oficina
            FROM tb_tupa t
            LEFT JOIN tb_tupa_oficina v ON v.id_tupa = t.id_tupa 
                AND (v.id_car = '$id_car_sesion' OR v.id_car = 0)
            LEFT JOIN oficina o ON v.cod_oficina = o.cod_oficina
            WHERE t.estado = 1
            GROUP BY t.id_tupa
            ORDER BY v.id_car DESC";

        return ejecutarConsulta($sql);
    }

    public function registrarDocumento($data)
    {
        global $conexion;
        if (!$conexion) return ['status' => 'error', 'mensaje' => 'No hay conexión.'];

        mysqli_begin_transaction($conexion);

        try {

            $nro_documento = $this->obtenerCorrelativo($data['id_estu']);
            // --- A. TABLA: documento --- (Tu código existente)
            $sqlDoc = "INSERT INTO documento (asunto, mensaje, folio, fecha, fecha_emision, numero, cod_tipo_documento, cod_oficina, cod_estado_documento, cod_estado_documento2, anexos, cod_usuario, cod_web, id_estu, id_tupa, celular_estu, comprobante, fecha_comprobante, observaciones, nombre_archivo,tipo_tramite) 
                   VALUES (?, ?, 1, NOW(), CURDATE(), ?, 6, 1, 3, 'Derivado', 1, 2, ?, ?, ?, ?, ?, ?, ?, ?, 'TA')";

            $stmtDoc = mysqli_prepare($conexion, $sqlDoc);
            mysqli_stmt_bind_param(
                $stmtDoc,
                "ssisiiissss",
                $data['denominacion'],
                $data['fundamentacion'],
                $nro_documento,
                $data['cod_web'],
                $data['id_estu'],
                $data['id_tupa'],
                $data['celular'],
                $data['nro_comprobante'],
                $data['fecha_comprobante'],
                $data['observaciones'],
                $data['nombre_archivo']
            );
            if (!mysqli_stmt_execute($stmtDoc)) throw new Exception("Error Documento: " . mysqli_stmt_error($stmtDoc));

            $cod_documento = mysqli_insert_id($conexion);

            // --- B. TABLA: historial_documento --- (Tu código existente)
            $sqlHist = "INSERT INTO historial_documento (cod_historial_documento_origen, oficina_destino, fecha_emision, oficina_origen, cod_documento, cod_trabajador, estado, estado2, eliminado) 
                    VALUES (0, ?, NOW(), 1, ?, 1, 1, 'Sin recibir', 0)";
            $stmtHist = mysqli_prepare($conexion, $sqlHist);
            mysqli_stmt_bind_param($stmtHist, "ii", $data['cod_oficina'], $cod_documento);
            if (!mysqli_stmt_execute($stmtHist)) throw new Exception("Error Historial: " . mysqli_stmt_error($stmtHist));

            // --- C. TABLA: tb_firma_fut --- (LO NUEVO)
            $sqlFirma = "INSERT INTO tb_firma_fut (cod_web, id_estu, firmado_por, dni_firmante, motivo, fecha_sello) 
                     VALUES (?, ?, ?, ?, 'Soy el autor del documento', ?)";

            $stmtFirma = mysqli_prepare($conexion, $sqlFirma);
            if (!$stmtFirma) throw new Exception("Error preparación Firma: " . mysqli_error($conexion));

            mysqli_stmt_bind_param(
                $stmtFirma,
                "sisss",
                $data['cod_web'],
                $data['id_estu'],
                $data['firmado_por'],
                $data['dni_firmante'],
                $data['fecha_sello']
            );

            if (!mysqli_stmt_execute($stmtFirma)) {
                throw new Exception("Error al insertar Firma: " . mysqli_stmt_error($stmtFirma));
            }

            mysqli_commit($conexion);
            return ['status' => 'success', 'mensaje' => 'Trámite y firma registrados.', 'cod_web' => $data['cod_web']];
        } catch (Exception $e) {
            mysqli_rollback($conexion);
            return ['status' => 'error', 'mensaje' => $e->getMessage()];
        }
    }

    public function listarMisTramites($id_estu)
    {
        global $conexion;

        // 1. Limpiamos el SQL: agregamos DATE_FORMAT y corregimos el alias del ORDER BY
        $sql = "SELECT 
                d.cod_web, 
                d.fecha,
                d.asunto, 
                o.nombre AS nombre_oficina,
                d.nombre_archivo,
                d.cod_estado_documento2 as estado
            FROM documento AS d
            INNER JOIN historial_documento AS hd ON d.cod_documento = hd.cod_documento
            INNER JOIN oficina AS o ON o.cod_oficina = hd.oficina_destino
            WHERE d.id_estu = '$id_estu'
            ORDER BY d.fecha DESC";

        $consulta = mysqli_query($conexion, $sql);
        return $consulta; // Si falla, devolverá false y el controlador lo manejará
    }

    public function mostrarSeguimientoInicial($id_estu, $cod_web)
    {
        global $conexion;

        // Esta consulta trae los datos del documento y el detalle de cada movimiento en el historial
        $sql = "SELECT 
                d.cod_documento,
                d.asunto,
                d.numero as num_doc,
                d.cod_estado_documento2 as estado,
                hd.cod_historial_documento as n_proveido,
                o_orig.nombre AS nombre_oficina_origen,
                hd.fecha_emision as fecha, 
                o_orig.celular AS celular_origen,
                o_dest.nombre AS nombre_oficina,
                hd.fecha_recepcion,
                o_dest.celular AS celular_destino,
                hd.estado2,
                hd.proveido as comentario
            FROM documento AS d
            INNER JOIN historial_documento AS hd ON d.cod_documento = hd.cod_documento
            LEFT JOIN oficina AS o_orig ON hd.oficina_origen = o_orig.cod_oficina
            LEFT JOIN oficina AS o_dest ON hd.oficina_destino = o_dest.cod_oficina
            WHERE d.cod_web = '$cod_web' 
            AND hd.eliminado = '0'
            ORDER BY hd.cod_historial_documento DESC";

        $consulta = mysqli_query($conexion, $sql);
        return $consulta;
    }

    /*public function mostrarSeguimientoInicial($id_estu, $cod_web)
    {
        global $conexion;

        // Esta consulta trae los datos del documento y el detalle de cada movimiento en el historial
        $sql = "SELECT 
            d.cod_web,
            d.asunto,
            d.fecha,
            o.nombre AS nombre_oficina,
            d.cod_estado_documento2 AS estado
        FROM documento AS d
        INNER JOIN historial_documento AS hd 
            ON d.cod_documento = hd.cod_documento
        INNER JOIN oficina AS o 
            ON hd.oficina_destino = o.cod_oficina
        WHERE d.cod_web = '$cod_web'
        AND d.eliminado = '0'
        AND hd.cod_historial_documento_origen = 0";

        $consulta = mysqli_query($conexion, $sql);
        return $consulta;
    }*/

    public function obtenerCorrelativo($id_estu) {
        global $conexion;
        $eliminado=0;
        // 1. Preparamos la sentencia
        $sql = "SELECT COUNT(*) as total FROM documento WHERE id_estu = ? and eliminado = ?";
        $stmt = mysqli_prepare($conexion, $sql);

        // 2. Vinculamos el parámetro (la "i" significa que id_estu es un entero/integer)
        mysqli_stmt_bind_param($stmt, "ii", $id_estu, $eliminado);
 
        // 3. Ejecutamos y obtenemos el resultado
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $fila = mysqli_fetch_assoc($resultado);

        // 4. Retornamos el siguiente número
        return $fila['total'] + 1;
    }

    public function obtenerDatosFUT($cod_web) {
        $sql = "SELECT d.*, o.nombre AS oficina FROM documento d INNER JOIN historial_documento hd ON hd.cod_documento = d.cod_documento INNER JOIN oficina o ON o.cod_oficina = hd.oficina_destino  
        INNER JOIN tb_tupa t ON t.id_tupa = d.id_tupa INNER JOIN tb_tupa_oficina v ON v.id_tupa = t.id_tupa AND v.cod_oficina = o.cod_oficina 
         WHERE d.cod_web = '$cod_web' and d.eliminado = 0";
        return ejecutarConsulta($sql);
    }
    public function obtenerFirmaFUT($cod_web) {
        $sql = "SELECT * FROM tb_firma_fut WHERE cod_web = '$cod_web'";
        return ejecutarConsulta($sql);
    }
}
