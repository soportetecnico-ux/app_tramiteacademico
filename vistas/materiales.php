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
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Materiales de Apoyo</h5>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-4">

                                <p style="font-size:12px; color:#777;">
                                    Selecciona una opción para visualizar el contenido.
                                </p>

                                <div style="display:flex; flex-direction:column; gap:10px;">

                                    <button onclick="mostrarPDF()" class="btn btn-danger btn-block"
                                        style="border-radius:8px;">
                                        Ver Manual PDF
                                    </button>

                                    <button onclick="mostrarVideo()" class="btn btn-success btn-block"
                                        style="border-radius:8px;">
                                        Ver Video Tutorial
                                    </button>

                                </div>

                            </div>

                            <div class="col-md-8">

                                <div id="previewBox" style="border:1px solid #eee;border-radius:10px;height:600px;overflow:hidden;background:#f9f9f9;">

                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;color:#999;font-size:14px;">
                                        Selecciona PDF o Video
                                    </div>

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




<script src="../scripts/materiales.js"></script>