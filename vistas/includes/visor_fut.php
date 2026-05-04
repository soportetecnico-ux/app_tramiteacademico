<?php
if (strlen(session_id()) < 1) {
    session_start();
}

// 1. Validación de sesión: Si no existe id_estu o cod_usuario, bloqueamos el acceso.
if (!isset($_SESSION['id_estu']) && !isset($_SESSION['cod_usuario'])) {
    // Si la petición es por ventana emergente, es mejor mostrar un mensaje que redireccionar bruscamente
    echo "
    <div style='text-align:center; padding:50px; font-family:sans-serif;'>
        <h2 style='color:#085ec5;'>Acceso Restringido</h2>
        <p>No se encontró una sesión activa. Por favor, inicie sesión nuevamente.</p>
        <br>
        <a href='https://tramite.undc.edu.pe/' style='background:#085ec5; color:white; padding:10px 20px; text-decoration:none; border-radius:4px;'>Regresar al Inicio</a>
    </div>";
    exit();
}

// 2. Recibimos el código web por la URL y lo sanitizamos
$codigo = isset($_GET['cod']) ? htmlspecialchars($_GET['cod']) : '';

// 3. Opcional: Validar que el código no esté vacío
if (empty($codigo)) {
    echo "<p style='text-align:center; margin-top:20px;'>Error: Código de documento no proporcionado.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FUT_<?php echo htmlspecialchars($codigo); ?>.pdf</title>
    <style>
        /* Simulamos el fondo oscuro del visor original de Chrome */
        body { 
            margin: 0; 
            padding: 0; 
            background-color: #525659; 
            overflow: hidden; 
        }
        iframe { 
            width: 100vw; 
            height: 100vh; 
            border: none; 
            display: block; 
        }
    </style>
</head>
<body>
    <iframe src="../../controladores/documentos.php?op=generarFUT&cod_web=<?php echo urlencode($codigo); ?>"></iframe>
</body>
</html>