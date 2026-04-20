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
        echo '<option value="" disabled selected>Seleccione un trámite</option>';

        while ($reg = $rspta->fetch_object()) {
            $oficina = !empty($reg->nombre_oficina) ? $reg->nombre_oficina : "OFICINA POR ASIGNAR";
            $cod = !empty($reg->cod_oficina) ? $reg->cod_oficina : "";

            // Si no hay anexos, enviamos cadena vacía
            $anexos = !empty($reg->nombres_anexos) ? $reg->nombres_anexos : "";

            echo '<option value="' . $reg->id_tupa . '" 
                    data-requisito="' . $reg->requisitos . '" 
                    data-monto="' . $reg->monto . '"
                    data-oficina="' . $oficina . '"
                    data-codoficina="' . $cod . '"
                    data-anexos="' . $anexos . '">' // <-- NUEVO ATRIBUTO
                . $reg->denominacion .
                '</option>';
        }
        break;

    case 'registrarDocumento':

        if (ob_get_contents()) ob_end_clean();
        header('Content-Type: application/json');

        try {

            $cod_web = "TA-" . date("YmdHis");

            $nombreArchivo = "";
            if (isset($_FILES['archivo_tupa']) && !empty($_FILES['archivo_tupa']['name'][0])) {

                $fileTmpPath = $_FILES['archivo_tupa']['tmp_name'][0];
                $originalName = $_FILES['archivo_tupa']['name'][0];
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));


                $nombre_raiz = pathinfo($originalName, PATHINFO_FILENAME);
                $nombre_raiz = str_replace(' ', '_', $nombre_raiz); // Simula limpia_espacios
                $nombre_raiz = preg_replace('/[^A-Za-z0-9\-]/', '', $nombre_raiz); // Quita caracteres raros
                $nombre_raiz = substr($nombre_raiz, 0, 100); // Lo limitamos un poco más por seguridad

                //Definir carpeta por año
                $anioActual = date("Y");
                $rutaBase = __DIR__ . "/../../views/archivos/" . $anioActual . "/";

                //Crear la carpeta si no existe (permisos 0777)
                if (!file_exists($rutaBase)) {
                    mkdir($rutaBase, 0777, true);
                }

                // Formato: FECHA_HORA_NOMBRERAIZ.ext (Ej: 20260420120530_mi_documento.pdf)
                $nombreArchivo = date("YmdHis") . "_" . $nombre_raiz . "." . $ext;

                $destinoFull = $rutaBase . $nombreArchivo;

                $extensionesPermitidas = ['pdf', 'rar', 'zip'];

                if (!in_array($ext, $extensionesPermitidas)) {
                    echo json_encode(['status' => 'error', 'mensaje' => 'Formato no permitido']);
                    exit;
                }

                if (move_uploaded_file($fileTmpPath, $destinoFull)) {
                    // IMPORTANTE: Guardamos en la BD "2026/nombre_archivo.pdf" para que luego sea fácil encontrarlo desde cualquier vista
                    $nombreArchivoParaBD = $anioActual . "/" . $nombreArchivo;
                } else {
                    echo json_encode(['status' => 'error', 'mensaje' => 'Error al mover archivo']);
                    exit;
                }
            }


            //Enviamos 'cod_oficina' que servirá para la oficina_destino en el historial
            $data = [
                'denominacion'    => $_POST['denominacion'] ?? null,
                'id_estu'         => $_POST['id_estu'] ?? null,
                'id_tupa'         => $_POST['id_tupa'] ?? null,
                'cod_oficina'     => $_POST['cod_oficina'] ?? '',
                'fundamentacion'   => $_POST['fundamentacion'] ?? '', // Se guardará en el campo 'mensaje'
                'nro_comprobante'   => $_POST['nro_comprobante'] ?? '',
                'fecha_comprobante' => $_POST['fecha_comprobante' ?? ''],
                'observaciones' => $_POST['observaciones' ?? ''],
                'cod_web'         => $cod_web,
                'nombre_archivo'  => $nombreArchivoParaBD,
                'firmado_por'       => $_POST['firmado_por'] ?? '',
                'dni_firmante'      => $_POST['dni_firmante'] ?? '',
                'fecha_sello'       => $_POST['fecha_sello'] ?? ''
            ];


            if (empty($data['id_tupa']) || empty($data['id_estu'])) {
                echo json_encode(['status' => 'error', 'mensaje' => 'Faltan datos obligatorios (Tipo de trámite o ID Estudiante).']);
                exit;
            }

            $rspta = $documentos->registrarDocumento($data);

            echo json_encode($rspta);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Excepción en controlador: ' . $e->getMessage()
            ]);
        }
        exit;
        break;

    case 'listarMisTramites':
        header('Content-Type: application/json; charset=utf-8');
        $id_estu = $_SESSION['id_estu'] ?? 0;

        $data = $documentos->listarMisTramites($id_estu);

        $tramites = array();
        if ($data) {
            while ($row = mysqli_fetch_assoc($data)) {
                // Pasamos el row completo, que ya trae 'fecha_formateada' y 'nombre_oficina'
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
