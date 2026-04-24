<?php
// Validamos que el controlador haya pasado los datos
if (!isset($reg_doc) || !isset($reg_usuario)) {
    exit("Acceso denegado: Datos insuficientes.");
}

require_once __DIR__ . '/../../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;
 
//Datos del Modelo Documento
$id_tupa         = $reg_doc->id_tupa; 
$asunto          = $reg_doc->asunto;
$dependencia    = $reg_doc->oficina;
$nroComprobante = $reg_doc->comprobante;  
$fechaPago      = $reg_doc->fecha_comprobante;  
$fundamentacion = $reg_doc->mensaje;  
// Extraemos la ruta que viene de la BD
$rutaArchivo = $reg_doc->nombre_archivo;
// 1. Quitamos la carpeta 
$soloNombre = basename($rutaArchivo); 
// 2. Cortamos en el primer guion bajo  
$partes = explode('_', $soloNombre, 2); 
// 3. Asignamos el nombre limpio  
$anexos = isset($partes[1]) ? $partes[1] : $soloNombre;
$observaciones  = $reg_doc->observaciones;  
//Datos del Modelo Usuario
$apepa          = $reg_usuario['apepa_estu'];
$apema          = $reg_usuario['apema_estu'];
$nombres        = $reg_usuario['nom_estu'];
$dni            = $reg_usuario['dni_estu'];
$direccion      = $reg_usuario['domi_estu'];
$distrito       = $reg_usuario['dist'];
$provincia      = $reg_usuario['provi'];
$departamento   = $reg_usuario['depar'];
$correo         = $reg_usuario['email_estu'];
$tipo_doc       = $reg_usuario['tipo_docu'];
$telefono       = '';
$celular        = $reg_usuario['celu_estu'];
$fechaPagoFmt = ($fechaPago) ? date("d/m/Y", strtotime($fechaPago)) : '';
// Formatear fecha de pago  YYYY-MM-DD → DD/MM/YYYY
$fechaPagoFmt = '';
if ($fechaPago) {
    [$y, $m, $d] = explode('-', $fechaPago);
    $fechaPagoFmt = "$d/$m/$y";
}
// Fecha actual
$meses = ['','ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
$hoy   = new DateTime('now', new DateTimeZone('America/Lima'));
$fechaFirma = 'SAN VICENTE, ' . $hoy->format('j') . ' DE '
            . $meses[(int)$hoy->format('n')] . ' DE ' . $hoy->format('Y');
// Logo como base64 (Dompdf no accede a rutas del sistema de archivos por defecto)
$logoPath = __DIR__ . '/../../assets/images/logo-tramite.png';
$logoB64  = '';
if (file_exists($logoPath)) {
    $logoB64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}

// Lógica para la firma
if (isset($reg_firma) && $reg_firma) {
    // Si hay firma en BD, extraemos de "24.04.2026 16:54..."
    $dia = substr($reg_firma->fecha_sello, 0, 2);
    $mes_num = (int)substr($reg_firma->fecha_sello, 3, 2);
    $anio = substr($reg_firma->fecha_sello, 6, 4);
    $fechaFirmaTexto = 'SAN VICENTE, ' . $dia . ' DE ' . $meses[$mes_num] . ' DE ' . $anio;
} else {
    // Fallback: Si no hay firma, usa la fecha actual
    $hoy = new DateTime('now', new DateTimeZone('America/Lima'));
    $fechaFirmaTexto = 'SAN VICENTE, ' . $hoy->format('j') . ' DE ' . $meses[(int)$hoy->format('n')] . ' DE ' . $hoy->format('Y');
}
 
/* ─────────────────────────────────────────────────────────────────
   2. HTML DEL FUT  (Dompdf usa un subconjunto de CSS 2.1)
──────────────────────────────────────────────────────────────── */
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>FUT_<?= $reg_doc->cod_web ?>.pdf</title>
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
  <tr><td class="border-top-0 h-20" style="padding-left: 25px;" colspan="2"><?= $asunto ?></td></tr>
  
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
      <span class="chk"><?= ($tipo_doc == 1) ? 'X' : '' ?></span> DNI &nbsp;&nbsp;
      
      <span class="chk"></span> L.E. &nbsp;&nbsp;
      
      <span class="chk"><?= ($tipo_doc == 3) ? 'X' : '' ?></span> C.E. &nbsp;&nbsp;
      
      <span class="chk"><?= (in_array($tipo_doc, [2, 4, 5, 6])) ? 'X' : '' ?></span> OTRO
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
      <?php if (isset($reg_firma) && $reg_firma): ?>
          <table style="width: 260px; margin: 0 auto 5px auto; border: none; border-collapse: collapse;">
              <tr>
                  <td style="width: 65px; border: none; vertical-align: middle; text-align: center; padding-right: 5px;">
                      <?php if ($logoB64): ?>
                          <img src="<?= $logoB64 ?>" style="width: 50px; height: auto;" alt="Escudo">
                      <?php endif; ?>
                  </td>
                  <td style="width: 195px; border: none; border-left: 1px solid #a0a0a0; text-align: left; padding-left: 8px; font-size: 7.5pt; line-height: 1.1;">
                      <span style="color: #444;">Firmado digitalmente por:</span>
                      
                      <div style="width: 140px; text-align: justify; padding-top: 1px; padding-bottom: 1px;">
                          <?= htmlspecialchars($reg_firma->firmado_por) ?>
                      </div>
                      
                      <strong><?= htmlspecialchars($reg_firma->dni_firmante) ?></strong><br>
                      Motivo: <?= htmlspecialchars($reg_firma->motivo) ?><br>
                      Fecha: <?= htmlspecialchars($reg_firma->fecha_sello) ?>
                  </td>
              </tr>
          </table>
      <?php else: ?>
          <div style="height: 60px;"></div>
      <?php endif; ?>

      <div style="width: 90%; margin: 0 auto; border-top: 1px solid #000; padding-top: 3px;">
          <span class="font-bold" style="font-size: 8pt;">FIRMA DEL USUARIO</span><br>
          <span style="font-size: 7.5pt;"><?= $fechaFirmaTexto ?></span><br>
          <span style="font-size: 7.5pt;">(LUGAR Y FECHA)</span>
      </div>
    </td>
    <td class="border-0" idth: 45%;>

    </td>
    
    <td style="width: 45%; padding: 8px;">
      <div class="font-bold mb-2">OBSERVACIONES:</div>
        <div style="font-size: 9pt; width: 100%; display: block; word-wrap: break-word;">
            <?= $observaciones ?: '&nbsp;' ?>
        </div>
    </td>
  </tr>
</table>

</body>
</html>
<?php
$htmlFut = ob_get_clean();
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($htmlFut);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="FUT_'.$reg_doc->cod_web.'.pdf"');
echo $dompdf->output();
exit();
?>