let datosUsuarioGlobal = null;

$(document).ready(function () {
    cargarDatosUsuario();
    listarTramites();

    const $contenedor = $('#contenedorSeguimiento');

    if ($contenedor.length) {
        // 2. Extraer el código que PHP inyectó en el atributo data
        const codWeb = $contenedor.attr('data-codweb');

        if (codWeb) {
            tablaSeguimiento(codWeb);
        } else {
            console.error("No se pudo obtener el CodWeb del servidor.");
        }
    }

    $.post("../controladores/documentos.php?op=seleccionarTramite", function (r) {
        if (r.trim() == "") {
            console.log("El controlador devolvió una cadena vacía.");
        }
        $("#id_tupa").html(r);
    });
    $("#id_tupa").on("change", function () {
        const opt = $(this).find('option:selected');
        const nombreTramite = opt.text();

        // 1. OBTENER DATOS DE LA OPCIÓN SELECCIONADA
        const requisito = opt.data('requisito');
        const monto = opt.data('monto');
        const oficina = opt.data('oficina');
        const codOficina = opt.data('codoficina');

        if (requisito !== undefined) {
            // --- LÓGICA DE DETALLES DEL TRÁMITE ---
            $("#lbl_requisito").text(requisito);
            $("#lbl_monto").text(monto);
            $("#dependencia").val(oficina);
            $("#dependencia").attr("data-cod", codOficina);
            $("#detalle_tupa").fadeIn(300);

            // Actualizar el texto de la fundamentación según el trámite
            actualizarPlantillaFundamentacion(nombreTramite);

        } else {
            // --- SI NO HAY NADA SELECCIONADO (Opción por defecto) ---
            $("#detalle_tupa").hide();
            $("#dependencia").val("");
            $("#lbl_requisito").text("");
            $("#lbl_monto").text("");

            actualizarPlantillaFundamentacion("[SELECCIONE UN TRÁMITE]");
        }
    });
});

//NUEVO DOCUMENTO

