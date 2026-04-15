<?php
/**
 * generar_fut.php
 * Genera el FUT en PDF usando Dompdf y lo guarda en el servidor.
 *
 * GET ?accion=preview    → devuelve PDF inline (para iframe de vista previa)
 * GET ?accion=descargar  → descarga el PDF
 * (sin accion)           → guarda el PDF y retorna JSON
 *
 * Requiere Dompdf: composer require dompdf/dompdf
 */

if (strlen(session_id()) < 1) session_start();
if (!isset($_SESSION['id_estu'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'No autenticado']);
    exit();
}

require_once __DIR__ . '/../../vendor2/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

/* ─────────────────────────────────────────────────────────────────
   1. RECIBIR Y SANITIZAR DATOS
──────────────────────────────────────────────────────────────── */
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) $data = $_POST;

function s(string $key, array $data): string {
    return htmlspecialchars(trim($data[$key] ?? ''), ENT_QUOTES, 'UTF-8');
}

$id_tupa       = s('id_tupa',       $data);
$dependencia    = s('dependencia',    $data);
$nroComprobante = s('nroComprobante', $data);
$fechaPago      = s('fechaPago',      $data);   // YYYY-MM-DD
$apepa          = s('apepa',          $data);
$apema          = s('apema',          $data);
$nombres        = s('nombres',        $data);
$dni            = s('dni',            $data);
$direccion      = s('direccion',      $data);
$distrito       = s('distrito',       $data);
$provincia      = s('provincia',      $data);
$departamento   = s('departamento',   $data);
$correo         = s('correo',         $data);
$telefono       = s('telefono',       $data);
$celular        = s('celular',        $data);
$fundamentacion = s('fundamentacion', $data);
$anexos         = s('anexos',         $data);
$observaciones  = s('observaciones',  $data);
$firmaBase64    = $data['firma'] ?? '';         // data:image/... base64

// Formatear fecha de pago  YYYY-MM-DD → DD/MM/YYYY
$fechaPagoFmt = '';
if ($fechaPago) {
    [$y, $m, $d] = explode('-', $fechaPago);
    $fechaPagoFmt = "$d/$m/$y";
}

// Fecha actual en español
$meses = ['','ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO',
          'JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
$hoy   = new DateTime('now', new DateTimeZone('America/Lima'));
$fechaFirma = 'SAN VICENTE, ' . $hoy->format('j') . ' DE '
            . $meses[(int)$hoy->format('n')] . ' DE ' . $hoy->format('Y');

// Logo como base64 (Dompdf no accede a rutas del sistema de archivos por defecto)
$logoPath = __DIR__ . '/../../assets/images/logo-tramite.png';
$logoB64  = '';
if (file_exists($logoPath)) {
    $logoB64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}

// Validar firma base64
$firmaImg = (str_starts_with($firmaBase64, 'data:image')) ? $firmaBase64 : '';

/* ─────────────────────────────────────────────────────────────────
   2. HTML DEL FUT  (Dompdf usa un subconjunto de CSS 2.1)
──────────────────────────────────────────────────────────────── */
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: "Helvetica", "Arial", sans-serif;
    font-size: 10pt;
    color: #000;
    background: #fff;
    padding: 15px; /* Margen interno para emular los bordes de la página */
  }

  /* ── Encabezado ── */
  .header-table { width: 100%; border: none; margin-bottom: 20px; }
  .header-table td { border: none; vertical-align: middle; }
  .logo-cell { width: 80px; padding-left: 120px; }
  .logo-cell img { height: 70px; width: auto; }
  .title-cell { text-align: center; }
  .uni-title { font-size: 14pt; font-weight: bold; margin-bottom: 5px; }
  .doc-title { font-size: 12pt; }

  /* ── Estructura Principal de Tablas ── */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px; /* Separación exacta entre secciones */     
  }
  th, td {
    border: 1px solid #000;
    padding: 5px 8px;
    vertical-align: top;
    font-size: 9pt;
  }
  th {
    text-align: left;
   }

  /* ── Utilidades Tipográficas ── */
  .font-bold { font-weight: bold; }
  .text-center { text-align: center; }
  
  /* ── Alturas fijas para áreas de escritura ── */
  .h-30 { height: 30px; }
  .h-60 { height: 60px; }
  .h-100 { height: 100px; }

  /* ── Checkbox simulado ── */
  .chk {
    display: inline-block;
    width: 10px;
    height: 10px;
    border: 1px solid #000;
    margin-right: 3px;
    text-align: center;
    line-height: 10px;
    font-size: 9px;
  }
  .sl {
    background:#e8e8e8;
    border-bottom:1px solid #000;
    font-weight:bold;
   }
  /* ── Eliminar bordes internos donde sea necesario ── */
  .border-0 { border: none !important; }
  .border-top-0 { border-top: none !important; }
