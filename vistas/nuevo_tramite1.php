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
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="page-header-title">
                                <h4 class="mb-0">Nuevo trámite</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- [ breadcrumb ] end --><!-- [ Main Content ] start -->


        <div class="row justify-content-center mt-0">
            <div class="col-md-12">
                <!-- Un solo form que engloba remitente + documento -->
                <form id="formDocumento">
                    <div class="row g-3">

                        <!-- Columna izquierda: Datos del Remitente -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5>Datos del Remitente</h5>
                                </div>
                                <div class="card-body">

                                    <div class="row g-3">
                                        <!-- DNI/RUC -->
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" id="dniRuc" name="dniRuc" class="form-control form-control-sm" readonly>
                                                <label for="dni" class="form-label">DNI/RUC</label>
                                            </div>
                                        </div>

                                        <!-- Nombre y Apellidos -->
                                        <div class="col-md-8">
                                            <div class="form-floating">
                                                <input type="text" id="nombreApellidos" name="nombreApellidos" class="form-control form-control-sm" readonly>
                                                <label for="nombreApellidos" class="form-label">Nombre y Apellidos</label>
                                            </div>
                                        </div>

                                        <!-- Correo -->
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <input type="email" class="form-control form-control-sm" id="correo" name="correo" readonly>
                                                <label for="correo">Correo</label>
                                            </div>
                                        </div>

                                        <!-- Celular -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control form-control-sm" id="celular" name="celular" readonly>
                                                <label for="celular">Celular</label>
                                            </div>
                                        </div>

                                        <!-- Dirección -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control form-control-sm" id="direccion" name="direccion" readonly>
                                                <label for="direccion">Dirección</label>
                                            </div>
                                        </div>

                                        <!-- Selección Sí / No -->
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <select class="form-select form-select-sm" id="estudiante" name="estudiante" required>
                                                    <option value="" selected disabled>Seleccione</option> <!-- Placeholder -->
                                                    <option value="1">Sí</option>
                                                    <option value="0">No</option>
                                                </select>
                                                <label for="estudiante">¿Es estudiante de la UNDC?</label>
                                            </div>

                                            <div class="alert alert-primary mt-4 mb-0" role="alert">
                                                <h6 class="fw-bold" style="color:#223e7a;">TRÁMITE VIRTUAL</h6>
                                                <small>
                                                    <p style="font-size: 12px;">
                                                        Para empezar un trámite en la UNDC y quienes no tengan un documento que anexar,
                                                        se debe descargar, rellenar y firmar el
                                                        <a href="../assets/documentos/FORMULARIOUNICODETRAMITE.docx" target="_blank" style="text-decoration: underline; color: #0d6efd;">
                                                            Formato Único de Trámite (Descargar FUT)
                                                        </a>
                                                        y adjuntar en el siguiente formulario para ser considerado un trámite válido.
                                                    </p>

                                                    <p style="font-size: 12px;">
                                                        Para los <strong>PEDIDOS DE ACCESO A LA INFORMACIÓN PÚBLICA</strong>, se debe descargar el
                                                        <a href="../assets/documentos/ANEXO 1 - FORMATO DE SOLICITUD DE ACCESO A LA INFORMACIÓN PÚBLICA.docx" target="_blank" style="text-decoration: underline; color: #0d6efd;">
                                                            Formato de Solicitud de Acceso a la Información Pública (Descargar Formato)
                                                        </a>
                                                        y adjuntar en el siguiente formulario para ser considerado un trámite válido.
                                                    </p>

                                                    <p style="font-size: 13px;">
                                                        Disponible 24/7 a través de la
                                                        <a href="https://tramite.undc.edu.pe/mesadepartes/" target="_blank">
                                                            Mesa de Partes Virtual
                                                        </a>.
                                                    </p>
                                                    <p class="mb-3" style="font-size: 13px;">
                                                        Recibirás un <strong>código de seguimiento</strong> por correo electrónico para consultar el estado de tu trámite.
                                                    </p>
                                                </small>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Datos del Documento -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5>Datos del Documento</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-select form-select-sm" id="tipoDocumento" name="tipoDocumento" required>

                                                </select>
                                                <label for="tipoDocumento">Tipo de Documento <span style="color:red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control form-control-sm" id="numeroDocumento" name="numeroDocumento" placeholder="Ingrese el número de documento">
                                                <label for="numeroDocumento">N° de Documento <span style="color:red;">*</span></label>
                                            </div>
                                            <small class="text-muted">Ejemplo: "001", "001/ITM"</small>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="number" class="form-control form-control-sm" id="folio" name="folio" placeholder="Número de folio" required>
                                                <label for="folio">Folio <span style="color:red;">*</span> (N° de páginas)</label>
                                            </div>
                                        </div>
                                        <style>
                                            .form-floating label {
                                                white-space: normal !important;
                                                /* Permite que el texto haga salto de línea */
                                                overflow: hidden;
                                                text-overflow: ellipsis;
                                                font-size: 0.85rem;
                                                /* Un poco más pequeño */
                                                line-height: 1.2;
                                                /* Ajusta altura */
                                            }
                                        </style>

                                        <!-- Asunto -->
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <input type="text" class="form-control form-control-sm" id="asunto" name="asunto" placeholder="Ingrese el asunto" required>
                                                <label for="asunto">Asunto <span style="color:red;">*</span></label>
                                            </div>
                                        </div>

                                        <!-- Mensaje -->
                                        <!-- Textarea -->
                                        <div class="col-md-12">
                                            <label for="mensaje" class="form-label fw-bold">Mensaje</label>
                                            <textarea id="mensaje" name="mensaje" class="form-control form-control-sm" rows="5"></textarea>
                                        </div>

                                        <!-- Adjuntar archivo -->
                                        <div class="col-md-12">
                                            <label for="archivo" class="form-label fw-bold" style="font-size: 0.875rem;">
                                                Adjuntar archivo
                                            </label>
                                            <input class="form-control form-control-sm" type="file" id="archivo" name="archivo" multiple
                                                accept=".pdf,.rar,.zip">
                                            <small class="text-muted">Tamaño máximo permitido: 80 MB (PDF, RAR, ZIP)</small>
                                        </div>

                                        <!-- Tabla archivos cargados -->
                                        <div class="col-md-12 mt-3">
                                            <h6 class="fw-bold">Archivos cargados</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="padding: 0.25rem 0.5rem;">Nombre</th>
                                                            <th style="padding: 0.25rem 0.5rem;" class="text-center">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tablaArchivos">
                                                        <!-- Filas dinámicas -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-3 mb-0 p-3 bg-gray-200 rounded d-flex align-items-center justify-content-between">
                                            <!-- Checkbox -->
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="aceptoTerminos" required>
                                                <label class="form-check-label" for="aceptoTerminos" style="font-size:13px ;">
                                                    Acepto los
                                                    <a href="https://web.undc.edu.pe/terminos-y-condiciones/" target="_blank">Términos y Condiciones</a> y la
                                                    <a href="https://web.undc.edu.pe/pdatospersonales/" target="_blank">Política de privacidad de los datos</a>.
                                                </label>
                                            </div>

                                            <!-- Botón de Enviar -->
                                            <button type="submit" class="btn btn-primary btn-sm px-5">Enviar</button>
                                        </div>



                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para visualizar PDF -->
<div class="modal fade" id="modalPDF" tabindex="-1" aria-labelledby="modalPDFLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- XL para que se vea grande -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPDFLabel">Vista previa del archivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <embed id="visorPDF" src="" type="application/pdf" width="100%" height="600px" />
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




<script src="../js/documentos.js"></script>