function cargarDatosUsuario() {
    $.ajax({
        url: "../controladores/usuarios.php?op=obtenerDatosUsuario",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {

                datosUsuarioGlobal = response.data;

                const data = datosUsuarioGlobal;

                $('#dni').val(data.dni_estu ?? '');
                $('#nombres').val(`${data.nom_estu ?? ''}`);
                $('#apepa').val(`${data.apepa_estu ?? ''}`);
                $('#apema').val(`${data.apema_estu ?? ''}`);
                $('#correo').val(data.email_estu ?? '');
                $('#celular').val(data.celu_estu ?? '');
                $('#direccion').val(data.domi_estu ?? '');
                $('#departamento').val(data.depar ?? '');
                $('#provincia').val(data.provi ?? '');
                $('#distrito').val(data.dist ?? '');
                $('#ubicacion').val(
                    [data.dist, data.provi, data.depar]
                        .filter(Boolean)
                        .join(', ')
                );
                $('#nombres_completos').val(
                    `${data.apepa_estu ?? ''} ${data.apema_estu ?? ''} ${data.nom_estu ?? ''}`.trim()
                );

                $('#nombreFirma').text(
                    `${data.apepa_estu ?? ''} ${data.apema_estu ?? ''} ${data.nom_estu ?? ''}`.trim()
                );
                $('#dniFirma').text(data.dni_estu ?? '');

                actualizarPlantillaFundamentacion("[SELECCIONE UN TRÁMITE]");
            } else {
                console.error("Error:", response.mensaje);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

function actualizarPlantillaFundamentacion(nombreTramite) {
    if (!datosUsuarioGlobal) return;

    const data = datosUsuarioGlobal;

    // Convertimos a Mayúsculas para resaltar, ya que textarea no soporta negritas HTML
    const nombreCompleto = `${data.apepa_estu ?? ''} ${data.apema_estu ?? ''} ${data.nom_estu ?? ''}`.trim().toUpperCase();
    const codigo = data.cod_estu ?? '-------';
    const dni = data.dni_estu ?? '--------';

    // Limpiamos y resaltamos el trámite
    const tramiteLimpio = nombreTramite.replace(/^\d+\s*-\s*/, '').toUpperCase();

    // La plantilla mantiene un formato limpio y profesional
    const plantilla = `Yo, ${nombreCompleto}, identificado(a) con DNI N° ${dni} y código de estudiante N° ${codigo}, ante usted con el debido respeto me presento y expongo:

Que, por convenir a mis intereses académicos, solicito se me otorgue el/la: ${tramiteLimpio}. Por lo expuesto, pido a usted acceder a mi solicitud por ser de justicia.

Sin otro particular, me despido de usted expresándole las muestras de mi especial consideración y estima personal.`;

    $('#txtFundamentacion').val(plantilla);
}


function validarArchivo(input) {
    const limiteMB = 20 * 1024 * 1024; // 20 MB en bytes
    if (input.files && input.files[0]) {
        if (input.files[0].size > limiteMB) {
            alert("El archivo es demasiado pesado. El máximo es 20 MB.");
            input.value = ""; // Limpiar el input
        }
    }
}


function confirmarEnvioDirecto() {

    // --- I. VALIDACIÓN DE DATOS DEL SOLICITANTE ---
    const celular = $("#celular").val().trim();
    if (celular === "") {
        Swal.fire({ title: "Campo requerido", text: "Por favor, ingrese un número de celular de contacto.", icon: "warning", width: '380px' });
        return;
    }

    // --- II. VALIDACIÓN DE SOLICITUD Y PAGO ---
    const idTupa = $("#id_tupa").val();
    if (!idTupa) {
        Swal.fire({ title: "Campo requerido", text: "Debe seleccionar un Tipo de Solicitud.", icon: "warning", width: '380px' });
        return;
    }

    const nroComprobante = $("#nroComprobante").val().trim();
    if (nroComprobante === "") {
        Swal.fire({ title: "Campo requerido", text: "Ingrese el número de comprobante de pago.", icon: "warning", width: '380px' });
        return;
    }
    // 2. Validar longitud
    if (nroComprobante.length < 6 || nroComprobante.length > 15) {
        Swal.fire({ title: "Voucher inválido", text: "El número debe tener entre 6 y 15 caracteres.", icon: "error", width: '380px' });
        return;
    }
    // 3. Validar longitud de observación
    if (observaciones.length > 250) {
        Swal.fire({ title: "Texto muy largo", text: "La observación no puede superar los 250 caracteres.", icon: "error", width: '380px' });
        return;
    }

    const fechaComprobante = $("#fechaComprobante").val();
    if (fechaComprobante === "") {
        Swal.fire({ title: "Campo requerido", text: "Seleccione la fecha en la que realizó el pago.", icon: "warning", width: '380px' });
        return;
    }
    const hoy = new Date().toISOString().split('T')[0]; // Obtiene fecha actual YYYY-MM-DD
    if (fechaComprobante > hoy) {
    Swal.fire({ title: "Fecha inválida", text: "La fecha de pago no puede ser mayor a la fecha actual.", icon: "error", width: '380px' });
    return;
}

    // --- III. VALIDACIÓN DE FUNDAMENTACIÓN ---
    const fundamentacion = $("#txtFundamentacion").val().trim();
    if (fundamentacion.length < 10) {
        Swal.fire({ title: "Fundamentación", text: "Explique brevemente el motivo de su trámite (mínimo 10 caracteres).", icon: "warning", width: '380px' });
        return;
    }

    // --- IV. VALIDACIÓN DE ANEXOS ---
    let hayArchivo = false;
    $("input[name='archivo_tupa[]']").each(function () {
        if ($(this).val() !== "") {
            hayArchivo = true;
        }
    });

    if (!hayArchivo) {
        Swal.fire({ title: "Anexo requerido", text: "Debe adjuntar al menos un archivo.", icon: "warning", width: '380px' });
        return;
    }

    // --- V. VALIDACIÓN DE FIRMA DIGITAL ---
    const firmaVisible = $("#previewFirmaContainer").is(":visible");
    const nombreFirma = $("#nombreFirma").text().trim();

    if (!firmaVisible || nombreFirma === "") {
        Swal.fire({
            title: "Firma requerida",
            text: "Debe hacer clic en el botón 'Estampar Firma' antes de enviar.",
            icon: "error",
            width: '380px'
        });
        return;
    }

    // --- VI. PREPARACIÓN DE DATOS (BLINDAJE DE DISABLED) ---
    let formElement = document.getElementById("formTramiteCompleto");
    let formData = new FormData(formElement);

    /** * IMPORTANTE: Los campos 'disabled' no entran automáticamente en FormData.
     * Los agregamos manualmente aquí. Aunque el usuario los habilite por consola,
     * el script capturará lo que esté en el valor del input al momento del envío.
     */
    formData.append("id_estu", $("#id_estu").val());
    formData.append("dni", $("#dni").val());
    formData.append("nombres_completos", $("#nombres_completos").val());
    formData.append("correo", $("#correo").val());
    formData.append("direccion", $("#direccion").val());
    formData.append("celular", celular);
    formData.append("id_tupa", idTupa);
    formData.append("denominacion", $("#id_tupa option:selected").text());
    formData.append("cod_oficina", $("#dependencia").attr("data-cod") || "");
    formData.append("fundamentacion", fundamentacion);
    formData.append("nro_comprobante", nroComprobante);
    formData.append("fecha_comprobante", fechaComprobante);
    formData.append("observaciones", $("#observaciones").val());

    // Datos de la firma
    formData.append("firmado_por", nombreFirma);
    formData.append("dni_firmante", $("#dniFirma").text().replace("DNI: ", "").trim());
    formData.append("fecha_sello", $("#fechaFirma").text().trim());

    // --- VII. CONFIRMACIÓN Y ENVÍO ---
    Swal.fire({
        title: '¿Confirmar envío?',
        text: "Su solicitud será enviada a la dependencia correspondiente.",
        icon: 'question',
        width: '380px',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Sí, enviar trámite',
        cancelButtonText: 'Revisar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../controladores/documentos.php?op=registrarDocumento",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    Swal.fire({
                        title: 'Procesando...',
                        width: '300px',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                },
                success: function (response) {
                    try {
                        let res = (typeof response === 'string') ? JSON.parse(response) : response;
                        if (res.status === "success") {
                            Swal.fire({ title: "¡Éxito!", text: "Código: " + res.cod_web, icon: "success", width: '380px' })
                                .then(() => { location.reload(); });
                        } else {
                            Swal.fire({ title: "Error", text: res.mensaje || "Error desconocido", icon: "error", width: '380px' });
                        }
                    } catch (e) {
                        Swal.fire({ title: "Error de respuesta", text: "El servidor respondió con un formato incorrecto.", icon: "error", width: '380px' });
                    }
                },
                error: function (xhr) {
                    Swal.fire({ title: "Error de conexión", text: "No se pudo conectar con el servidor. Intente nuevamente.", icon: "error", width: '380px' });
                }
            });
        }
    });
}

