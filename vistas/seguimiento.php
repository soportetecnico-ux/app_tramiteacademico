<?php
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION['id_estu'])) {
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
                            <span class="badge bg-light-success text-primary p-2" style="font-size: 14px; border: 1px dashed">
                                <i class="fas fa-fingerprint me-1"></i>
                                <strong><?php echo ($cod_web != '') ? $cod_web : 'SIN CÓDIGO'; ?></strong>
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive py-3">
                            <table id="tablaSeguimiento" class="table table-hover align-middle w-100" >
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
                            
                            <div id="contenedorDetallesTramite" class="p-3 border rounded-3 bg-light-subtle">
                                <p class="text-muted small mb-0 text-center">Seleccione un movimiento para ver detalles adicionales.</p>
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