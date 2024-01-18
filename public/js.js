var div=document.getElementById('page');
console.log(div);
var page= div.getAttribute('attr');
console.log(page);
let x= Number(page);
console.log(x);
//////////////////////////////////////////////

var div2=document.getElementById('urlPdf');
console.log(div2);
var url= div2.getAttribute('attr2');
console.log(url);

/////////////////////////////////////////
// Chargement du fichier PDF
var pdfUrl = 'pdf/'+url;

// Options pour pdf.js
var options = {
    // Placez ici toutes les options spécifiques que vous voulez

    renderer: 'canvas',
    pagemode: 'sidebar state'
};


// Récupérer une référence vers le canvas
var canvas = document.getElementById('pdf-canvas');

// Charger le document PDF
pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
    // Récupérer la première page du document
    
    return pdf.getPage(x);
}).then(function(page) {
    // Configuration de l'échelle pour le rendu du canvas
    var scale = 1;
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