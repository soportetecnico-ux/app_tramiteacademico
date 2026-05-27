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
<div class="pc-container">
    <div class="pc-content"><!-- [ breadcrumb ] start -->



        <div class="row justify-content-center mt-2">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Mis Trámites</h5><span class="d-block m-t-5">Todas mis trámites en Mesa de Partes</span>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table id="tablaTramites" class="table table-hover align-middle w-100 border-0">
                                <thead class="bg-light">
                                    <tr style="letter-spacing: 0.5px;">
                                        <th class="text-center px-3 border-0 text-uppercase text-muted">#</th>
                                        <th class="border-0 text-uppercase text-muted">FECHA EMISIÓN</th>
                                        <th class="border-0 text-uppercase text-muted">CÓDIGO WEB</th>
                                        <th class="border-0 text-uppercase text-muted">ASUNTO</th>
                                        <th class="border-0 text-uppercase text-muted">DEPENDENCIA DESTINO</th>
                                        <th class="border-0 text-uppercase text-muted">FUT</th>
                                        <th class="border-0 text-uppercase text-muted">ADJUNTO</th>
                                        <th class="border-0 text-uppercase text-muted">ESTADO</th>
                                        <th class="border-0 text-uppercase text-muted">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
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