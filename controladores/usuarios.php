<?php

session_start();

require_once(__DIR__ . '/../modelos/Usuario.php');

$usuarios = new Usuario();


$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fech_crea = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {

    case 'salir':
        // 1. Asegurar que tenemos la sesión
        if (strlen(session_id()) < 1) {
            session_start();
        }

        // 2. LOGOUT QUIRÚRGICO: Solo eliminamos lo que pertenece a este sistema
        // Esto mantiene viva la sesión del navegador para que el OTRO sistema no se cierre
        unset($_SESSION['sistema_academico']);
        unset($_SESSION['access_token']);

        // 3. Revocar Token de Google (con validación de existencia)
        include('../google/config.php');
        if (isset($google_client)) {
            $google_client->revokeToken();
        }

        // 4. Prevenir caché (Crucial para que no puedan volver atrás)
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // 5. Redireccionar al login
        header("Location: ../index.php");
        exit();


    case 'obtenerDatosUsuario':

        $id_estu = $_SESSION['sistema_academico']['id_estu'];

        $rspta = $usuarios->obtenerDatosUsuario($id_estu);

        if ($rspta) {
            echo json_encode([
                "status" => "success",
                "data" => $rspta
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "mensaje" => "No se encontraron datos"
            ]);
        }

        break;


    default:

        break;
}
