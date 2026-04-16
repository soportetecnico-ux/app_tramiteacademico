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
                            <li class="breadcrumb-item" aria-current="page">Nuevo trámite</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- [ breadcrumb ] end --><!-- [ Main Content ] start -->

        <div class="container my-4">
            <form id="formTramiteCompleto">
                <div class="card mb-2 shadow-sm">
                    <div class="card-header py-2 px-3 bg-light">
                        <h6 class="mb-0 fw-bold small text-uppercase" style="color: #0a3e9e;">I. SOLICITO: </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-md-12">
                                <select class="form-select form-select-sm" id="id_tupa">
                                </select>
                            </div>
                        </div>

                        <div id="detalle_tupa" class="mt-2" style="display:none;">
                            <div class="row g-2">
                                <div class="col-md-9">
                                    <div class="p-3 border rounded shadow-sm h-100" style="background-color: #0a2152;">
                                        <small class="text-uppercase fw-bold text-white d-block mb-2" style="font-size: 0.7rem;">
                                            Requisitos del Trámite
                                        </small>
                                        <div id="lbl_requisito" class="text-white fw-medium" style="white-space: pre-wrap;font-size: 0.8rem;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="p-3 border rounded shadow-sm h-100 d-flex flex-column justify-content-center" style="background-color: #0a2152;">
                                        <small class="text-uppercase fw-bold text-white d-block mb-2" style="font-size: 0.7rem;">
                                            Derecho de Pago
                                        </small>
                                        <div id="lbl_monto" class="text-white fw-medium" style="white-space: pre-wrap;font-size: 0.8rem;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-2 shadow-sm">
                    <div class="card-header py-2 px-3 bg-light">
                        <h6 class="mb-0 fw-bold small text-uppercase" style="color: #0a3e9e;">II. DEPENDENCIA O AUTORIDAD A QUIEN SE DIRIGE LA SOLICITUD:</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-md-12">
                                <input type="text"
                                    class="form-control form-control-sm"
                                    id="dependencia"
                                    placeholder="NOMBRE DE DEPENDENCIA"
                                    style="background-color: #f8f9fa; border: 1px solid #dee2e6;" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-2 shadow-sm">
                    <div class="card-header py-2 px-3 bg-light">
                        <h6 class="mb-0 fw-bold small text-uppercase" style="color: #0a3e9e;">III. DERECHO DE TRÁMITE</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="nroComprobante">
                                    <label class="fw-bold">N° COMPROBANTE DE PAGO <span style="color:#db2727;">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="fechaPago">
                                    <label class="fw-bold">FECHA DE PAGO <span style="color:#db2727;">*</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-2 shadow-sm">
                    <div class="card-header py-2 px-3 bg-light">
                        <h6 class="mb-0 fw-bold small text-uppercase" style="color: #0a3e9e;">IV. DATOS DEL SOLICITANTE</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <div class="form-floating"><input type="text" class="form-control" id="apepa" placeholder="Ape." disabled><label class="fw-bold">APELLIDO PATERNO <span style="color:#db2727;">*</span></label></div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating"><input type="text" class="form-control" id="apema" placeholder="Ape." disabled><label class="fw-bold">APELLIDO MATERNO <span style="color:#db2727;">*</span></label></div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating"><input type="text" class="form-control" id="nombres" placeholder="Nom." disabled><label class="fw-bold">NOMBRES <span style="color:#db2727;">*</span></label></div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating"><input type="text" class="form-control" id="dni" placeholder="Dni." disabled><label class="fw-bold">DOCUMENTO DE IDENTIDAD <span style="color:#db2727;">*</span></label></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-2 shadow-sm">
                    <div class="card-header py-2 px-3 bg-light">
                        <h6 class="mb-0 fw-bold small text-uppercase" style="color: #0a3e9e;">V. DIRECCIÓN</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="direccion" placeholder="Dir" disabled>
                                    <label class="fw-bold">DOMICILIO: AV. / CALLE / JIRÓN / DPTO / MZ / LOTE / URB. <span style="color:#db2727;">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="distrito" placeholder="Dist" disabled>
                                    <label class="fw-bold">DISTRITO <span style="color:#db2727;">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="provincia" placeholder="Prov" disabled>
                                    <label class="fw-bold">PROVINCIA <span style="color:#db2727;">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="departamento" placeholder="Dep" disabled>
                                    <label class="fw-bold">DEPARTAMENTO <span style="color:#db2727;">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="correo" placeholder="Email" disabled>
                                    <label class="fw-bold">AUTORIZO SE ME NOTIFIQUE AL CORREO ELECTRÓNICO: <span style="color:#db2727;">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="telefono">
                                    <label class="fw-bold">TELÉFONO: (OPCIONAL)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="celular" placeholder="Cel">
                                    <label class="fw-bold">CELULAR: <span style="color:#db2727;">*</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-2 shadow-sm">
                    <div class="card-header py-2 px-3 bg-light">
                        <h6 class="mb-0 fw-bold small text-uppercase" style="color: #0a3e9e;">VI. FUNDAMENTACIÓN </h6>
                    </div>
                    <div class="card-body p-2">
                        <textarea class="form-control" rows="3" placeholder="Fundamentación..."></textarea>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header py-2 px-3 bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold small text-uppercase" style="color: #0a3e9e;">VII. ANEXOS <span class="text-muted fw-normal" style="font-size: 0.65rem;">(Máx. 10 archivos - 20MB c/u)</span></h6>
                        <button type="button" class="btn btn-outline-primary btn-sm py-0 px-1" onclick="agregarArchivo()" style="font-size: 0.8rem;">
                            <i class="ti ti-layout-grid-add"></i> Agregar
                        </button>
                    </div>
                    <div class="card-body p-2">
                        <div id="listaAnexos">
                            <div class="input-group input-group-sm mb-1 archivo-item">
                                <input type="file" class="form-control form-control-sm" onchange="validarArchivo(this)">
                                <button class="btn btn-outline-danger" type="button" onclick="eliminarFila(this)"><i class="ti ti-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header py-1 px-3 bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold small text-uppercase" style="color: #0a3e9e;">Firma Digital</h6>
                                <button type="button" class="btn btn-primary btn-sm py-0 px-2" onclick="generarFirmaDigital()" style="font-size: 0.75rem;">
                                    <i class="bi bi-pen-fill"></i> Firmar Documento
                                </button>
                            </div>
                            <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center" style="min-height: 100px;">

                                <div id="previewFirmaContainer" class="mb-1" style="display: none;">
                                    <div style="display: flex; align-items: center; padding: 5px; background: #fff; width: fit-content; font-family: Arial, sans-serif; line-height: 1.1;">
                                        <div style="padding-right: 1px;">
                                            <img src="../assets/images/logo-tramite.png" alt="Escudo" style="height: 55px; width: auto;">
                                        </div>

                                        <div style="padding-left: 8px; text-align: left; font-size: 9px; color: #000;">
                                            <div>Firmado digitalmente por <span id="nombreFirma" style="font-weight: bold;"></span></div>
                                            <div style="font-weight: bold; font-size: 11px;"><span id="dniFirma" style="font-weight: bold;"></span></div>
                                            <div>Motivo: Soy el autor del documento</div>
                                            <div>Fecha: <span id="fechaFirma"></span></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-top border-dark w-75 text-center pt-1">
                                    <p class="mb-0 fw-bold" style="font-size: 0.65rem;">FIRMA DEL USUARIO</p>
                                    <p class="text-muted mb-0" style="font-size: 0.6rem;">
                                        SAN VICENTE, <span id="fechaActualAutomatica" class="fw-bold"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header py-2 px-3 bg-light">
                                <h6 class="mb-0 fw-bold small text-uppercase" style="color: #0a3e9e;">Observaciones <span class="fw-normal text-muted" style="font-size: 0.65rem;">(Opcional)</span></h6>
                            </div>
                            <div class="card-body p-1">
                                <textarea class="form-control form-control-sm" id="observaciones" rows="4" placeholder="Digite sus observaciones..." style="border:none; resize: none; font-size: 0.8rem;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ────── BOTONES PRINCIPALES ────── -->
                <div class="d-flex justify-content-end mt-4 mb-3 gap-2">
                    <button type="button" class="btn btn-secondary" onclick="history.back()">
                        <i class="ti ti-arrow-left me-1"></i> Volver
                    </button>
                    <!-- Vista previa: abre modal con PDF generado por Dompdf -->
                    <button type="button" id="btnVistaPreviaFUT"
                        class="btn btn-outline-primary px-4"
                        onclick="abrirVistaPreviaFUT()">
                        <i class="ti ti-file-search me-1"></i> Vista Previa
                    </button>
                    <!-- Enviar trámite directamente (genera PDF + registra en BD) -->
                    <button type="button" id="btnEnviarDirecto"
                        class="btn btn-success px-4 fw-bold"
                        onclick="confirmarEnvioDirecto()">
                        <i class="ti ti-send me-1"></i> Enviar trámite
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- [ Main Content ] end -->

<link rel="stylesheet" href="../assets/css/fut-style.css">

<?php include('includes/fut_modal.php'); ?>

<script src="../scripts/fut-pdf.js"></script>

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script src="../scripts/documentos.js"></script>