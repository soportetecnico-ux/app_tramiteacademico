/**
 * fut-pdf.js  (versión Dompdf)
 */

/* ─── Recolectar datos del formulario ──────────────────────── */
function recolectarDatos() {
    const inputs = document.querySelectorAll('#listaAnexos input[type="file"]');
    const anexosArr = [];
    inputs.forEach((inp, i) => {
        if (inp.files && inp.files[0])
            anexosArr.push((i + 1) + '. ' + inp.files[0].name);
    });

    const fundTa = document.querySelector('#formTramiteCompleto textarea');

    return {
        id_tupa:       val('id_tupa'),
        dependencia:    val('dependencia'),
        nroComprobante: val('nroComprobante'),
        fechaPago:      val('fechaPago'),
        apepa:          val('apepa'),
        apema:          val('apema'),
        nombres:        val('nombres'),
        dni:            val('dni'),
        direccion:      val('direccion'),
        distrito:       val('distrito'),
        provincia:      val('provincia'),
        departamento:   val('departamento'),
        correo:         val('correo'),
        telefono:       val('telefono'),
        celular:        val('celular'),
        fundamentacion: fundTa ? fundTa.value.trim() : '',
        anexos:         anexosArr.join('\n'),
        observaciones:  val('observaciones'),
        firma:          (document.getElementById('imgFirma')?.src?.startsWith('data:image'))
                        ? document.getElementById('imgFirma').src
                        : '',
    };
}

/* ─── Helper: leer valor de un elemento ──────────────────────── */
function val(id) {
    const el = document.getElementById(id);
    return el ? (el.value || el.innerText || '').trim() : '';
}

/* ─── Validación mínima antes de abrir preview ─────────────── */
function abrirVistaPreviaFUT() {
    const id_tupa = val('id_tupa');
    const apepa    = val('apepa');
    const dni      = val('dni');

    if (!id_tupa) {
        alert('⚠ Por favor selecciona un tipo de solicitud (campo I).');
        document.getElementById('id_tupa').focus();
        return;
    }
    if (!apepa || !dni) {
        alert('⚠ Los datos del solicitante no están cargados (campo IV).');
        return;
    }

    // Mostrar modal y cargar PDF en iframe
    const modal = new bootstrap.Modal(document.getElementById('modalVistaPreviaFUT'));
    modal.show();
    cargarPreviewEnIframe();
}

/* ─── Cargar preview en iframe (GET ?accion=preview) ───────── */
function cargarPreviewEnIframe() {
    const loader = document.getElementById('futPreviewLoader');
    const iframe = document.getElementById('iframeFUT');

    console.log("Iniciando generación de vista previa...");

    // 1. Estado inicial: Mostrar loader (con important para evitar conflictos con Bootstrap)
    loader.style.setProperty('display', 'flex', 'important');
    iframe.style.display = 'none';

    // 2. Limpieza de memoria: Liberar URL anterior si existe
    if (iframe.src && iframe.src.startsWith('blob:')) {
        URL.revokeObjectURL(iframe.src);
    }
    iframe.src = '';

    const datos = recolectarDatos();

    fetch('includes/generar_fut.php?accion=preview', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(datos),
    })
    .then(res => {
        console.log("Respuesta del servidor recibida. Status:", res.status);
        if (!res.ok) throw new Error('Error en el servidor: ' + res.status);
        return res.blob();
    })
    .then(blob => {
        console.log("Blob creado con éxito, tamaño:", blob.size);
        const url = URL.createObjectURL(blob);

        // 3. Ocultar loader inmediatamente (usando important)
        loader.style.setProperty('display', 'none', 'important');
        
        // 4. Mostrar iframe y cargar el PDF
        iframe.style.display = 'block';
        iframe.src = url;
        
        console.log("PDF asignado al iframe y loader ocultado.");
    })
    .catch(err => {
        // En caso de error, también debemos ocultar el loader
        loader.style.setProperty('display', 'none', 'important');
        console.error("Fallo en la carga:", err);
        alert('Error al generar vista previa: ' + err.message);
    });
}

/* ─── Descargar PDF (GET ?accion=descargar) ─────────────────── */
function descargarFUT() {
    const btn   = document.getElementById('btnDescargarFUT');
    const datos = recolectarDatos();
    const dni   = datos.dni || 'SIN_DNI';

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generando…';

    fetch('includes/generar_fut.php?accion=descargar', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(datos),
    })
    .then(res => {
        if (!res.ok) throw new Error('Error al descargar el PDF.');
        return res.blob();
    })
    .then(blob => {
        const url  = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href     = url;
        link.download = 'FUT_' + dni + '.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    })
    .catch(err => {
        console.error(err);
        alert('❌ Error al descargar el PDF. Intente de nuevo.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-download me-1"></i> Descargar PDF';
    });
}

/* ─── Enviar trámite (guarda en servidor + registra en BD) ──── */
function enviarTramite() {
    const btn   = document.getElementById('btnEnviarTramite');
    const datos = recolectarDatos();

    // Validación final
    if (!datos.id_tupa || !datos.apepa || !datos.dni) {
        alert('⚠ Completa los campos obligatorios antes de enviar.');
        return;
    }

    if (!confirm('¿Confirmas el envío del trámite? Una vez enviado no podrás editarlo.')) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando…';

    // 1. Generar y guardar el FUT PDF en el servidor
    fetch('includes/generar_fut.php', {          // sin ?accion → guarda y retorna JSON
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(datos),
    })
    .then(res => res.json())
    .then(resp => {
        if (!resp.ok) throw new Error(resp.msg || 'Error al guardar el FUT.');

        // 2. Registrar el trámite en la base de datos con la ruta del PDF guardado
        return fetch('includes/registrar_tramite.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({
                ...datos,
                ruta_fut: resp.ruta,
                nombre_fut: resp.nombre,
            }),
        });
    })
    .then(res => res.json())
    .then(resp => {
        if (!resp.ok) throw new Error(resp.msg || 'Error al registrar el trámite.');

        // Éxito: cerrar modal y redirigir a bandeja
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalVistaPreviaFUT'));
        modal?.hide();

        alert('✅ Trámite enviado correctamente.\nN° de expediente: ' + (resp.nro_expediente || '—'));
        window.location.href = 'bandeja_tramites.php';
    })
    .catch(err => {
        console.error(err);
        alert('❌ ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-send me-1"></i> Enviar trámite';
    });
}
