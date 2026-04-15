$(document).ready(function () {

    cargarDatosUsuario();
    listarTramites();

    $.post("../controladores/documentos.php?op=seleccionarTramite", function (r) {
        $("#id_tupa").html(r);
    });

    $("#id_tupa").on("change", function () {
        const opt = $(this).find('option:selected');

        // Extraer datos de los atributos data-
        const requisito = opt.data('requisito');
        const monto = opt.data('monto');
        const oficina = opt.data('oficina');
        const codOficina = opt.data('codoficina');

        if (requisito !== undefined) {
            // 1. Llenar los cuadros de información
            $("#lbl_requisito").text(requisito);
            $("#lbl_monto").text(monto);

            // 2. LLENAR EL CAMPO DEPENDENCIA AUTOMÁTICAMENTE
            $("#dependencia").val(oficina);

            // Opcional: Si necesitas guardar el cod_oficina para el registro final, 
            // puedes guardarlo en un input hidden o un atributo del propio input
            $("#dependencia").attr("data-cod", codOficina);

            // 3. Mostrar el detalle con efecto
            $("#detalle_tupa").fadeIn(300);
        } else {
            $("#detalle_tupa").hide();
            $("#dependencia").val("");
        }
    });

});

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


function cargarDatosUsuario() {
    $.ajax({
        url: "../controladores/usuarios.php?op=obtenerDatosUsuario",
        type: "GET",
        dataType: "json",
        success: function (response) {

            if (response.status === "success") {

                const data = response.data;

                $('#dni').val(data.dni_estu ?? '');
                $('#nombres').val(
                    `${data.nom_estu ?? ''}`
                );
                $('#apepa').val(
                    `${data.apepa_estu ?? ''}`
                );
                $('#apema').val(
                    `${data.apema_estu ?? ''}`
                );
                $('#correo').val(data.email_estu ?? '');
                $('#celular').val(data.celu_estu ?? '');
                $('#direccion').val(data.domi_estu ?? '');
                $('#departamento').val(data.depar ?? '');
                $('#provincia').val(data.provi ?? '');
                $('#distrito').val(data.dist ?? '');

            } else {
                console.error("Error:", response.mensaje);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
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

function visualizarFirma(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('imgFirma').src = e.target.result;
            document.getElementById('previewFirmaContainer').style.display = 'block';

            // Si usas el formato oficial oculto, también copia la imagen allí
            const outFirma = document.getElementById('out_img_firma');
            if (outFirma) outFirma.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
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