let datosUsuarioGlobal = null;

$(document).ready(function () {
    cargarDatosUsuario();
    listarTramites();
    if ($('#contenedorDashboard').length > 0) {
        listarConteoDocs();
        listarActividadReciente();
    }
     

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

        //Añadimos para capturar el valor del trámite seleccionado, para usarlo en la verificación en SIVIRENO
        const idTupa = $(this).val();

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
            // --- NUEVO: LÓGICA DE VERIFICACIÓN EN SIVIRENO ---
            verificarTramiteEstu(idTupa);

        } else {
            // --- SI NO HAY NADA SELECCIONADO (Opción por defecto) ---
            $("#detalle_tupa").hide();
            $("#dependencia").val("");
            $("#lbl_requisito").text("");
            $("#lbl_monto").text("");

            actualizarPlantillaFundamentacion("[SELECCIONE UN TRÁMITE]");
            // NUEVO: Deshabilitar botón si no hay trámite seleccionado
            $("#btnEnviar").prop("disabled", true);
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
        width: '350px',
        showConfirmButton: false,
        timer: 1500,
        position: 'center',
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

                data: null,
                className: "text-center align-middle",
                render: function (data, type, row, meta) {
                    return `<span class="badge rounded-pill bg-light text-dark fw-medium px-2" style="font-size:12px;">${meta.row + 1}</span>`;
                }
            },
            {

                data: "fecha",
                className: "align-middle",
                render: function (data) {
                    return `
                        <div class="d-flex align-items-center">
                            <span class="text-secondary fw-medium" style="font-size: 12px;">${data}</span>
                        </div>`;
                }
            },
            {
                data: "cod_web",
                className: "align-middle",
                render: function (data) {
                    return `<span class="badge bg-light-secondary" style="letter-spacing: 0.5px; font-size: 12px;">${data}</span>`;
                }
            },
            {
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

                    let texto = '';
                    let clase = '';

                    switch (parseInt(data)) {
                        case 0:
                            texto = 'En Proceso';
                            clase = 'text-bg-warning';
                            break;
                        case 1:
                            texto = 'Finalizado';
                            clase = 'text-bg-success';
                            break;
                        case 2:
                            texto = 'Observado';
                            clase = 'text-bg-danger';
                            break;
                        default:
                            texto = 'SIN ESTADO';
                            clase = 'text-bg-secondary';
                    }

                    return `
            <div class="d-flex align-items-center">
                <span class="badge ${clase}" style="font-size: 12px;">
                    ${texto}
                </span>
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

        dom: '<"d-flex flex-wrap justify-content-between align-items-center mb-4"lf>rt<"d-flex flex-wrap justify-content-between align-items-center mt-4"ip>'
    });
}
function irASeguimiento(codWeb) {

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "seguimiento.php";

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "cod_web";
    input.value = codWeb;

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
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
                render: function (data, type, row) {

                    let texto = '';
                    let clase = '';
                    let boton = '';

                    switch (parseInt(data)) {
                        case 0:
                            texto = 'En Proceso';
                            clase = 'text-bg-warning';
                            break;

                        case 1:
                            texto = 'Finalizado';
                            clase = 'text-bg-success';
                            break;

                        case 2:
                            texto = 'Observado';
                            clase = 'text-bg-danger';
            
                            break;

                        default:
                            texto = 'SIN ESTADO';
                            clase = 'text-bg-secondary';
                    }

                    return `
            <div class="d-flex justify-content-center align-items-center">
                <span class="badge ${clase} shadow-sm" style="font-size: 12px; padding: 5px 12px;">
                    ${texto}
                </span>
            </div>
        `;
                }
            }
        ],
        initComplete: function (settings, json) {

            if (codWeb) {
                obtenerDetalleCompleto(codWeb);
            }
        },
        language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
        dom: 'rt'
    });
}


function obtenerDetalleCompleto(codWeb) {
    $.ajax({
        url: "../controladores/documentos.php?op=mostrarSeguimiento",
        type: "GET",
        data: { cod_web: codWeb },
        dataType: "json",
        success: function (response) {

            console.log("RESPONSE:", response);

            if (!response || !response.aaData) {
                console.error("Respuesta inválida");
                return;
            }

            if (response.aaData.length > 0) {
                generarVistaDetalle(response.aaData);
            } else {
                $('#contenedorDetallesTramite')
                    .html('<p class="text-danger">Sin datos del backend</p>');
            }
        },
        error: function () {
            console.error("Error al cargar el detalle del trámite.");
        }
    });
}

function generarVistaDetalle(dataArray) {

    if (!dataArray || dataArray.length === 0) {
        $('#contenedorDetallesTramite').html('<p class="text-muted">No hay información disponible.</p>');
        return;
    }

    const safe = (val) => val ? val : '---';

    const grupos = {};

    dataArray.forEach(item => {
        let key = item.cod_documento || 'SIN_COD';

        if (!grupos[key]) {
            grupos[key] = [];
        }
        grupos[key].push(item);
    });

    let html = '';

    // Función auxiliar para imprimir "Observado" en lugar del número 2 en la vista
    const textoEstado = (val) => {
        if (val == 2 || String(val).toLowerCase() === 'observado') return 'Observado';
        return safe(val);
    };

    //Recorrer cada expediente
    Object.values(grupos).forEach((grupo) => {

        const principal = grupo[0];


        // Procesar donde se usó como referencia
        let htmlReferencias = '';
        if (principal.usado_en_referencia) {
            const lista = principal.usado_en_referencia.split('|');
            htmlReferencias = `
                <div class="mt-3" style='font-size:13px;'>
                    <p class="mb-1 text-dark" style='font-size:13px;'>Usado como referencia en:</p>
                    <ul class="list-unstyled mb-0 ms-3">
                        ${lista.map(ref => `<li class="text-muted">• ${ref}</li>`).join('')}
                    </ul>
                </div>`;
        }


        // 1. Detección a prueba de fallos (ignora si viene null, en mayúsculas o como número 2)
        const estaObservado = principal.observado == 2;

        let comentarioObservacion = principal.comentario_observacion
            ? principal.comentario_observacion
            : "Se han encontrado observaciones en su trámite. Por favor, revise y adjunte los documentos requeridos.";

        if (estaObservado) {
            html += `
                <div class="alert mb-4 shadow-sm" role="alert" style="background-color: #fff5f5; border: 1px solid #f5c2c7; border-left: 5px solid #dc3545 !important; border-radius: 0.5rem;">
                    <div class="d-flex gap-3">
                        <div class="text-danger mt-1">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                        <div class="w-100">
                            <h6 class="alert-heading fw-bold mb-1 text-danger">Acción Requerida: Trámite Observado</h6>
                            <p class="mb-3 small text-dark">${safe(comentarioObservacion)}</p>

                            <div class="bg-white p-3 rounded-2 border" style="border-color: #f5c2c7 !important;">
                                <form id="formSubsanar_${principal.cod_documento}" class="m-0">
                                    <label class="form-label fw-semibold small mb-2 text-dark">Adjuntar documento subsanado:</label>
                                    <div class="input-group input-group-sm">
                                        <input type="file" class="form-control" id="archivoSubsanacion_${principal.cod_documento}" required>
                                        <button class="btn btn-primary px-3" type="button" onclick="procesarSubsanacion('${principal.cod_documento}')">
                                            <i class="fas fa-upload me-1"></i> Enviar Subsanación
                                        </button>
                                    </div>
                                    <div class="form-text mt-1" style="font-size: 11px;">Solo se permiten archivos PDF o ZIP. Max 50MB.</div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                
            `;
        }
        html += `
<div class="card mb-4 border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-4" style="color:#085ec5; font-size: 14px;">DATOS PRINCIPALES DEL TRÁMITE</h6>

        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <span class="text-muted d-block" style="font-size: 13px;">Expediente:</span>
                <div class="border-bottom pb-2 mt-1">
                    <strong class="text-dark">${safe(principal.cod_documento)}</strong>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <span class="text-muted d-block" style="font-size: 13px;">N° Documento:</span>
                <div class="border-bottom pb-2 mt-1">
                    <strong class="text-dark">${principal.num_doc ? String(principal.num_doc).padStart(3, '0') : '---'}</strong>
                </div>
            </div>

            <!-- Fila 2: Asunto y Estado -->
            <div class="col-md-6 mb-3">
                <span class="text-muted d-block" style="font-size: 13px;">Asunto:</span>
                <div class="border-bottom pb-2 mt-1">
                    <strong class="text-dark text-uppercase">${safe(principal.asunto)}</strong>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <span class="text-muted d-block" style="font-size: 13px;">Estado:</span>
                <div class="border-bottom pb-2 mt-1">
                    <strong class="text-dark">${safe(principal.estado)}</strong>
                </div>
            </div>
        </div>

        ${htmlReferencias}

        <div class="table-responsive mt-3">
            <table class="table table-bordered align-middle" style="font-size: 12px; border: 1px solid #dee2e6;">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="py-3 px-2 text-center">#</th>
                        <th class="py-3">N° PROVEÍDO</th>
                        <th class="py-3">OFICINA ORIGEN</th>
                        <th class="py-3">FECHA ENVÍO</th>
                        <th class="py-3">OFICINA DESTINO</th>
                        <th class="py-3">FECHA RECEPCIÓN</th>
                        <th class="py-3">ESTADO</th>
                        <th class="py-3">COMENTARIO</th>
                    </tr>
                </thead>
                <tbody>
`;
        // 🔹 Invertir orden dentro del grupo
        grupo = [...grupo].reverse();

        grupo.forEach((data, index) => {

            html += `
                <tr class="bg-white">
                    <td class="fw-semibold text-center">${index + 1}</td>

                    <td class="text-center">
                        ${safe(data.n_proveido)}
                    </td>

                    <td>
                        <div class="text-muted">${safe(data.nombre_oficina_origen)}</div>
                    </td>

                    <td>
                        <div class="text-muted">${safe(data.fecha)}</div>
                    </td>

                    <td>
                        <div class="text-muted">${safe(data.nombre_oficina)}</div>
                    </td>

                    <td>
                        <div class="text-muted">${safe(data.fecha_recepcion)}</div>
                    </td>

                    <td class="text-center">
                        <span class="badge bg-light-dark rounded-pill" style="font-size:11px">
                            ${safe(data.estado2)}
                        </span>
                    </td>

                    <td class="text-muted">
                        ${safe(data.comentario)}
                    </td>
                </tr>
            `;
        });

        html += `
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        `;
    });

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




function subsanarDocumento(cod_web) {

    $('#cod_web_subsanar').val(cod_web);

    $('#archivoSubsanar').val('');
    $('#comentarioSubsanar').val('');

    const modal = new bootstrap.Modal(document.getElementById('modalSubsanar'));
    modal.show();
}


function procesarSubsanacion(codDocumento) {
    const docSubsanado = document.getElementById(`archivoSubsanacion_${codDocumento}`);
    const codWeb = $('#contenedorSeguimiento').data('codweb');
    if (docSubsanado.files.length === 0) {
        alert("Por favor, seleccione un archivo para subsanar.");
        return;
    }
    const maxSize = 50 * 1024 * 1024; // 20 MB
    if (docSubsanado.files[0].size > maxSize) {
        alert("El archivo excede el límite permitido de 50MB.");
        return;
    }
    const formData = new FormData();
    formData.append("archivo", docSubsanado.files[0]);
    formData.append("cod_documento", codDocumento);

    const btnSubmit = event.currentTarget || docSubsanado.nextElementSibling;
    const originalText = btnSubmit.innerHTML;
    if (btnSubmit) {
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';
        btnSubmit.disabled = true;
    }

    $.ajax({
        url: ' ../controladores/documentos.php?op=subsanarDocumento',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                    btnSubmit.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${percentComplete}%`;
                }
            }, false);
            return xhr;
        },
        success: function (response) {
            try {
                const res = typeof response === 'string' ? JSON.parse(response) : response;

                if (res.status === 'success' || res.success) {
                    Swal.fire({
                        title: "Trámite subsanado correctamente.",
                        icon: "success"
                    });
                    //obtenerDetalleCompleto(codWeb);
                    tablaSeguimiento(codWeb);
                } else {
                    Swal.fire({
                        title: "Ocurrió un problema.",
                        text: res.message || "Error al subir el archivo.",
                        icon: "error"
                    });
                }
            } catch (error) {
                console.error("Error al leer la respuesta del servidor:", response);
                Swal.fire({
                    title: "Error",
                    text: "El archivo se envió, pero hubo un problema al leer la respuesta.",
                    icon: "error"
                });
            }
        },
        error: function (xhr, status, error) {
            console.error("Error de AJAX:", error);
            Swal.fire({
                title: "Error",
                text: "Error de conexión al intentar enviar el documento. Intente nuevamente.",
                icon: "error"
            });
        },
        complete: function () {
            if (btnSubmit) {
                btnSubmit.innerHTML = originalText;
                btnSubmit.disabled = false;
            }
            docSubsanado.value = '';
        }
    });
}

