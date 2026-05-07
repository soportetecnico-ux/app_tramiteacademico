<?php
session_start();

// 1. Verificación de seguridad: ¿Está logueado?
if (!isset($_SESSION['sistema_academico']['id_estu'])) {
    die("Acceso denegado. Por favor, inicie sesión.");
}

// 2. Obtener el nombre del archivo de forma segura
$archivo = $_GET['file'] ?? '';


$directorioBase = realpath('../../../views/archivos/'); 
$rutaArchivo = realpath($directorioBase . '/' . $archivo);

if ($rutaArchivo && strpos($rutaArchivo, $directorioBase) === 0 && file_exists($rutaArchivo)) {
    
    // Obtener información para los headers
    $mimeType = mime_content_type($rutaArchivo);
    
    // Configurar headers para descarga
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
    header('Content-Length: ' . filesize($rutaArchivo));
    header('Pragma: no-cache');
    
    // Entregar el archivo
    readfile($rutaArchivo);
    exit;
} else {
    die("Error: Archivo no encontrado o acceso no autorizado.");
}
?>