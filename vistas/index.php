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
<link rel="stylesheet" href="../assets/css/dashboard.css">
<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-4"
                    style="background: linear-gradient(130deg, #1340a8 0%, #2563eb 55%, #4f8ef7 100%); border-radius: 15px !important;">
                    <div class="card-body-banner p-4">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0 shadow-sm"
                                style="width: 54px; height: 54px; background-color: #fff; color: #0e5fbb; border: 2px solid #edf2ff;">
                                <i class="ti ti-school fs-2"></i>
                            </div>

                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-1 fw-bold" style="font-size:1.1rem; font-weight:700; color:#fff; margin-bottom:3px; letter-spacing:-.2px;">¡Bienvenido a la Mesa de Partes Académica!</h5>
                                <p class="mb-0" style="font-size:.85rem; color:rgba(255,255,255);">Gestiona tus solicitudes de forma eficiente y realiza el seguimiento en tiempo real.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-xl-3">
                <div class="stat-card blue shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="text-muted mb-1 fw-bold" style="font-size: 11px; letter-spacing: 0.8px;">TOTAL TRÁMITES</h6>
                                <h3 class="mb-0 fw-bold text-dark" id="totalTramites">0</h3>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 48px; height: 48px; background-color: rgba(14, 95, 187, 0.1); color: #0e5fbb;">
                                <i class="ti ti-folders fs-4"></i>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-fill" id="pb-total"></div>
                        </div>
                        <p class="stat-footer">Total actual <span id="f-total">0</span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stat-card amber shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="text-muted mb-1 fw-bold" style="font-size: 11px; letter-spacing: 0.8px;">EN PROCESO</h6>
                                <h3 class="mb-0 fw-bold text-warning" id="totalPendientes">0</h3>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 48px; height: 48px; background-color: rgba(255, 193, 7, 0.1); color: #ffc107;">
                                <i class="ti ti-calendar-time fs-4"></i>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-fill" id="pb-pendientes"></div>
                        </div>
                        <p class="stat-footer">Del total <span id="f-proc">89%</span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stat-card red shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="text-muted mb-1 fw-bold" style="font-size: 11px; letter-spacing: 0.8px;">OBSERVADOS</h6>
                                <h3 class="mb-0 fw-bold text-danger" id="totalObservados">0</h3>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 48px; height: 48px; background-color: rgba(220, 53, 69, 0.1); color: #dc3545;">
                                <i class="ti ti-alert-circle fs-4"></i>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-fill" id="pb-observados"></div>
                        </div>
                        <p class="stat-footer">Requieren acción <span id="f-obs">0%</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card green shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="text-muted mb-1 fw-bold" style="font-size: 11px; letter-spacing: 0.8px;">FINALIZADOS</h6>
                                <h3 class="mb-0 fw-bold text-success" id="totalFinalizados">0</h3>
                            </div>
                            <div class="icon-wrapper d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 48px; height: 48px; background-color: rgba(25, 135, 84, 0.1); color: #198754;">
                                <i class="ti ti-circle-check fs-4"></i>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-fill" id="pb-finalizados"></div>
                        </div>
                        <p class="stat-footer">Completados <span id="f-fin">11%</span></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- CAJA DE GRAFICO DE DISTRIBUCIÓN DE TRAMITES CON DONUT -->
        <div class="row g-4 mt-2">
            <div class="col-md-6 col-sm-12"> 
                <div class="panel h-100 shadow-sm border-0">
                    <p class="panel-title">Distribución de Trámites</p>
                    <div class="donut-wrap">
                        <svg class="donut-svg" width="110" height="110" viewBox="0 0 110 110">
                            <circle cx="55" cy="55" r="42" fill="none" stroke="#e4eaf5" stroke-width="13" />
                            <circle id="arc-proc" cx="55" cy="55" r="42" fill="none" stroke="#e8a020" stroke-width="13" stroke-dasharray="0 263.6" stroke-linecap="round" transform="rotate(-90 55 55)" style="transition: stroke-dasharray 1.3s cubic-bezier(0.4,0,0.2,1)" />
                            <circle id="arc-fin" cx="55" cy="55" r="42" fill="none" stroke="#0f9e6e" stroke-width="13" stroke-dasharray="0 263.6" stroke-linecap="round" transform="rotate(-90 55 55)" style="transition: stroke-dasharray 1.3s 0.15s cubic-bezier(0.4,0,0.2,1)" />
                            <circle id="arc-obs" cx="55" cy="55" r="42" fill="none" stroke="#d63251" stroke-width="13" stroke-dasharray="0 263.6" stroke-linecap="round" transform="rotate(-90 55 55)" style="transition: stroke-dasharray 1.3s 0.3s cubic-bezier(0.4,0,0.2,1)" />
                            <text x="55" y="51" text-anchor="middle" font-family="DM Sans" font-size="18" font-weight="700" fill="#0d1b3e" id="donut-center">9</text>
                            <text x="55" y="65" text-anchor="middle" font-family="DM Sans" font-size="9" fill="#6b7a99">trámites</text>
                        </svg>
                        <div class="donut-legend">
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#e8a020;"></div>
                                <span class="legend-label">En Proceso</span>
                                <span class="legend-val"></span><span class="legend-pct"></span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#0f9e6e;"></div>
                                <span class="legend-label">Finalizados</span>
                                <span class="legend-val"></span><span class="legend-pct"></span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#d63251;"></div>
                                <span class="legend-label">Observados</span>
                                <span class="legend-val"></span><span class="legend-pct"></span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#1a4fba;"></div>
                                <span class="legend-label">Total</span>
                                <span class="legend-val"></span><span class="legend-pct">(100%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="panel h-100 shadow-sm border-0">
                    <p class="panel-title">Actividad Reciente</p>
                    <ul class="activity-list">
                        </ul>
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
<script src="../scripts/documentos.js"></script>