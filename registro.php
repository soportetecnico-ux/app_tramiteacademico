<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Trámite Documentario - UNDC</title>
    <link rel="shortcut icon" type="image/png" href="../imagenes/undcico.png">
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
            <div class="col-lg-4 d-flex align-items-center justify-content-center">

                <!-- FORMULARIO DE CREAR CUENTA (INICIALMENTE OCULTO) -->
                <div id="registroCuenta" class="col-lg-8 d-flex flex-column justify-content-center align-items-center p-4">
                    <!-- PESTAÑAS/PASOS -->
                    <div class="text-center mb-4">
                        <h5 class="fw-bold text-primary">CREAR UNA CUENTA</h5>
                        <div class="d-flex justify-content-center align-items-center mt-3 gap-4">
                            <div class="step-indicator text-center">
                                <div id="circle1" class="circle active">1</div>
                                <div class="fw-bold small mt-2">Validación<br>de datos</div>
                            </div>
                            <div class="step-line"></div>
                            <div class="step-indicator text-center">
                                <div id="circle2" class="circle">2</div>
                                <div class="fw-bold small mt-2">Registro de<br>usuario</div>
                            </div>
                            <div class="step-line"></div>
                            <div class="step-indicator text-center">
                                <div id="circle3" class="circle">3</div>
                                <div class="fw-bold small mt-2">Activa tu<br>cuenta</div>
                            </div>
                        </div>
                    </div>

                    <!-- FORMULARIO DE PASOS -->
                    <div class="card shadow-sm rounded-4 mx-auto w-100" style="max-width: 600px;">
                        <div class="card-body">
                            <form id="formRegistro">
                                <!-- Paso 1 -->
                                <div class="step" id="paso1">
                                    <div class="mb-3">
                                        <label for="tipoDocumento" class="form-label">Tipo de documento</label>
                                        <select class="form-select" id="tipoDocumento" required>
                                            <option value="">Seleccione tipo de documento</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="documento" class="form-label">Documento de identidad</label>
                                        <input type="text" class="form-control" id="documento" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fechaNacimiento" class="form-label">Fecha de nacimiento</label>
                                        <input type="date" class="form-control" id="fechaNacimiento" required>
                                    </div>
                                    <button type="button" class="btn btn-primary w-100" onclick="cambiarPaso(2)">Siguiente</button>
                                </div>

                                <!-- Paso 2 -->
                                <div class="step d-none" id="paso2">
                                    <div class="mb-3">
                                        <label class="form-label">Nombres</label>
                                        <input type="text" class="form-control" id="nombres" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Apellido Paterno</label>
                                            <input type="text" class="form-control" id="apellidoPaterno" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Apellido Materno</label>
                                            <input type="text" class="form-control" id="apellidoMaterno" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Correo electrónico</label>
                                        <div class="input-group">
                                            <input type="email" class="form-control" id="correo" required>
                                            <button class="btn btn-outline-primary" type="button" onclick="enviarCodigo()">Validar</button>
                                        </div>
                                    </div>

                                    <div class="mb-3 d-none" id="grupoCodigo">
                                        <label class="form-label">
                                            Código de verificación <span id="tiempoRestante" class="text-muted ms-2">(03:00)</span>
                                        </label>
                                        <input type="text" class="form-control" id="codigoVerificacion" placeholder="Ingrese el código">
                                        <div id="mensajeCodigo" class="form-text text-danger d-none">Código expirado. Solicita uno nuevo.</div>
                                        <button type="button" class="btn btn-primary mt-2" id="btnValidarCodigo" onclick="validarCodigo()">Validar Código</button>
                                    </div>



                                    <button type="button" class="btn btn-secondary w-100 mb-2" onclick="cambiarPaso(1)">Atrás</button>
                                    <button type="button" class="btn btn-success w-100" id="btnActivarCuenta" onclick="registrarUsuario()" disabled>Activar cuenta</button>

                                </div>


                                <!-- Paso 3 -->
                                <div class="step d-none" id="paso3">
                                    <p class="text-success text-center">Usted ha sido registrado ocn éxito</p>
                                    <a href="index.php" class="btn btn-success w-100">Iniciar</a>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-lg-8 d-none d-lg-block p-0">
                <div class="bg-login-image h-100 w-100bg-cover bg-center min-vh-100 d-flex align-items-center" style="background-image: linear-gradient(rgba(5, 71, 163, 0.5), rgba(5, 71, 163, 0.2)), url('../imagenes/tramites-ciudadanos3.webp');"></div>
            </div>
        </div>
    </div>
    <footer class="text-center text-white py-3 border-top fixed-bottom"
        style="background: #0547a3;">
        <small>
            © 2025 Universidad Nacional de Cañete - Sistema de Trámite Documentario<br>
        </small>
    </footer>
    <script>
        function cambiarPaso(n) {
            // Ocultar todos
            document.querySelectorAll(".step").forEach(s => s.classList.add("d-none"));
            // Mostrar el actual
            document.getElementById("paso" + n).classList.remove("d-none");

            // Actualizar los círculos
            for (let i = 1; i <= 3; i++) {
                document.getElementById("circle" + i).classList.remove("active");
            }
            document.getElementById("circle" + n).classList.add("active");
        }
    </script>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./js/usuarios.js"></script>
</body>

</html>