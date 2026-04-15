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

            // SESIÓN CON TABLA
            $_SESSION['id_estu'] = $fetch->id_estu;
            $_SESSION['correo'] = $fetch->email_estu;
            $_SESSION['id_car']  = $fetch->id_car;

            $_SESSION['nomcompleto'] = 
                $fetch->nom_estu . ' ' . 
                $fetch->apepa_estu . ' ' . 
                $fetch->apema_estu;

            $_SESSION['user_image'] = $data['picture'];

            header('Location: ./vistas/index.php');
            exit;

        } else {
            // NO REGISTRADO
            unset($_SESSION['access_token']);
            $google_client->revokeToken();

            $_SESSION['error_message'] = "Sin acceso";
            header('Location: ./index.php');
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

    <style>
        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden; /* Evita scroll doble */
        }

        .vh-100-custom {
            height: calc(100vh - 56px); /* Altura total menos el footer */
        }

        .bg-login-image {
            background-image: url('./assets/images/tramites-login.webp');
            background-size: cover;
            background-position: center;
            height: 100%;
        }

        /* Colores exactos de la imagen */
        .btn-ingresar {
            background-color: #0c234a !important; /* Azul marino muy oscuro */
            border: none;
            color: white;
        }

        .btn-registro {
            background-color: #28a745 !important; /* Verde registro */
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
                        <h5 class="fw-bold text-undc mb-0">TRÁMITES</h5>
                        <p class="small fw-bold text-undc mb-4">ACADÉMICOS</p>
                        
                        <p class="text-muted small">Iniciar sesión con su correo y contraseña.</p>
                        <?php echo '<div class="d-grid my-3">' . $login_button . '</div>'; ?>
                    </div>
                    <div id="mensajeLogin" class="mt-2 text-center text-danger"></div>

                    <!-- Recuperar contraseña -->
                    <div class="text-center">
                        <a href="#" class="text-decoration-none small" data-bs-toggle="collapse" data-bs-target="#mensajeRecuperar" style="color: #0547a3;">
                            ¿Olvidó su contraseña?
                        </a>
                        <div id="mensajeRecuperar" class="collapse mt-2 text-muted small">
                            Escriba a la Oficina de Tecnologías de la Información: <a href="mailto:sistemas@undc.edu.pe">sistemas@undc.edu.pe</a>
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
            © 2025 Universidad Nacional de Cañete - Sistema de Trámite Documentario
        </small>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    if ($error_message == "Sin acceso") {
        echo
        '<script>
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
              toast.onmouseenter = Swal.stopTimer;
              toast.onmouseleave = Swal.resumeTimer;
            }
          });
          Toast.fire({
            icon: "error",
            title: "No tienes acceso al sistema."
          });
        </script>';
    }
    ?>

</body>

</html>