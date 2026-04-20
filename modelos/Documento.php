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
            // --- A. TABLA: documento --- (Tu código existente)
            $sqlDoc = "INSERT INTO documento (asunto, mensaje, folio, fecha, fecha_emision, cod_tipo_documento, cod_oficina, cod_estado_documento, cod_estado_documento2, anexos, cod_usuario, cod_web, id_estu, id_tupa, comprobante, fecha_comprobante, observaciones, nombre_archivo) 
                   VALUES (?, ?, 1, NOW(), CURDATE(), 6, 1, 3, 'Derivado', 1, 2, ?, ?, ?, ?, ?, ?, ?)";

            $stmtDoc = mysqli_prepare($conexion, $sqlDoc);
            mysqli_stmt_bind_param(
                $stmtDoc,
                "sssiissss",
                $data['denominacion'],
                $data['fundamentacion'],
                $data['cod_web'],
                $data['id_estu'],
                $data['id_tupa'],
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
                o.nombre AS nombre_oficina 
            FROM documento AS d
            INNER JOIN historial_documento AS hd ON d.cod_documento = hd.cod_documento
            INNER JOIN oficina AS o ON o.cod_oficina = hd.oficina_destino
            WHERE d.id_estu = '$id_estu'
            ORDER BY d.fecha DESC";

        $consulta = mysqli_query($conexion, $sql);
        return $consulta; // Si falla, devolverá false y el controlador lo manejará
    }
}