function generarFirmaDigital() {
    const container = document.getElementById('previewFirmaContainer');
    if (container) container.style.display = 'flex'; // Usamos flex para mantener el alineado de la imagen

    const ahora = new Date();

    const nombre = $("#nombres_completos").val() || "USUARIO";
    const dni = $("#dni").val() || "";

    $("#nombreFirma").text(nombre);
    $("#dniFirma").text(dni);

    const dia = String(ahora.getDate()).padStart(2, '0');
    const mes = String(ahora.getMonth() + 1).padStart(2, '0');
    const anio = ahora.getFullYear();
    const hora = String(ahora.getHours()).padStart(2, '0');
    const min = String(ahora.getMinutes()).padStart(2, '0');
    const seg = String(ahora.getSeconds()).padStart(2, '0');

    const fechaSelloFormateada = `${dia}.${mes}.${anio} ${hora}:${min}:${seg} -05:00`;
    const elFechaFirma = document.getElementById('fechaFirma');
    if (elFechaFirma) elFechaFirma.innerText = fechaSelloFormateada;

    const opcionesFecha = { day: 'numeric', month: 'long', year: 'numeric' };
    let fechaLarga = ahora.toLocaleDateString('es-PE', opcionesFecha);

    const elFechaAuto = document.getElementById('fechaActualAutomatica');
    if (elFechaAuto) {
        elFechaAuto.innerText = `${fechaLarga.toUpperCase()}`;
    }

    Swal.fire({
        icon: 'success',
        title: 'Firma estampada correctamente',
        width: '350px', // Mantenemos el tamaño mediano que te gusta
        showConfirmButton: false,
        timer: 1500,
        position: 'center', // Centrado total
        customClass: {
            popup: 'rounded-4'
        }
    });
}

