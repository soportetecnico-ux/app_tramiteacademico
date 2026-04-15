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
</style>
<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content"><!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">Bandeja de trámites</li>
                            <li class="breadcrumb-item" aria-current="page">Mis trámites</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Mis trámites</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- [ breadcrumb ] end --><!-- [ Main Content ] start -->


        <div class="row justify-content-center mt-4">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Mis Trámites</h5><span class="d-block m-t-5">Todas mis trámites en Mesa de Partes</span>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaTramites">
                                <thead>
                                    <tr style="font-size: 11px;">
                                        <th>#</th>
                                        <th>FECHA</th>
                                        <th>CODIGO WEB</th>
                                        <th>DOCUMENTO</th>
                                        <th>ASUNTO</th>
                                        <th>ESTADO</th>
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