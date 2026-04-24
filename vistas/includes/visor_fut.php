<?php
// Recibimos el código web por la URL
$codigo = isset($_GET['cod']) ? $_GET['cod'] : '';
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