function listarTramites() {
    $('#tablaTramites').DataTable({
        destroy: true,
        responsive: true,
        autoWidth: false,
        ajax: {
            url: "../controladores/documentos.php?op=listarMisTramites",
            type: "GET",
            dataSrc: "aaData"
        },
        columns: [
            {
                // Índice con estilo circular sutil
                data: null,
                className: "text-center align-middle",
                render: function (data, type, row, meta) {
                    return `<span class="badge rounded-pill bg-light text-dark fw-medium px-2" style="font-size:12px;">${meta.row + 1}</span>`;
                }
            },
            {
                // Fecha con icono y estilo muted
                data: "fecha", // Asegúrate que el SQL devuelva este alias
                className: "align-middle",
                render: function (data) {
                    return `
                        <div class="d-flex align-items-center">
                            <span class="text-secondary fw-medium" style="font-size: 12px;">${data}</span>
                        </div>`;
                }
            },
            {
                // Código Web estilo "Tag" elegante
                data: "cod_web",
                className: "align-middle",
                render: function (data) {
                    return `<span class="badge bg-light-secondary" style="letter-spacing: 0.5px; font-size: 12px;">${data}</span>`;
                }
            },
            {
                // Asunto con tipografía limpia y truncado inteligente
                data: "asunto",
                className: "align-middle",
                render: function (data) {
                    return `
                        <div class="d-flex align-items-center">
                            <span class="text-muted small text-uppercase" style="font-size: 12px;">${data}</span>
                        </div>`;
                }
            },
            {
                // Oficina destino con estilo institucional
                data: "nombre_oficina",
                className: "align-middle",
                render: function (data) {
                    return `
                        <div class="d-flex align-items-center">
                            <span class="text-muted small text-uppercase" style="font-size: 12px;">${data}</span>
                        </div>`;
                }
            },
            {
                // Columna para Vista Previa del FUT
                data: "cod_web", // Usamos cod_web como referencia
                className: "text-center align-middle",
                render: function (data) {
                    return `
                        <button onclick="generarFUT('${data}')" 
                                class="btn btn-sm btn-light-primary border" 
                                style="font-size: 11px; padding: 4px 8px; border-radius: 6px;"
                                title="Ver Formulario FUT">
                            <i class="fas fa-file-invoice me-1"></i> FUT
                        </button>`;
                }
            },
            {
                data: "nombre_archivo",
                className: "align-middle",
                render: function (data) {
                    if (!data) return '<span class="text-muted small">Sin archivo</span>';

                    const extension = data.split('.').pop().toLowerCase();

                    // Extrae solo "nombre.pdf" ignorando "academicos/2026/"
                    const nombreReal = data.split('/').pop();

                    // Cortamos el nombre si es muy largo para que el botón no se deforme
                    const nombreCorto = nombreReal.length > 10
                        ? nombreReal.substring(0, 7) + "..."
                        : nombreReal;

                    let icono = "fas fa-file-alt";
                    let color = "text-secondary";

                    if (extension === 'pdf') {
                        icono = "fas fa-file-pdf";
                        color = "text-danger";
                    } else if (extension === 'zip' || extension === 'rar') {
                        icono = "fas fa-file-archive";
                        color = "text-warning";
                    }

                    // OJO AQUÍ: Si data ya trae "academicos/2026/...", 
                    // solo retrocedemos hasta la carpeta 'archivos' o donde inicie la ruta.
                    const rutaArchivo = `../../views/archivos/${data}`;

                    return `
            <div class="d-inline-block">
                <a href="${rutaArchivo}" download 
                   class="d-flex align-items-center text-decoration-none p-1 px-2" 
                   style="background-color: #f1f3f5; border-radius: 6px; border: 1px solid #e9ecef; transition: all 0.2s;"
                   title="${nombreReal}"> 
                    
                    <i class="${icono} ${color} me-2" style="font-size: 1.1rem;"></i>
                    
                    <div class="d-flex flex-column" style="line-height: 1.1;">
                        <span class="text-dark fw-bold" style="font-size: 0.6rem; white-space: nowrap;">${nombreCorto}</span>
                        <span class="text-muted" style="font-size: 0.5rem;">${extension.toUpperCase()}</span>
                    </div>
                </a>
            </div>`;
                }
            },
            {

                data: "estado",
                className: "align-middle",
                render: function (data) {
                    return `
                        <div class="d-flex align-items-center">
                            <span class="badge text-bg-success" style="font-size: 12px;">${data}</span>
                        </div>`;
                }
            },
            {

                data: "cod_web",
                className: "text-center align-middle",
                render: function (data) {
                    return `
                        <button onclick="irASeguimiento('${data}')" 
                                class="btn btn-shadow btn-primary btn-sm px-3" style="font-size:12px;">Seguimiento
                        </button>`;
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
            paginate: {
                next: '<i class="ti ti-chevron-right"></i>',
                previous: '<i class="ti ti-chevron-left"></i>'
            }
        },
        // Personalización del DOM para que se vea limpio
        dom: '<"d-flex flex-wrap justify-content-between align-items-center mb-4"lf>rt<"d-flex flex-wrap justify-content-between align-items-center mt-4"ip>'
    });
}
function irASeguimiento(codWeb) {
    // Creamos un formulario virtual
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "seguimiento.php"; // Página de destino

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "cod_web";
    input.value = codWeb;

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit(); // Saltamos a la siguiente página
}

function tablaSeguimiento(codWeb) {
    $('#tablaSeguimiento').DataTable({
        destroy: true,
        responsive: true,
        ordering: false,
        ajax: {
            url: "../controladores/documentos.php?op=mostrarSeguimientoInicial",
            type: "GET",
            data: { cod_web: codWeb },
            dataSrc: "aaData"
        },
        columns: [
            {
                data: "asunto",
                className: "ps-4",
                render: data => `<span class="text-muted" style="font-size: 12px;">${data.toUpperCase()}</span>`
            },
            {
                data: "fecha",
                render: data => `<span class="text-muted" style="font-size: 12px;">${data.toUpperCase()}</span>`
            },
            {
                data: "nombre_oficina",
                render: data => `<span class="badge bg-light text-primary border fw-semibold" style="font-size: 11px;">${data}</span>`
            },
            {
                data: "estado",
                className: "text-center",
                render: function (data) {
                    let bg = data === 'Derivado' ? 'bg-success' : 'bg-warning';
                    return `<span class="badge ${bg} shadow-sm" style="font-size: 11px; padding: 5px 12px;">${data}</span>`;
                }
            }
        ],
        // EVENTO: Cuando la tabla termine de cargar, inyectamos el diseño "moderno" abajo
        initComplete: function (settings, json) {
            if (json.aaData && json.aaData.length > 0) {
                const primerRegistro = json.aaData[0]; // Tomamos el movimiento más reciente
                generarVistaDetalle(primerRegistro);
            }
        },
        language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
        dom: 'rt'
    });
}

function generarVistaDetalle(data) {
    console.log("DEPURACIÓN DE DATA:", data);

    const html = `
        <div class="mb-4">
            <h6 class="text-primary fw-bold mb-3" style="font-size: 13px; letter-spacing: 0.5px;">DATOS PRINCIPALES DEL TRÁMITE</h6>
            <div class="row g-0">
                <div class="col-md-6 border-bottom py-2 d-flex align-items-center">
                    <span class="fw-bold text-dark me-2" style="min-width: 140px; font-size: 13px;">Expediente:</span>
                    <span class="text-muted" style="font-size: 13px;">${data.cod_documento || '---'}</span>
                </div>
                <div class="col-md-6 border-bottom py-2 d-flex align-items-center ps-md-4">
                    <span class="fw-bold text-dark me-2" style="min-width: 100px; font-size: 13px;">Asunto:</span>
                    <span class="text-muted text-uppercase" style="font-size: 13px;">${data.asunto || '---'}</span>
                </div>
                
                <div class="col-md-6 border-bottom py-2 d-flex align-items-center">
                    <span class="fw-bold text-dark me-2" style="min-width: 140px; font-size: 13px;">Nro de documento:</span>
                    <span class="text-muted" style="font-size: 13px;">${ (data.num_doc || data.numero) ? String(data.num_doc || data.numero).padStart(3, '0') : '---' }</span>
                </div>
                <div class="col-md-6 border-bottom py-2 d-flex align-items-center ps-md-4">
                    <span class="fw-bold text-dark me-2" style="min-width: 100px; font-size: 13px;">Estado:</span>
                    <span class="text-muted" style="font-size: 13px;">${data.estado || '---'}</span>
                </div>
            </div>

            <div class="row g-2 align-items-stretch mt-4">
        <div class="col-md-2">
            <div class="h-100 p-2 border-start border-secondary border-4 bg-white shadow-sm rounded-end">
                <strong class="d-block text-primary fw-bold text-uppercase mb-1" style="font-size: 10px;">N° Proveído:</strong>
                <span class="fw-bold text-dark d-block small">${data.n_proveido || '---'}</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="h-100 p-2 border-start border-secondary border-4 bg-white shadow-sm rounded-end">
                <strong class="text-muted d-block fw-bold text-uppercase" style="font-size: 10px;">Origen:</strong>
                <span class="fw-bold text-dark d-block small text-truncate" title="${data.nombre_oficina_origen}">${data.nombre_oficina_origen || '---'}</span>
                <div class="mt-1 pt-1 border-top" style="font-size: 10px;">
                    <span class="text-muted d-block"><i class="far fa-calendar-alt me-1"></i>Envío: ${data.fecha || '---'}</span>
                    <span class="text-muted d-block"><i class="fas fa-mobile-alt me-1"></i>Cel: ${data.celular_origen || '---'}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="h-100 p-2 border-start border-secondary border-4 bg-white shadow-sm rounded-end">
                <strong class="text-muted d-block fw-bold text-uppercase" style="font-size: 10px;">Destino:</strong>
                <span class="fw-bold text-dark d-block small text-truncate" title="${data.nombre_oficina}">${data.nombre_oficina || '---'}</span>
                <div class="mt-1 pt-1 border-top" style="font-size: 10px;">
                    <span class="text-muted d-block"><i class="far fa-calendar-check me-1"></i>Recibo: ${data.fecha_recepcion || '---'}</span>
                    <span class="text-muted d-block"><i class="fas fa-mobile-alt me-1"></i>Cel: ${data.celular_destino || '---'}</span>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="h-100 p-2 border-start border-secondary border-4 bg-white shadow-sm rounded-end">
                <strong class="d-block text-primary fw-bold text-uppercase mb-1" style="font-size: 10px;">Estado:</strong>
                <div class="d-flex align-items-center h-50">
                    <span class="badge bg-light-info text-info border border-info w-100" style="font-size: 10px;">
                        ${data.estado2 || data.estado}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="h-100 p-2 border-start border-secondary border-4 bg-white shadow-sm rounded-end">
                <strong class="d-block text-primary fw-bold text-uppercase mb-1" style="font-size: 10px;">Comentario:</strong>
                <div style="max-height: 45px; overflow-y: auto;">
                    <span class="small text-muted lh-sm d-block" style="font-size: 10px;">
                        ${data.comentario || 'Sin observaciones.'}
                    </span>
                </div>
            </div>
        </div>
    </div>
    `;
    // Asegúrate de que este ID sea el que tienes en tu HTML para mostrar los detalles
    $('#contenedorDetallesTramite').html(html);
}

function generarFUT(cod_web) {
    // Agregamos "includes/" a la ruta
    const url = `includes/visor_fut.php?cod=${cod_web}`; 
    
    const width = 1000;
    const height = 800;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;

    window.open(url, 'Vista FUT', 
        `width=${width},height=${height},top=${top},left=${left},scrollbars=yes`);
}

/* function irASeguimiento(codWeb) {
    // Creamos un formulario dinámicamente
    var form = document.createElement("form");
    form.method = "POST";
    form.action = "seguimiento.php";

    // Creamos el input que contendrá el cod_web
    var input = document.createElement("input");
    input.type = "hidden";
    input.name = "cod_web";
    input.value = codWeb;

    form.appendChild(input);
    document.body.appendChild(form);

    // Enviamos el formulario
    form.submit();
}

// documentos.js

function mostrarSeguimiento(codWeb) {
    console.log("Iniciando seguimiento para:", codWeb); // Para que veas en consola que llegó
    
    $('#tablaSeguimiento').DataTable({
        destroy: true,
        responsive: true,
        ajax: {
            url: "../controladores/documentos.php?op=mostrarSeguimiento",
            type: "GET",
            data: { cod_web: codWeb }, 
            dataSrc: "aaData"
        },
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: "remitente" },
            { data: "asunto", className: "small text-uppercase" },
            { data: "enviado_por" },
            { 
                data: "fecha",
                render: data => `<span class="text-secondary" style="font-size: 12px;">${data}</span>`
            },
            { data: "nombre_oficina" },
            { 
                data: "estado",
                render: function (data) {
                    const colores = {
                        'Pendiente': 'bg-warning',
                        'Derivado': 'bg-success',
                        'Finalizado': 'bg-primary'
                    };
                    let badgeClass = colores[data] || 'bg-secondary';
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            }
        ],
        language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
        dom: 'rt'
    });
} */