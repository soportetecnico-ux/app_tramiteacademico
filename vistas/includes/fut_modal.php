<!-- ============================================================
     fut_modal.php  — Vista previa del FUT (renderiza PDF de Dompdf en iframe)
     ============================================================ -->
<div class="modal fade" id="modalVistaPreviaFUT" tabindex="-1"
     aria-labelledby="modalFUTLabel" aria-hidden="true"
     data-bs-backdrop="static">

  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <!-- CABECERA -->
      <div class="modal-header bg-primary text-white py-2">
        <h5 class="modal-title fw-bold" id="modalFUTLabel">
          <i class="ti ti-file-text me-2"></i>Vista Previa — Formulario Único de Trámite (FUT)
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- BARRA DE ACCIONES -->
      <div class="modal-body py-2 px-3 bg-light border-bottom d-flex align-items-center gap-2 flex-wrap">
        <span class="text-muted small me-auto">
          <i class="ti ti-info-circle me-1"></i>Revisa los datos. Si todo está correcto, envía el trámite.
        </span>
        <button type="button" class="btn btn-light btn-sm border" data-bs-dismiss="modal">
            Cerrar
        </button>
        <!-- Editar: cierra el modal y vuelve al formulario -->
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
          <i class="ti ti-pencil me-1"></i> Editar datos
        </button>
        <!-- Descargar PDF -->
        <button type="button" class="btn btn-outline-danger btn-sm px-3" id="btnDescargarFUT" onclick="descargarFUT()">
          <i class="ti ti-download me-1"></i> Descargar PDF
        </button>
        <!-- Enviar trámite (acción principal) -->
        <button type="button" class="btn btn-success btn-sm px-4 fw-bold" id="btnEnviarTramite" onclick="enviarTramite()">
          <i class="ti ti-send me-1"></i> Enviar trámite
        </button>
      </div>

      <!-- CUERPO: IFRAME que muestra el PDF generado por Dompdf -->
      <div class="modal-body p-0 bg-secondary bg-opacity-25">
        <!-- Estado de carga -->
        <div id="futPreviewLoader" class="d-flex flex-column align-items-center justify-content-center py-5">
          <div class="spinner-border text-primary mb-3" role="status"></div>
          <span class="text-muted small">Generando vista previa…</span>
        </div>
        <!-- Iframe oculto hasta cargar -->
        <iframe id="iframeFUT"
                src=""
                style="width:100%; height:780px; border:none; display:none;"
                title="Vista previa del FUT">
        </iframe>
      </div>

    </div><!-- /modal-content -->
  </div><!-- /modal-dialog -->
</div><!-- /modal -->
