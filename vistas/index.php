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
        

        <div class="row mt-0"><!-- [ sample-page ] start -->


            <div class="row mt-0">
                <div class="col-md-12">
                    <div class="alert alert-secondary" role="alert" style="background-color:#edf2ff; border-color: #0e5fbb;">
                        <strong style="color:#0e5fbb;">¡Bienvenido al Mesa de Partes Académico de la Universidad!</strong><br>
                        Este sistema está diseñado para gestionar de manera eficiente los trámites académicos y garantizar un flujo organizado de solicitudes.
                        Estamos comprometidos con la mejora continua y con ofrecerte una experiencia óptima. ¡Gracias por confiar en nuestra gestión administrativa!
                    </div>
                </div>
                <!-- Card 1 -->

            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-5 g-4 mt-2">
                <!-- Total Condiciones -->
                <div class="col">
                    <div class="card social-widget-card available-balance-card" style="background-color: #1b7bdc;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-white m-0" id="TotalCondiciones"></h3>
                                <span class="m-t-10 text-white">Condiciones</span>
                            </div>
                            <span class="pc-micon">
                                <svg class="pc-icon" style="width: 24px; height: 24px;">
                                    <use xlink:href="#custom-element-plus"></use>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Total Componentes -->
                <div class="col">
                    <div class="card social-widget-card available-balance-card" style="background-color: #1b7bdc;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-white m-0" id="TotalComponentes"></h3>
                                <span class="m-t-10 text-white">Componentes</span>
                            </div>
                            <span class="pc-micon">
                                <svg class="pc-icon" style="width: 24px; height: 24px;">
                                    <use xlink:href="#custom-element-plus"></use>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Total Indicadores -->
                <div class="col">
                    <div class="card social-widget-card available-balance-card" style="background-color: #1b7bdc;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-white m-0" id="TotalIndicadores"></h3>
                                <span class="m-t-10 text-white">Indicadores</span>
                            </div>
                            <span class="pc-micon">
                                <svg class="pc-icon" style="width: 24px; height: 24px;">
                                    <use xlink:href="#custom-element-plus"></use>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Total Medios -->
                <div class="col">
                    <div class="card social-widget-card available-balance-card" style="background-color: #1b7bdc;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-white m-0" id="TotalMedios"></h3>
                                <span class="m-t-10 text-white">Medios de Verificación</span>
                            </div>
                            <span class="pc-micon">
                                <svg class="pc-icon" style="width: 24px; height: 24px;">
                                    <use xlink:href="#custom-element-plus"></use>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Total Evidencias -->
                <div class="col">
                    <div class="card social-widget-card available-balance-card" style="background-color: #1b7bdc;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-white m-0" id="TotalEvidencias"></h3>
                                <span class="m-t-10 text-white">Evidencias</span>
                            </div>
                            <span class="pc-micon">
                                <svg class="pc-icon" style="width: 24px; height: 24px;">
                                    <use xlink:href="#custom-element-plus"></use>
                                </svg>
                            </span>
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

