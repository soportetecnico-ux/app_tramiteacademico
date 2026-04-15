<?php
require_once "config.php";

//Primera conexion
$conexion = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
mysqli_query($conexion, 'SET NAMES "' . DB_ENCODE . '"');

if (mysqli_connect_errno()) {
	printf("Error en la conexión a la base de datos: %s\n", mysqli_connect_error());
	exit();
}

// Segunda conexión
$conexion2 = new mysqli(DB2_HOST, DB2_USERNAME, DB2_PASSWORD, DB2_NAME);
mysqli_query($conexion2, 'SET NAMES "' . DB2_ENCODE . '"');

if ($conexion2->connect_errno) {
	printf("Error conexión 2: %s\n", $conexion2->connect_error);
	exit();
}


if (!function_exists('ejecutarConsulta')) {
	function ejecutarConsulta($sql)
	{
		global $conexion;
		$query = $conexion->query($sql);
		return $query;
	}

	function ejecutarConsultaSimpleFila($sql)
	{
		global $conexion;
		$query = $conexion->query($sql);
		$row = $query->fetch_assoc();
		return $row;
	}

	function ejecutarConsulta_retornarID($sql)
	{
		global $conexion;
		$query = $conexion->query($sql);
		return $conexion->insert_id;
	}

	function limpiarCadena($str)
	{
		global $conexion;
		$str = mysqli_real_escape_string($conexion, trim($str));
		return htmlspecialchars($str);
	}

	/* Segunda conexion */

	function ejecutarConsulta2($sql)
	{
		global $conexion2;
		$query = $conexion2->query($sql);
		return $query;
	}

	function ejecutarConsultaSimpleFila2($sql)
	{
		global $conexion2;
		$query = $conexion2->query($sql);
		$row = $query->fetch_assoc();
		return $row;
	}

	function ejecutarConsulta_retornarID2($sql)
	{
		global $conexion2;
		$query = $conexion2->query($sql);
		return $conexion2->insert_id;
	}

	function limpiarCadena2($str)
	{
		global $conexion2;
		$str = mysqli_real_escape_string($conexion2, trim($str));
		return htmlspecialchars($str);
	}

	
}
