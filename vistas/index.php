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
    <div class="pc-content">

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-4"
                    style="background: linear-gradient(to right, #ffffff, #edf2ff); border-radius: 15px; !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                style="width: 48px; height: 48px; background-color: rgba(14, 95, 187, 0.15); color: #0e5fbb;">
                                <i class="ti ti-school fs-3"></i>
                            </div>

                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1 fw-bold" style="color:#0a2152; letter-spacing: -0.3px;">¡Bienvenido al Mesa de Partes Académico!</h5>
                                <p class="mb-0 text-muted">Gestiona tus solicitudes de forma eficiente y realiza el seguimiento en tiempo real.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1 small fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">TOTAL TRÁMITES</h6>
                                <h4 class="mb-0 fw-bold" id="TotalTramites">0</h4>
                            </div>
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 42px; height: 42px; background-color: rgba(14, 95, 187, 0.15); color: #0e5fbb;">
                                <i class="ti ti-folders fs-4"></i>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 4px; border-radius: 10px;">
                            <div class="progress-bar bg-primary" style="width: 100%; border-radius: 10px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1 small fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">PENDIENTES</h6>
                                <h4 class="mb-0 fw-bold text-warning" id="TotalPendientes">0</h4>
                            </div>
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 42px; height: 42px; background-color: rgba(255, 193, 7, 0.15); color: #ffc107;">
                                <i class="ti ti-calendar-time fs-4"></i>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 4px; border-radius: 10px;">
                            <div class="progress-bar bg-warning" style="width: 45%; border-radius: 10px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1 small fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">OBSERVADOS</h6>
                                <h4 class="mb-0 fw-bold text-danger" id="TotalObservados">0</h4>
                            </div>
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 42px; height: 42px; background-color: rgba(220, 53, 69, 0.15); color: #dc3545;">
                                <i class="ti ti-alert-circle fs-4"></i>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 4px; border-radius: 10px;">
                            <div class="progress-bar bg-danger" style="width: 20%; border-radius: 10px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted mb-1 small fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">FINALIZADOS</h6>
                                <h4 class="mb-0 fw-bold text-success" id="TotalFinalizados">0</h4>
                            </div>
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 42px; height: 42px; background-color: rgba(25, 135, 84, 0.15); color: #198754;">
                                <i class="ti ti-circle-check fs-4"></i>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 4px; border-radius: 10px;">
                            <div class="progress-bar bg-success" style="width: 80%; border-radius: 10px;"></div>
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