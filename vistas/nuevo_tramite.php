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

        <div class="container">
            <form id="formTramiteCompleto">
                <div class="card mb-2 shadow-sm">
                    <div class="card-header py-1 px-3 bg-light">
                        <h6 class="mb-0 fw-bold small text-uppercase" style="color: #0a3e9e;">I. IDENTIFICACIÓN DEL SOLICITANTE</h6>
                    </div>
                    <div class="card-body p-2 bg-light-subtle">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="text" class="form-control form-control-sm" id="dni" disabled>
                                    <label class="small fw-bold">DNI/ID</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control form-control-sm" id="nombres_completos" placeholder="Apellidos y Nombres" disabled>
                                    <label class="small fw-bold">APELLIDOS Y NOMBRES</label>
                                </div>
                                <input type="hidden" id="apepa">
                                <input type="hidden" id="apema">
                                <input type="hidden" id="nombres">
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control form-control-sm" id="direccion" disabled>
                                    <label class="small">DOMICILIO ACTUAL</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control form-control-sm" id="ubicacion" placeholder="Distrito / Prov / Dep" disabled>
                                    <label class="small">DISTRITO / PROVINCIA / DEP.</label>
                                </div>
                                <input type="hidden" id="distrito">
                                <input type="hidden" id="provincia">
                                <input type="hidden" id="departamento">
                            </div>

                            <div class="col-md-5">
                                <div class="form-floating">
                                    <input type="email" class="form-control form-control-sm text-primary" id="correo" disabled>
                                    <label class="small fw-bold">CORREO NOTIFICACIÓN</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control form-control-sm" id="celular">
                                    <label class="small">CELULAR</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-2 shadow-sm">
                    <div class="card-header py-1 px-3 bg-light">
                        <h6 class="mb-0 fw-bold small text-uppercase" style="color: #0a3e9e;">II. SOLICITUD Y COMPROBANTE DE PAGO</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="fw-bold small mb-1">TIPO DE SOLICITUD:</label>
                                <select class="form-select form-select-sm fw-bold" id="id_tupa"></select>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small mb-1">DIRIGIDO A:</label>
                                <input type="text" class="form-control form-control-sm bg-light" id="dependencia" disabled>
                            </div>
                        </div>

                        <div id="detalle_tupa" style="display:none;" class="row g-2 mb-2">
                            <div class="col-md-8">
                                <div class="p-2 rounded text-white" style="background-color: #0a2152; font-size: 0.75rem;">
                                    <i class="ti ti-list-check text-info"></i> <strong>REQUISITOS:</strong>
                                    <div id="lbl_requisito" class="mt-1 opacity-75"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 rounded text-center text-white" style="background-color: #0a2152;">
                                    <small class="d-block text-info fw-bold">DERECHO DE PAGO</small>
                                    <span id="lbl_monto" class="fs-6 fw-bold"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-1">

                            <div class="col-md-6">
                                <div class="form-floating"><input type="text" class="form-control" id="nroComprobante"><label class="fw-bold small text-primary">N° COMPROBANTE *</label></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating"><input type="date" class="form-control" id="fechaPago"><label class="fw-bold small text-primary">FECHA DE PAGO *</label></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header py-1 px-3 bg-light">
                                <h6 class="mb-0 fw-bold small">III. FUNDAMENTACIÓN</h6>
                            </div>
                            <div class="card-body p-2">
                                <textarea id="txtFundamentacion" class="form-control" rows="6" style="font-size: 0.85rem;" placeholder="Explique brevemente el motivo de su solicitud..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header py-1 px-3 bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold small">IV. ANEXOS</h6>
                                <button type="button" class="btn btn-primary btn-sm py-0" onclick="agregarArchivo()"><i class="ti ti-plus"></i></button>
                            </div>
                            <div class="card-body p-2">
                                <div id="listaAnexos" style="max-height: 150px; overflow-y: auto;">
                                    <div class="input-group input-group-sm mb-1 archivo-item">
                                        <input type="file" class="form-control" onchange="validarArchivo(this)">
                                        <button class="btn btn-outline-danger" type="button" onclick="eliminarFila(this)"><i class="ti ti-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header py-1 px-3 bg-light d-flex justify-content-between align-items-center" style="height: 40px;">
                                <h6 class="mb-0 fw-bold small">V. FIRMA DIGITAL</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" onclick="generarFirmaDigital()" style="font-size: 0.7rem; height: 24px;">
                                    <i class="fas fa-marker me-1"></i> Estampar Firma
                                </button>
                            </div>
                            <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                                <div id="previewFirmaContainer" style="display: none; align-items: center; background: #fff; font-family: Arial, sans-serif; padding: 6px; width: 242px; box-sizing: border-box; border: 1px solid #eee;">
                                    <div style="padding-right: 10px; flex-shrink: 0; border-right: 1px solid #eee; display: flex; align-items: center; height: 60px;">
                                        <img src="../assets/images/logo-tramite.png" alt="Escudo" style="height: 55px; width: auto; display: block;">
                                    </div>
                                    <div style="padding-left: 10px; text-align: justify; color: #000; line-height: 1.2; flex-grow: 1;">
                                        <div style="font-size: 10px; color: #333; margin-bottom: 2px;">
                                            Firmado digitalmente por: <span id="nombreFirma" style="text-transform: uppercase; display: block; width: 100%;"></span>
                                        </div>
                                        <div style="font-size: 10px; font-weight: bold; margin-bottom: 2px; text-align: left;">
                                            <span id="dniFirma"></span>
                                        </div>
                                        <div style="font-size: 10px; color: #444; text-align: left;">
                                            <div style="margin-bottom: 1px;">Motivo: Soy el autor del documento</div>
                                            <div>Fecha: <span id="fechaFirma"></span></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-top border-dark w-75 text-center pt-1 mt-auto">
                                    <p class="mb-0 fw-bold" style="font-size: 0.65rem;">FIRMA DEL USUARIO</p>
                                    <p class="text-muted mb-0" style="font-size: 0.6rem;">SAN VICENTE, <span id="fechaActualAutomatica" class="fw-bold"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header py-1 px-3 bg-light d-flex align-items-center" style="height: 40px;">
                                <h6 class="mb-0 fw-bold small">VI. OBSERVACIONES (OPCIONAL)</h6>
                            </div>
                            <div class="card-body p-1">
                                <textarea class="form-control form-control-sm h-100" id="observaciones" rows="4" style="border:none; resize: none; min-height: 120px;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3 gap-2">
                    <button type="button" class="btn btn-light border" onclick="history.back()">Volver</button>
                    <button type="button" class="btn btn-outline-primary" onclick="abrirVistaPreviaFUT()"><i class="ti ti-eye"></i> Vista Previa</button>
                    <button type="button" class="btn btn-success px-4" onclick="confirmarEnvioDirecto()"><i class="ti ti-send"></i> Enviar Trámite</button>
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