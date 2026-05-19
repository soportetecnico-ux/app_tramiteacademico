<?php

require_once("./modelos/Usuario.php");
include('./google/config.php');

// Mostrar error si existe
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_GET["code"])) {

    $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

    if (!isset($token['error'])) {

        $google_client->setAccessToken($token['access_token']);
        $_SESSION['access_token'] = $token['access_token'];

        $google_service = new Google_Service_Oauth2($google_client);
        $data = $google_service->userinfo->get();

        $email = $data['email'];

        //solo correos institucionales

        if (!str_ends_with($email, '@undc.edu.pe')) {
            $_SESSION['error_message'] = "Solo correos institucionales";
            header('Location: ./index.php');
            exit;
        }

        //VALIDAR EN BD
        $usuarios = new Usuario();
        $rspta = $usuarios->verificarLogeo($email);

        if ($rspta && $rspta->num_rows > 0) {

            $fetch = $rspta->fetch_object();

            $_SESSION['sistema_academico']['id_estu']    = $fetch->id_estu;
            $_SESSION['sistema_academico']['correo']     = $fetch->email_estu;
            $_SESSION['sistema_academico']['id_car']     = $fetch->id_car;
            $_SESSION['sistema_academico']['nivel']      = $fetch->nivel;

            $_SESSION['sistema_academico']['nomcompleto'] =
                $fetch->nom_estu . ' ' .
                $fetch->apepa_estu . ' ' .
                $fetch->apema_estu;

            $_SESSION['sistema_academico']['user_image'] = $data['picture'];

            header('Location: ./vistas/index.php');
            exit;
        } else {
            // NO REGISTRADO
            unset($_SESSION['access_token']);
            $google_client->revokeToken();

            $_SESSION['error_message'] = "Sin acceso";
            header('Location: index.php');
            exit;
        }
    }
}

$login_button = '
<a class="btn mt-2 btn-danger w-100" href="' . $google_client->createAuthUrl() . '">
    Iniciar sesión con Google
</a>
';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Trámite Documentario - UNDC</title>
    <link rel="shortcut icon" type="image/png" href="imagenes/undcico.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="./assets/images/sistema/logo-tramite.png" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/style-preset.css">
    <link rel="stylesheet" href="./assets/fonts/tabler-icons.min.css">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            overflow: hidden;
            font-family: 'Poppins', sans-serif !important;
        }

        .vh-100-custom {
            height: calc(100vh - 56px);
            /* Altura total menos el footer */
        }

        .bg-login-image {
            background-image: url('./assets/images/tramites-login.webp');
            background-size: cover;
            background-position: center;
            height: 100%;
        }

        /* Colores exactos de la imagen */
        .btn-ingresar {
            background-color: #0c234a !important;
            /* Azul marino muy oscuro */
            border: none;
            color: white;
        }

        .btn-registro {
            background-color: #28a745 !important;
            /* Verde registro */
            border: none;
            color: white;
        }

        .form-control {
            border-radius: 5px;
            padding: 10px;
        }

        .text-undc {
            color: #0547a3;
        }

        footer {
            height: 56px;
            background: #0547a3;
            z-index: 1030;
        }
    </style>
</head>

<body>

    <div class="container-fluid p-0">
        <div class="row g-0 vh-100-custom">

            <div class="col-lg-4 d-flex align-items-center justify-content-center bg-white shadow-lg">
                <div class="w-100 p-5" style="max-width: 400px;">
                    <div class="text-center mb-4">
                        <img src="../imagenes/logo-tramite.png" alt="Escudo" class="mb-3" width="60">
                        <h5 class="fw-semibold text-undc mb-0">SISTEMA DE TRÁMITES ACADÉMICOS</h5>

                        <p class="text-muted small mt-3">Iniciar sesión con su correo institucional</p>
                        <?php echo '<div class="d-grid my-2">' . $login_button . '</div>'; ?>
                    </div>
                    <div id="mensajeLogin" class="mt-2 text-center"></div>

                    <!-- Recuperar contraseña -->
                    <div class="text-center">
                        <a href="#" class="text-decoration-none small" data-bs-toggle="collapse" data-bs-target="#mensajeRecuperar" style="color: #0547a3;">
                            ¿Olvidó su contraseña?
                        </a>
                        <div id="mensajeRecuperar" class="collapse mt-2 small">
                            <div class="alert alert-info p-2 mb-0 small">
                                Si tiene problemas de acceso, comuníquese con la Oficina de Tecnologías de la Información al correo:
                                <a href="mailto:sistemas@undc.edu.pe">sistemas@undc.edu.pe</a><br>
                                Adjunte su <strong>DNI</strong> y proporcione sus datos completos (nombres y apellidos) para su validación.
                            </div>
                        </div>
                    </div>

                    <p class="mt-3 text-muted" style="font-size: 0.9rem; text-align: center;">
                        Al hacer clic en <strong>"Ingresar"</strong>, aceptas nuestros
                        <a href="#" class="text-undc">Términos y Condiciones</a> y nuestra
                        <a href="#" class="text-undc">Política de Privacidad</a>.
                    </p>
                </div>
            </div>

            <div class="col-lg-8 d-none d-lg-block">
                <div class="bg-login-image"></div>
            </div>

        </div>
    </div>

    <footer class="fixed-bottom d-flex align-items-center justify-content-center text-white">
        <small class="text-center">
            © <script>
                document.write(new Date().getFullYear())
            </script> Universidad Nacional de Cañete - Sistema de Trámites Académicos
        </small>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    if ($error_message == "Sin acceso") {

        echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        let div = document.getElementById("mensajeLogin");

        div.innerHTML = `
    <div class="alert alert-danger d-flex justify-content-center align-items-center text-center p-2" role="alert">
        <i class="ti ti-alert-triangle me-2 fs-3"></i>
        <div>El correo no tiene acceso.</div>
    </div>
`;
        setTimeout(() => {
            div.innerHTML = "";
        }, 6000);
    });
    </script>';
    }
    ?>

</body>

</html>