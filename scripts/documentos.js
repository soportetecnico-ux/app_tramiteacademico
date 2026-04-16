let datosUsuarioGlobal = null;

$(document).ready(function () {
    cargarDatosUsuario();


    $.post("../controladores/documentos.php?op=seleccionarTramite", function (r) {
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
        const anexosRaw = opt.data('anexos'); // Trae los nombres unidos por "|"

        // 2. REFERENCIA AL CONTENEDOR DE ANEXOS
        const contenedorAnexos = document.getElementById('listaAnexos');

        // Limpiamos la lista actual antes de generar la nueva
        contenedorAnexos.innerHTML = "";

        if (requisito !== undefined) {
            // --- LÓGICA DE DETALLES DEL TRÁMITE ---
            $("#lbl_requisito").text(requisito);
            $("#lbl_monto").text(monto);
            $("#dependencia").val(oficina);
            $("#dependencia").attr("data-cod", codOficina);
            $("#detalle_tupa").fadeIn(300);

            actualizarPlantillaFundamentacion(nombreTramite);

            // --- LÓGICA DINÁMICA DE ANEXOS ---
            if (anexosRaw && anexosRaw.trim() !== "") {
                const lista = anexosRaw.split('|');

                lista.forEach((nombre, index) => {
                    const nuevaFila = document.createElement('div');
                    nuevaFila.className = 'mb-2 archivo-item';

                    // Generamos el HTML con el nombre específico del anexo
                    nuevaFila.innerHTML = `
                    <label class="mb-1 fw-bold text-uppercase" style="font-size: 0.7rem; color: #4e4e4e;">
                        <i class="ti ti-file-check text-primary"></i> ${nombre}
                    </label>
                    <div class="input-group input-group-sm">
                        <input type="file" class="form-control form-control-sm" 
                               name="archivo_tupa[]" 
                               id="anexo_${index}" 
                               onchange="validarArchivo(this)">
                        <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.parentElement.remove()">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                `;
                    contenedorAnexos.appendChild(nuevaFila);
                });
            } else {
                // Si el trámite no tiene anexos configurados en la tabla
                contenedorAnexos.innerHTML = `
                <div class="alert alert-light border py-2 px-3 mb-0" style="font-size: 0.8rem;">
                    <i class="ti ti-info-circle text-info"></i> Este trámite no requiere documentos adjuntos específicos.
                </div>`;
            }

        } else {
            // --- SI NO HAY NADA SELECCIONADO ---
            $("#detalle_tupa").hide();
            $("#dependencia").val("");
            $("#lbl_requisito").text("");
            $("#lbl_monto").text("");
            contenedorAnexos.innerHTML = ""; // Limpiar anexos

            actualizarPlantillaFundamentacion("[SELECCIONE UN TRÁMITE]");
        }
    });
});

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

Que, por convenir a mis intereses académicos, solicito se me otorgue el/la: ${tramiteLimpio}.

Por lo expuesto, pido a usted acceder a mi solicitud por ser de justicia.

Sin otro particular, me despido de usted expresándole las muestras de mi especial consideración y estima personal.`;

    $('#txtFundamentacion').val(plantilla);
}

function listarTramites() {
    $('#tablaTramites').DataTable({
        destroy: true,
        ordering: false,
        ajax: {
            url: "../controladores/documentos.php?op=listarMisTramites",
            type: "GET",
            dataSrc: function (json) {
                console.log("Datos recibidos del servidor:", json);
                return json.aaData; // DataTables usará este array
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: "fecha" },
            { data: "cod_web" },
            { data: "tipo_documento" },
            { data: "asunto" },
            {
                data: "nombre_archivo",
                render: function (data, type, row) {
                    if (data) {
                        return `<a href="../views/archivos/${data}" target="_blank" class="btn btn-sm btn-primary">Ver</a>`;
                    } else {
                        return '<span class="text-muted">Sin archivo</span>';
                    }
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });
}

function agregarArchivo() {
    const contenedor = document.getElementById('listaAnexos');
    const numArchivos = contenedor.getElementsByClassName('archivo-item').length;

    if (numArchivos < 10) {
        const nuevaFila = document.createElement('div');
        nuevaFila.className = 'input-group input-group-sm mb-1 archivo-item';
        nuevaFila.innerHTML = `
            <input type="file" class="form-control form-control-sm" onchange="validarArchivo(this)">
            <button class="btn btn-outline-danger" type="button" onclick="eliminarFila(this)"><i class="ti ti-trash"></i></button>
        `;
        contenedor.appendChild(nuevaFila);
    } else {
        alert("Máximo 10 archivos permitidos.");
    }
}

function eliminarFila(boton) {
    const contenedor = document.getElementById('listaAnexos');
    if (contenedor.getElementsByClassName('archivo-item').length > 1) {
        boton.closest('.archivo-item').remove();
    } else {
        // Si es el último, solo limpia el valor
        boton.previousElementSibling.value = "";
    }
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

function generarFirmaDigital() {
    const container = document.getElementById('previewFirmaContainer');
    if (container) container.style.display = 'flex';

    const ahora = new Date();

    // 1. Formato Fecha Sello (Puntos como separador)
    const dia = String(ahora.getDate()).padStart(2, '0');
    const mes = String(ahora.getMonth() + 1).padStart(2, '0');
    const anio = ahora.getFullYear();
    const hora = String(ahora.getHours()).padStart(2, '0');
    const min = String(ahora.getMinutes()).padStart(2, '0');
    const seg = String(ahora.getSeconds()).padStart(2, '0');

    // Mantenemos el formato de tu imagen: 16.04.2026 08:55:07 -05:00
    const fechaFormateada = `${dia}.${mes}.${anio} ${hora}:${min}:${seg} -05:00`;

    const elFechaFirma = document.getElementById('fechaFirma');
    if (elFechaFirma) elFechaFirma.innerText = fechaFormateada;

    // 2. Formato Pie de Página (SAN VICENTE, 16 DE ABRIL DE 2026)
    const opcionesFecha = { day: 'numeric', month: 'long', year: 'numeric' };
    let fechaLarga = ahora.toLocaleDateString('es-PE', opcionesFecha);

    const elFechaAuto = document.getElementById('fechaActualAutomatica');
    if (elFechaAuto) {
        elFechaAuto.innerText = `SAN VICENTE, ${fechaLarga.toUpperCase()}`;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const fechaElemento = document.getElementById('fechaActualAutomatica');

    const opciones = {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    };
    const hoy = new Date().toLocaleDateString('es-ES', opciones);


    fechaElemento.innerText = hoy.toUpperCase();
});