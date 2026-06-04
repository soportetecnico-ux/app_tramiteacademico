function mostrarPDF() {

    document.getElementById("previewBox").innerHTML = `
    
        <iframe 
            src="../assets/documentos/MANUAL_USUARIO_TA.pdf"
            width="100%"
            height="100%"
            style="border:none;">
        </iframe>

    `;
}

function mostrarVideo() {

    document.getElementById("previewBox").innerHTML = `
    
        <iframe
            width="100%"
            height="100%"
            src="https://drive.google.com/file/d/1Re6HIU1Gw8RS7o0c7swqd4iqcyamEoGb/preview"
            title="Video Tutorial"
            frameborder="0"
            allow="autoplay; encrypted-media"
            allowfullscreen>
        </iframe>

    `;
}

document.addEventListener("DOMContentLoaded", function () {
    mostrarPDF();
});
