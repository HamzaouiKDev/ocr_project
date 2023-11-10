// Chargement du fichier PDF
var pdfUrl = 'pdf/pdf_file.pdf';
// Options pour pdf.js
var options = {
    // Placez ici toutes les options spécifiques que vous voulez
};

// Récupérer une référence vers le canvas
var canvas = document.getElementById('pdf-canvas');

// Charger le document PDF
pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
    // Récupérer la première page du document
    return pdf.getPage(1);
}).then(function(page) {
    // Configuration de l'échelle pour le rendu du canvas
    var scale = 1.5;
    var viewport = page.getViewport({ scale: scale });

    // Récupérer le contexte du canvas
    var context = canvas.getContext('2d');
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    // Dessiner la page sur le canvas
    var renderContext = {
        canvasContext: context,
        viewport: viewport
    };
    page.render(renderContext);
});