</style>
</head>
<body>

<table class="header-table">
  <tr>
    <td class="logo-cell">
      <?php if ($logoB64): ?>
      <img src="<?= $logoB64 ?>" alt="Logo UNDC">
      <?php endif; ?>
    </td>
    <td class="title-cell">
      <div class="uni-title">UNIVERSIDAD NACIONAL DE CAÑETE</div>
      <div class="doc-title">FORMULARIO ÚNICO DE TRÁMITE (FUT)</div>
    </td>
    <td style="width: 80px;"></td> </tr>
</table>

<table>
  <tr><th class="font-bold sl" colspan="2">I. SOLICITO:</th></tr>
  <tr><td class="border-top-0 h-20" style="padding-left: 25px;" colspan="2"><?= $id_tupa ?></td></tr>
  
  <tr><th class="font-bold sl" colspan="2">II. DEPENDENCIA O AUTORIDAD A QUIEN SE DIRIGE LA SOLICITUD:</th></tr>
  <tr><td class="border-top-0 h-20" style="padding-left: 25px;" colspan="2"><?= $dependencia ?></td></tr>

  <tr><th colspan="2" class="font-bold">III. DERECHO DE TRÁMITE (opcional)</th></tr>
  <tr>
    <th width="50%" class="font-bold sl text-center">N° COMPROBANTE DE PAGO</th>
    <th width="50%" class="font-bold sl text-center">FECHA DE PAGO</th>
  </tr>
  <tr>
    <td width="50%"><?= $nroComprobante ?: '&nbsp;' ?></td>
    <td width="50%"><?= $fechaPagoFmt ?: '&nbsp;' ?></td>
  </tr>
</table>

 <table>
  <tr><th colspan="4" class="font-bold text-left">IV. DATOS DEL SOLICITANTE:</th></tr>
  
  <tr>
    <th colspan="3" class="font-bold sl">PERSONA NATURAL</th>
    <th colspan="1" class="font-bold sl text-center">DOCUMENTO DE IDENTIDAD</th>
  </tr>
  <tr class="text-center font-bold" style="font-size: 8.5pt;">
    <th width="20%" class="sl">Apellido Paterno</th>
    <th width="20%" class="sl">Apellido Materno</th>
    <th width="20%" class="sl">Nombres</th>
    <th width="40%" class="sl text-center" style="white-space: nowrap;">
      <span class="chk">X</span> DNI &nbsp;&nbsp;
      <span class="chk"></span> L.E. &nbsp;&nbsp;
      <span class="chk"></span> C.E. &nbsp;&nbsp;
      <span class="chk"></span> OTRO
    </th>
  </tr>
  <tr class="text-center h-30" style="vertical-align: middle;">
    <td><?= $apepa ?></td>
    <td><?= $apema ?></td>
    <td><?= $nombres ?></td>
    <td><?= $dni ?></td>
  </tr>

  <tr>
    <th colspan="3" class="font-bold sl">PERSONA JURÍDICA</th>
    <th colspan="1" class="font-bold sl text-center">RUC</th>
  </tr>
  <tr>
    <td class="font-bold" style="vertical-align: middle; width: 100%;" colspan="4">Razón Social</td>
  </tr>
  <tr>
     <td colspan="3" class="h-30" style="vertical-align: middle; text-align: center;">
       <span style="color: #666;">------------------------------------------------------</span>
    </td>
    <td class="font-bold" style="vertical-align: middle; width: 40%;">N°</td>
  </tr>
</table>

<table>
  <tr><th colspan="3" class="font-bold">V. DIRECCION:</th></tr>
  <tr>
    <td colspan="3" class="font-bold sl text-center">DOMICILIO: AV. /CALLE / JIRÓN / DPTO. / MZ. / LOTE / URB.</td>
  </tr>
  <tr>
    <td colspan="3" class="text-center"><?= $direccion ?></td>
  </tr>
  <tr>
    <td width="33%" class="font-bold sl">DISTRITO </td>
    <td width="33%" class="font-bold sl">PROVINCIA </td>
    <td width="34%" class="font-bold sl">DEPARTAMENTO </td>
  </tr>
  <tr>
    <td width="33%"><?= $distrito ?></td>
    <td width="33%"> <?= $provincia ?></td>
    <td width="34%"><?= $departamento ?></td>
  </tr>
  <tr>
    <td colspan="1" style="vertical-align: middle;">
      <span class="font-bold">Autorizo se me notifique al siguiente correo electrónico:</span><br><?= $correo ?>
    </td>
    <td class="font-bold">TELÉFONO: <br> <?= $telefono ?> </td>
    <td class="font-bold">O CELULAR: <br><?= $celular ?></td>
  </tr>