function listarConteoDocs() {
    // DEFINIMOS LA RUTA COMPLETA PARA EVITAR PROBLEMAS DE CONTEXTO EN DIFERENTES PÁGINAS
    const url = '../controladores/documentos.php?op=listarConteoDocs';
    // INICIAMOS LA PETICIÓN AJAX
    fetch(url)
        .then(response => {
            // Verificamos que la respuesta sea exitosa
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        // Procesamos los datos recibidos
        .then(data => {
            // Verificamos que el formato de datos sea correcto
            if (data.aaData && data.aaData.length > 0) {
                const conteos = data.aaData[0];
                // Extraemos los conteos, asegurándonos de convertirlos a enteros y manejar posibles valores nulos o no numéricos
                const total = parseInt(conteos.total) || 0;
                const pendientes = parseInt(conteos.pendiente) || 0;
                const observados = parseInt(conteos.observado) || 0;
                const finalizados = parseInt(conteos.finalizado) || 0;

                // Actualizamos los elementos del DOM con los conteos
                document.getElementById('totalTramites').textContent = total;
                document.getElementById('totalPendientes').textContent = pendientes;
                document.getElementById('totalObservados').textContent = observados;
                document.getElementById('totalFinalizados').textContent = finalizados;

                // Calculamos los porcentajes, evitando división por cero
                const pctPend = total > 0 ? (pendientes / total) * 100 : 0;
                const pctObs = total > 0 ? (observados / total) * 100 : 0;
                const pctFin = total > 0 ? (finalizados / total) * 100 : 0;

                // Actualizamos las barras de progreso
                document.getElementById('pb-total').style.width = '100%';
                document.getElementById('pb-pendientes').style.width = pctPend + '%';
                document.getElementById('pb-observados').style.width = pctObs + '%';
                document.getElementById('pb-finalizados').style.width = pctFin + '%';

                // Actualizamos los textos de porcentaje
                document.getElementById('f-total').textContent = total;
                document.getElementById('f-proc').textContent = Math.round(pctPend) + '%';
                document.getElementById('f-obs').textContent = Math.round(pctObs) + '%';
                document.getElementById('f-fin').textContent = Math.round(pctFin) + '%';

                // Cálculo para el gráfico de donut (usamos circunferencia de un círculo con radio 55)
                const circunferencia = 263.6;
                // Convertimos los porcentajes a longitudes de arco para cada categoría
                const dashPend = (pctPend / 100) * circunferencia;
                const dashFin = (pctFin / 100) * circunferencia;
                const dashObs = (pctObs / 100) * circunferencia;

                // Para que las categorías se muestren en orden (Pendientes, Finalizados, Observados), calculamos los offsets acumulativos
                const offsetPend = 0;
                const offsetFin = dashPend;
                const offsetObs = dashPend + dashFin;
                // Función para aplicar los estilos SVG a cada arco del donut
                function setArc(id, dash, offset) {
                    const el = document.getElementById(id);
                    el.style.strokeDasharray = `${dash} ${circunferencia - dash}`;
                    el.style.strokeDashoffset = -offset;
                    el.setAttribute('transform', `rotate(-90 55 55)`);
                }
                // Aplicamos los estilos con un pequeño retraso para asegurar que el DOM esté listo y evitar problemas de renderizado
                setTimeout(() => {
                    setArc('arc-proc', dashPend, offsetPend);
                    setArc('arc-fin', dashFin, offsetFin);
                    setArc('arc-obs', dashObs, offsetObs);
                }, 100);

                // Actualizamos el centro del donut con el total de trámites
                document.getElementById('donut-center').textContent = total;
                // Actualizamos las leyendas con los conteos y porcentajes
                document.querySelector('#arc-proc').closest('.donut-wrap')
                    ?.querySelectorAll('.legend-val')[0]
                    && (document.querySelectorAll('.legend-val')[0].textContent = pendientes);
                document.querySelectorAll('.legend-val')[1].textContent = finalizados;
                document.querySelectorAll('.legend-val')[2].textContent = observados;
                document.querySelectorAll('.legend-val')[3].textContent = total;
                // Actualizamos los porcentajes en las leyendas
                document.querySelectorAll('.legend-pct')[0].textContent = `(${Math.round(pctPend)}%)`;
                document.querySelectorAll('.legend-pct')[1].textContent = `(${Math.round(pctFin)}%)`;
                document.querySelectorAll('.legend-pct')[2].textContent = `(${Math.round(pctObs)}%)`;
            }
        })
        .catch(error => console.error('Error al cargar el dashboard:', error));
}

function listarActividadReciente() {
    // DEFINIMOS LA RUTA COMPLETA PARA EVITAR PROBLEMAS DE CONTEXTO EN DIFERENTES PÁGINAS
    const url = '../controladores/documentos.php?op=listarReciente';
    // INICIAMOS LA PETICIÓN AJAX
    fetch(url)
        // Verificamos que la respuesta sea exitosa
        .then(response => {
            // Verificamos que la respuesta sea exitosa
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        // Procesamos los datos recibidos
        .then(data => {
            const lista = document.querySelector('.activity-list');
            lista.innerHTML = '';
            // Verificamos que el formato de datos sea correcto y que haya actividades
            if (!data.aaData || data.aaData.length === 0) {
                lista.innerHTML = '<li class="activity-item"><div class="activity-text">No hay actividad reciente.</div></li>';
                return;
            }
            // Iteramos sobre cada actividad y la agregamos a la lista
            data.aaData.forEach(item => {
                lista.innerHTML += `
                    <li class="activity-item">
                        <div class="activity-dot" style="background:${item.estado_color};"></div>
                        <div class="activity-text"><strong>SOLICITUD</strong> DE ${item.asunto}</div>
                        <span class="activity-tag" style="background:${item.estado_bg}; color:${item.estado_color};">
                            ${item.estado_texto}
                        </span>
                        <span class="activity-time">${item.fecha_texto}</span>
                    </li>`;
            });
        })
        .catch(err => console.error('Error actividad reciente:', err));
}

   /* NUEVA FUNCIÓN: Consulta al controlador de Sivireno*/
  function verificarTramiteEstu(idTupa) {
    const btn = $("#btn-enviar"); 
    btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Verificando...');

    $.post("../controladores/sivireno.php?op=verificarTramite", { id_tupa: idTupa }, function(data) {
        try {
            const res = JSON.parse(data);
            btn.removeClass("btn-primary btn-danger btn-warning");
            
            if (res.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Requisito cumplido',
                    text: res.mensaje,
                    confirmButtonColor: '#3085d6'
                });
                // CUMPLE
                btn.prop("disabled", false).text("Enviar Solicitud").addClass("btn-primary").removeClass("btn-danger");
             } else {
                // NO CUMPLE
                Swal.fire({
                    icon: 'warning',
                    title: 'Requisito no cumplido',
                    text: res.mensaje,
                    confirmButtonColor: '#3085d6'
                });
                if (res.bloqueo) {
                    // Bloqueo total (Ej. No es egresado o 0 notas aprobadas)
                    btn.text("No disponible").prop("disabled", true).addClass("btn-danger");
                } else {
                    // Excepción flexible (Puede intentar enviarlo)
                    btn.prop("disabled", false).text("Enviar Solicitud").addClass("btn-warning");
                }
            }
        } catch (e) {
            console.error("Error JSON:", data);  
            btn.removeClass("btn-primary btn-warning").addClass("btn-danger");
            btn.text("Error de sistema").prop("disabled", true);
        }
    });
}


/* function irASeguimiento(codWeb) {
    // Creamos un formulario dinámicamente
    var form = document.createElement("form");
    form.method = "POST";
    form.action = "seguimiento.php";*/

