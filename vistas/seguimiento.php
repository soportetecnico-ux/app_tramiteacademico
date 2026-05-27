<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['sistema_academico']['id_estu'])) {
    header("Location: ../index.php");
    exit();
}
?>


<?php
include('head.php')
?>
<style>
    .choices__list--dropdown {
        z-index: 1051 !important;
        position: absolute;
    }

    thead th {
        font-size: 12px !important;
        text-transform: uppercase;
        /* Para que se vea más profesional */
    }
</style>
<!-- [ Main Content ] start -->
<?php
// Recibimos el código web enviado por POST
$cod_web = $_POST['cod_web'] ?? '';
?>

<div class="pc-container">
    <div class="pc-content">
        <div class="row justify-content-center mt-2">
            <div class="col-md-12">
                <div class="card" id="contenedorSeguimiento" data-codweb="<?php echo htmlspecialchars($cod_web, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Seguimiento de Trámite</h5>
                            <span class="d-block m-t-5">Historial de movimientos</span>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light-primary text-primary p-2" style="font-size: 14px; border: 1px dashed">
                                <i class="fas fa-fingerprint me-1"></i>
                                <strong><?php echo ($cod_web != '') ? $cod_web : 'SIN CÓDIGO'; ?></strong>
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive py-3">
                            <table id="tablaSeguimiento" class="table table-hover align-middle w-100">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 text-uppercase text-muted" style="font-size: 11px; font-weight: 700;">Asunto</th>
                                        <th class="border-0 text-uppercase text-muted" style="font-size: 11px; font-weight: 700;">Fecha de Envío</th>
                                        <th class="border-0 text-uppercase text-muted" style="font-size: 11px; font-weight: 700;">Destino</th>
                                        <th class="border-0 text-uppercase text-muted text-center" style="font-size: 11px; font-weight: 700;">Estado</th>
                                    </tr>
                                </thead>
                                <tbody style="background-color: transparent;">
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <div id="contenedorDetallesTramite" class="p-3 border rounded-3">
                                <p class="small mb-0 text-center">Seleccione un movimiento para ver detalles adicionales.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPDF" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h6 class="modal-title">
          <i id="modalIcon" class="fas fa-file"></i> Vista del archivo
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row">

          <!-- IZQUIERDA -->
          <div class="col-md-9 d-flex justify-content-center align-items-center">

            <iframe id="iframePDF" style="
                width:100%;
                height:75vh;
                border-radius:6px;
                border:1px solid #ddd;
                display:none;
            "></iframe>

            <div id="zipContainer" style="display:none; text-align:center;">
              <i class="fas fa-file-archive" style="
                  font-size:100px;
                  color:#fd7e14;
              "></i>
              <p class="mt-2 text-muted">Archivo comprimido (sin vista previa)</p>
            </div>

          </div>

          <!-- DERECHA -->
          <div class="col-md-3">
            <div class="card shadow-sm">
              <div class="card-body">

                <div class="mb-2">
                  <label class="fw-bold small">Estado:</label><br>
                  <span id="pdfEstado" class="badge bg-secondary"></span>
                </div>

                <div class="mb-2">
                  <label class="fw-bold small">Fecha de atención:</label><br>
                  <span id="pdfOficina" class="badge bg-light text-dark border"></span>
                </div>

                <div class="mb-2">
                  <label class="fw-bold small">Comentario:</label>
                  <div id="pdfComentario" class="small text-muted"></div>
                </div>

                <div class="mt-3">
                  <a id="pdfDescargar" target="_blank" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-download"></i> Descargar
                  </a>
                </div>

              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- [ Main Content ] end -->
<?php
include('footer.php')
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>




<script src="../scripts/documentos.js"></script>