<?php

session_start();

require_once(__DIR__ . '/../modelos/Documento.php');

$documentos = new Documento();

$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fech_crea = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {

    case 'seleccionarTramite':
        if (!isset($_SESSION)) {
            session_start();
        }
        $id_car = $_SESSION['id_car'];

        $rspta = $documentos->seleccionarTramite($id_car);
        echo '<option value="" disabled selected>Seleccione una opción</option>';

        while ($reg = $rspta->fetch_object()) {
            // Si no encontró oficina ni específica ni general, ponemos un aviso
            $oficina = !empty($reg->nombre_oficina) ? $reg->nombre_oficina : "OFICINA POR ASIGNAR";
            $cod = !empty($reg->cod_oficina) ? $reg->cod_oficina : "";

            echo '<option value="' . $reg->id_tupa . '" 
                      data-requisito="' . $reg->requisitos . '" 
                      data-monto="' . $reg->monto . '"
                      data-oficina="' . $oficina . '"
                      data-codoficina="' . $cod . '">'
                . $reg->denominacion .
                '</option>';
        }
        break;

    /*  case 'seleccionarTramite':
        $rspta = $documentos->seleccionarTramite();
        echo '<option value="" disabled selected>Seleccione una opción</option>';

        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->id_tupa . '" 
                      data-requisito="' . $reg->requisitos . '" 
                      data-monto="' . $reg->monto . '"
                      data-oficina="' . $reg->nombre . '"
                      data-codoficina="' . $reg->cod_oficina . '">'
                . $reg->denominacion .
                '</option>';
        }
        break; */

    case 'registrarMPV':
        header('Content-Type: application/json');

        try {
            // Generar cod_web único
            $cod_web = time() . rand(10, 99);

            // Procesar archivo si existe
            $nombreArchivo = "";
            if (!empty($_FILES['archivo']['name'])) {
                // Obtener extensión del archivo
                $ext = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
                // Generar nombre con fecha y hora + nombre original
                $nombreArchivo = date("YmdHis") . "_" . basename($_FILES['archivo']['name']);
                $destino = __DIR__ . "/../../views/archivos/" . $nombreArchivo;

                if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $destino)) {
                    echo json_encode([
                        'status' => 'error',
                        'mensaje' => 'No se pudo guardar el archivo. Verifica permisos de la carpeta.'
                    ]);
                    exit;
                }
            }

            // Armar array de datos
            $data = [
                'cod_remitente'  => 0,
                'email'          => $_POST['correo'] ?? '',
                'celular'        => $_POST['celular'] ?? '',
                'direccion'      => $_POST['direccion'] ?? '',
                'cod_tipo_documento'  => $_POST['tipoDocumento'] ?? '',
                'folio'          => $_POST['folio'] ?? 0,
                'numero'         => $_POST['numeroDocumento'] ?? '',
                'asunto'         => $_POST['asunto'] ?? '',
                'mensaje'        => $_POST['mensaje'] ?? '',
                'nombre_archivo' => $nombreArchivo,
                'estudiante'     => $_POST['estudiante'] ?? '',
                'cod_web'        => $cod_web,
                'id_usuario'     => $_SESSION['id_usuario'] ?? '',
            ];

            // Llamar al modelo
            $rspta = $documentos->registrarMPV($data);

            // Asegurarse de que siempre sea un JSON válido
            if (!isset($rspta['status']) || !isset($rspta['mensaje'])) {
                $rspta = [
                    'status' => 'error',
                    'mensaje' => 'Ocurrió un error inesperado al registrar el documento.'
                ];
            }

            echo json_encode($rspta);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Excepción: ' . $e->getMessage()
            ]);
            exit;
        }

    default:
        echo json_encode([
            'status' => 'error',
            'mensaje' => 'Operación no válida'
        ]);
        exit;

    case 'listarMisTramites':
        header('Content-Type: application/json; charset=utf-8');
        $id_usuario = $_SESSION['id_usuario'] ?? 0;

        $data = $documentos->listarMisTramites($id_usuario);

        $tramites = array();
        if ($data) {
            while ($row = mysqli_fetch_assoc($data)) {
                $tramites[] = $row;
            }
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($tramites),
            "iTotalDisplayRecords" => count($tramites),
            "aaData" => $tramites
        );

        echo json_encode($results);
        break;
}
