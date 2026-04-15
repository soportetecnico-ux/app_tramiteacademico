<?php

session_start();

require_once(__DIR__ . '/../modelos/Usuario.php');

$usuarios = new Usuario();


$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fech_crea = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {

    case 'salir':
        include('../google/config.php');

        // Revocar token de Google
        $google_client->revokeToken();

        // Limpiar sesión completamente
        session_start();
        $_SESSION = [];
        session_unset();
        session_destroy();

        // Eliminar cookies de sesión si existen
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Evitar que el navegador guarde caché
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // Redireccionar al login
        header("Location: ../index.php");
        exit();



    case 'obtenerDatosUsuario':

        $id_estu = $_SESSION['id_estu'];

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
