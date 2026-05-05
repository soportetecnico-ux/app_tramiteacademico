<?php
if (strlen(session_id()) < 1) {
    session_start();
}

require_once "../../modelos/Documento.php";
$doc = new Documento();
$codigo = isset($_GET['cod']) ? htmlspecialchars($_GET['cod']) : '';

// Inicializamos variables de control
$error_msg = null;
$mostrar_documento = false;

// 1. Validar Sesión
if (!isset($_SESSION['id_estu'])) {
    $error_msg = "No se encontró una sesión activa. Por favor, inicie sesión nuevamente.";
}
// 2. Si hay sesión, Validar Permisos
else {
    $tiene_permiso = $doc->esPropietario($codigo, $_SESSION['id_estu']);
    if (!$tiene_permiso) {
        $error_msg = "No tienes permisos para ver este FUT.";
    } else {
        $mostrar_documento = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Formato Único de Trámite Digital</title>
    <link rel="stylesheet" href="./../../assets/css/style.css">
    <style>
        body {
            margin: 0;
            background-color: #525659;
            overflow: hidden;
        }

        iframe {
            width: 100vw;
            height: 100vh;
            border: none;
            display: block;
        }

        /* Ajuste para centrar la tarjeta si es necesario */
        .contenedor-error {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>

    <?php if ($mostrar_documento): ?>
        <iframe src="../../controladores/documentos.php?op=generarFUT&cod_web=<?php echo urlencode($codigo); ?>"></iframe>

    <?php else: ?>
        <div class="d-flex justify-content-center align-items-center m-5">
            <div class='card shadow' style="width: 90%; max-width: 800px;">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-dark">Formato Único de Trámite Digital</h5>
                </div>

                <div class="card-body text-center p-5">
                    <h5 class='text-danger'>Acceso Denegado</h5>
                    <p><?php echo $error_msg; ?></p>
                </div>

            </div>
        </div>
    <?php endif; ?>

</body>

</html>