<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Trámite Documentario - UNDC</title>
    <link rel="shortcut icon" type="image/png" href="imagenes/undcico.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../estilos/bootstrap.min.css" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
        }

        .bg-login-image {
            background-image: url('../imagenes/fondompv.jpg');
            /* cambia por tu imagen */
            background-size: cover;
            background-position: center;
            height: 100%;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #007bff;
        }

        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-acceder:hover {
            opacity: 0.90
        }

        .circle {
            width: 30px;
            height: 30px;
            line-height: 30px;
            border-radius: 50%;
            background-color: #ddd;
            color: #fff;
            text-align: center;
            font-weight: bold;
        }

        .circle.active {
            background-color: #007bff;
        }

        .step-line {
            width: 40px;
            height: 2px;
            background-color: #ddd;
            margin-top: 15px;
        }

        .step {
            display: block;
        }

        .step.d-none {
            display: none !important;
        }
    </style>
</head>


<body class="bg-login-image">

    <div class="container-fluid h-100">
        <div class="row h-100">
            <!-- Columna del formulario -->
            <div class="col-lg-4 d-flex align-items-center justify-content-center bg-white">
                <div class="w-100 p-4" style="max-width: 400px;">
                    <div class="text-center mb-4">
                        <img src="../imagenes/logo-tramite.png" alt="Logo" class="mb-2" width="20%">
                        <img src="../imagenes/logos-mpv-azul.png" alt="Logo" class="mb-3" width="100%">
                        <!-- <h4 class="fw-bold mb-5">TRÁMITE DOCUMENTARIO</h4> -->

                        <p class="text-muted">Iniciar sesión con su correo institucional</p>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-acceder p-2" style="background-color: #dc3545;border-color:#dc3545" onclick="location.href='<?php echo $authUrl; ?>'"><i class="bi bi-google"></i> <b>Acceder con Google</b></button>

                    <div class="row g-3 mb-4 mt-3">
                        <div class="text-center">
                            <a href="#" class="text-decoration-none small" data-bs-toggle="collapse" data-bs-target="#mensajeRecuperar" style="color: #0547a3;">
                                ¿Olvidó su contraseña?
                            </a>
                            <div id="mensajeRecuperar" class="collapse mt-2 text-muted small">
                                Escriba a la Oficina de Tecnologías de la Información: <br>
                                <a href="mailto:sistemas@undc.edu.pe">sistemas@undc.edu.pe</a>
                            </div>
                        </div>

                        <!-- Crear cuenta (si aplica) -->
                        <div class="text-center mt-3">
                            <a href="registro.php" class="text-decoration-none small" style="color: #0547a3;">
                                ¿No tienes una cuenta? Crear una cuenta
                            </a>
                        </div>

                        <!-- Términos y condiciones -->
                        <p class="mt-4 text-muted small text-center">
                            Al hacer clic en <strong>"Acceder con Google"</strong>, aceptas nuestros
                            <a href="https://portal.undc.edu.pe/terminos-y-condiciones/" target="_blank" class="text-decoration-underline" style="color: #0547a3;">Términos y Condiciones</a> y nuestra
                            <a href="https://portal.undc.edu.pe/pdatospersonales/" target="_blank" class="text-decoration-underline" style="color: #0547a3;">Política de Privacidad</a>.
                        </p>
                    </div>
                </div>

            </div>

            <div id="cardsTramite" class="col-lg-8 d-flex flex-column justify-content-center align-items-center p-4">

                <!-- Card Trámite Virtual -->
                <div class="card mb-4 shadow-sm rounded-4 w-100" style="max-width: 600px;">
                    <div class="card-body">
                        <h6 class="card-title text-primary fw-bold">
                            <i class="bi bi-globe"></i> Trámite Virtual
                        </h6>
                        <p class="card-text fs-10">
                            Para iniciar un trámite sin adjuntar documentos adicionales, descarga, completa y firma el FUT, y adjúntalo en el formulario virtual.
                        </p>
                        <p class="card-text">
                            <i class="bi bi-clock"></i> <strong>Disponible 24/7</strong> a través de la Mesa de Partes Virtual.
                        </p>

                        <a href="ruta-al-archivo/FUT.pdf" class="btn btn-outline-primary w-100 mb-3" target="_blank">
                            <i class="bi bi-download"></i> Descargar Formato Único de Trámite (FUT)
                        </a>

                    </div>
                </div>

                <!-- Card Trámite Presencial -->
                <div class="card shadow-sm rounded-4 w-100" style="max-width: 600px;">
                    <div class="card-body">
                        <h6 class="card-title text-success fw-bold">
                            <i class="bi bi-person-lines-fill"></i> Trámite Presencial
                        </h6>
                        <p class="card-text">
                            <i class="bi bi-geo-alt-fill"></i> Acércate a la mesa de partes del local administrativo ubicado en <em>[Dirección exacta]</em>.
                        </p>
                        <p class="card-text">
                            <i class="bi bi-calendar-check"></i> <strong>Horario:</strong> Lunes a viernes de 08:00 a.m. a 01:00 p.m. y de 3:00 p.m. a 6:00 p.m.
                        </p>
                        <p class="card-text">
                            <i class="bi bi-envelope-paper"></i> Recibirás un <strong>código de seguimiento</strong> por correo electrónico para consultar el estado de tu trámite.
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </div>
    <footer class="text-center text-white py-3 border-top fixed-bottom"
        style="background: #0547a3;">
        <small>
            © 2025 Universidad Nacional de Cañete - Sistema de Trámite Documentario<br>
        </small>
    </footer>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>