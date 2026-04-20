let datosUsuarioGlobal = null;

$(document).ready(function () {
    cargarDatosUsuario();
    listarTramites();


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

    const fechaComprobante = $("#fechaComprobante").val();
    if (fechaComprobante === "") {
        Swal.fire({ title: "Campo requerido", text: "Seleccione la fecha en la que realizó el pago.", icon: "warning", width: '380px' });
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
                    return `<span class="badge rounded-pill bg-light text-dark fw-medium px-2" style="font-size: 0.75rem;">${meta.row + 1}</span>`;
                }
            },
            {
                // Fecha con icono y estilo muted
                data: "fecha", // Asegúrate que el SQL devuelva este alias
                className: "align-middle",
                render: function (data) {
                    return `
                        <div class="d-flex align-items-center">
                            <i class="ti ti-calendar-event me-2 text-primary opacity-75 fs-5"></i>
                            <span class="text-secondary fw-medium" style="font-size: 0.8rem;">${data}</span>
                        </div>`;
                }
            },
            {
                // Código Web estilo "Tag" elegante
                data: "cod_web",
                className: "align-middle",
                render: function (data) {
                    return `<span class="badge bg-light-success fw-bold" style="letter-spacing: 0.5px;">${data}</span>`;
                }
            },
            {
                // Asunto con tipografía limpia y truncado inteligente
                data: "asunto",
                className: "align-middle",
                render: function (data) {
                    return `
                        <div class="d-flex align-items-center">
                            <span class="text-muted small text-uppercase">${data}</span>
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
                            <span class="text-muted small text-uppercase">${data}</span>
                        </div>`;
                }
            },
            {
                // Botón de Acción elegante (Modern Glassmorphism style)
                data: "cod_web",
                className: "text-center align-middle",
                render: function (data) {
                    return `
                        <button onclick="verSeguimiento('${data}')" 
                                class="btn btn-shadow btn-success btn-sm px-3">Seguimiento
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