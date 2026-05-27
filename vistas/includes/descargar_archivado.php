<?php
session_start();

// Seguridad
if (!isset($_SESSION['sistema_academico']['id_estu'])) {
    die("Acceso denegado.");
}

// Archivo
$archivo = $_GET['file'] ?? '';


$directorioBase = realpath('../../../views/archivos/archivo_archivado/'); 
$rutaArchivo = realpath($directorioBase . '/' . $archivo);

if ($rutaArchivo && strpos($rutaArchivo, $directorioBase) === 0 && file_exists($rutaArchivo)) {

    $mimeType = mime_content_type($rutaArchivo);

    header('Content-Type: ' . $mimeType);


    header('Content-Disposition: inline; filename="' . basename($rutaArchivo) . '"');

    header('Content-Length: ' . filesize($rutaArchivo));

    readfile($rutaArchivo);
    exit;

} else {
    echo "❌ Archivo no encontrado: " . htmlspecialchars($archivo);
}
?>