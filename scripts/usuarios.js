$(document).ready(function () {


    
    $("#formLogin").on("submit", function (e) {
        e.preventDefault();
        $("#mensajeLogin").text('');

        $.ajax({
            url: "controladores/usuarios.php?op=loguearUsuario",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    window.location.href = "views/index.php";

                } else {
                    $("#mensajeLogin").text(response.mensaje);
                }
            },
            error: function () {
                $("#mensajeLogin").text("Error al procesar el login. Intente nuevamente.");
            }
        });
    });



});