</table>

<table>
  <tr><th class="font-bold">VI. FUNDAMENTACIÓN DE LA SOLICITUD:</th></tr>
  <tr><td class="h-100" style="white-space: pre-wrap; padding-left: 25px;"><?= $fundamentacion ?></td></tr>
</table>

<table>
  <tr><th>VII. ANEXOS (Relación de Documentos y Anexos que se adjunta:</th></tr>
  <tr><td class="h-60" style="white-space: pre-wrap; padding-left: 25px;"><?= $anexos ?></td></tr>
</table>

<table class="border-0" style="margin-top: 10px;">
  <tr>
    <td class="text-center" style="width: 50%; vertical-align: bottom; padding-bottom: 0;">
      <div style="height: 80px; text-align: center; margin-bottom: 5px;">
        <?php if ($firmaImg): ?>
          <img src="<?= $firmaImg ?>" alt="Firma" style="max-height: 75px; max-width: 250px;">
        <?php endif; ?>
      </div>
      <div style="width: 80%; margin: 0 auto; border-top: 1px solid #000; padding-top: 5px;">
        <span class="font-bold">FIRMA DEL USUARIO</span><br>
        <span style="font-size: 8pt;"><?= $fechaFirma ?></span><br>
        <span style="font-size: 8pt;">(LUGAR Y FECHA)</span>
      </div>
    </td>
    <td class="border-0" idth: 45%;>

    </td>
    
    <td style="width: 45%; padding: 8px;">
      <div class="font-bold mb-2">OBSERVACIONES:</div>
      <div style="font-size: 9pt; white-space: pre-wrap;"><?= $observaciones ?: '&nbsp;' ?></div>
    </td>
  </tr>
</table>

</body>
</html>
<?php
$htmlFut = ob_get_clean();

/* ─────────────────────────────────────────────────────────────────
   3. GENERAR PDF CON DOMPDF
──────────────────────────────────────────────────────────────── */
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', false);   // imágenes embebidas como base64
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($htmlFut, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

/* ─────────────────────────────────────────────────────────────────
    4. RESPUESTA SEGÚN ACCIÓN (Control de flujo)
──────────────────────────────────────────────────────────────── */
$accion = $_GET['accion'] ?? 'guardar';

// Si es Vista Previa o Descarga, enviamos el PDF y detenemos el script (EXIT)
if ($accion === 'preview' || $accion === 'descargar') {
    header('Content-Type: application/pdf');
    
    if ($accion === 'preview') {
        header('Content-Disposition: inline; filename="PREVIEW_FUT.pdf"');
    } else {
        header('Content-Disposition: attachment; filename="FUT_' . ($dni ?: 'DOCUMENTO') . '.pdf"');
    }
    
    echo $dompdf->output();
    exit(); // IMPORTANTE: Aquí se detiene, evitando que se guarde en el servidor
}

/* ─────────────────────────────────────────────────────────────────
    5. GUARDAR EN SERVIDOR (Solo ocurre si la acción es 'guardar')
──────────────────────────────────────────────────────────────── */
$idEstu   = $_SESSION['id_estu'];
$fechaDir = $hoy->format('Y/m');
$dirBase  = __DIR__ . "/../storage/tramites/{$idEstu}/{$fechaDir}";

// Crear carpeta si no existe
if (!is_dir($dirBase)) {
    mkdir($dirBase, 0755, true);
}

// Nombre único para el archivo final
$nombreArchivo = 'FUT_' . $idEstu . '_' . $hoy->format('Ymd_His') . '.pdf';
$rutaCompleta  = $dirBase . '/' . $nombreArchivo;
$rutaRelativa  = "storage/tramites/{$idEstu}/{$fechaDir}/{$nombreArchivo}";

// Guardar el PDF físicamente en el servidor
file_put_contents($rutaCompleta, $dompdf->output());

// Responder al JS con JSON para que continúe con el registro en BD
echo json_encode([
    'ok'     => true,
    'ruta'   => $rutaRelativa,
    'nombre' => $nombreArchivo,
    'msg'    => 'FUT generado y guardado correctamente.',
]);
?>