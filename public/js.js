function setupPdfViewer() {
    var pageNode = document.getElementById('page');
    var currentPage = 1;
    if (pageNode) {
        var attrValue = pageNode.getAttribute('attr');
        if (attrValue) {
            var parsed = Number(attrValue);
            if (!Number.isNaN(parsed) && parsed > 0) {
                currentPage = parsed;
            }
        }
    }

    var urlNode = document.getElementById('urlPdf');
    var pdfUrl = '';
    if (urlNode) {
        var rawUrl = (urlNode.getAttribute('attr2') || '').trim();
        if (rawUrl.length > 0) {
            if (rawUrl.startsWith('http://') || rawUrl.startsWith('https://')) {
                pdfUrl = rawUrl;
            } else if (rawUrl.startsWith('/')) {
                pdfUrl = rawUrl;
            } else {
                pdfUrl = '/' + rawUrl.replace(/^\/+/, '');
            }
        }
    }

    if (!pdfUrl) {
        console.warn('Aucun PDF a afficher : chemin non fourni.');
        return;
    }

    var pdfjsLib = window.pdfjsLib || window['pdfjs-dist/build/pdf'];
    if (!pdfjsLib) {
        console.error('pdfjsLib introuvable. Vérifiez le chargement de pdf.js');
        return;
    }

    var canvas = document.getElementById('pdf-canvas');
    if (!canvas) {
        console.error('Element #pdf-canvas introuvable');
        return;
    }

    var context = canvas.getContext('2d');
    var pdfDocument = null;
    var totalPages = 0;
    var isRendering = false;
    var scale = 1;
    var rotation = 0;
    var pendingRender = false;
    var viewerFrame = canvas.parentElement;

    var prevBtn = document.getElementById('pdf-prev');
    var nextBtn = document.getElementById('pdf-next');
    var pageIndicator = document.getElementById('pdf-page-indicator');
    var tablePageIndicator = document.getElementById('table-page-indicator');
    var zoomInBtn = document.getElementById('pdf-zoom-in');
    var zoomOutBtn = document.getElementById('pdf-zoom-out');
    var zoomIndicator = document.getElementById('pdf-zoom-indicator');
    var rotateBtn = document.getElementById('pdf-rotate');

    var MIN_SCALE = 0.5;
    var MAX_SCALE = 2.5;
    var SCALE_STEP = 0.1;
    var ROTATION_STEP = 90;

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function getAvailableWidth() {
        if (viewerFrame) {
            var frameWidth = viewerFrame.clientWidth || viewerFrame.getBoundingClientRect().width;
            if (frameWidth > 0) {
                var styles = window.getComputedStyle(viewerFrame);
                var paddingLeft = parseFloat(styles.paddingLeft) || 0;
                var paddingRight = parseFloat(styles.paddingRight) || 0;
                var available = frameWidth - paddingLeft - paddingRight;
                if (available > 0) {
                    return available;
                }
            }
        }
        return canvas.parentElement ? canvas.parentElement.clientWidth || 600 : 600;
    }

    function updateZoomIndicator() {
        if (zoomIndicator) {
            zoomIndicator.textContent = Math.round(scale * 100) + '%';
        }
        if (zoomOutBtn) {
            zoomOutBtn.disabled = (scale <= MIN_SCALE + 0.01);
            zoomOutBtn.classList.toggle('viewer-btn--disabled', zoomOutBtn.disabled);
        }
        if (zoomInBtn) {
            zoomInBtn.disabled = (scale >= MAX_SCALE - 0.01);
            zoomInBtn.classList.toggle('viewer-btn--disabled', zoomInBtn.disabled);
        }
    }

    function setButtonState(button, disabled) {
        if (!button) {
            return;
        }
        button.disabled = disabled;
        button.classList.toggle('viewer-btn--disabled', disabled);
    }

    function updateControls() {
        if (pageIndicator) {
            pageIndicator.textContent = totalPages ? currentPage + ' / ' + totalPages : currentPage;
        }
        if (tablePageIndicator) {
            tablePageIndicator.textContent = totalPages ? currentPage + ' / ' + totalPages : currentPage;
        }
        setButtonState(prevBtn, !pdfDocument || currentPage <= 1 || isRendering);
        setButtonState(nextBtn, !pdfDocument || (totalPages && currentPage >= totalPages) || isRendering);
        updateZoomIndicator();
    }

    function renderPage(pageNum) {
        if (!pdfDocument) {
            return;
        }
        pendingRender = false;
        isRendering = true;
        updateControls();
        pdfDocument.getPage(pageNum).then(function (page) {
            if (!viewerFrame || !viewerFrame.isConnected) {
                viewerFrame = canvas.parentElement;
            }
            var availableWidth = getAvailableWidth();
            var baseViewport = page.getViewport({ scale: 1, rotation: rotation });
            var baseWidth = baseViewport.width || 1;
            var fitScale = availableWidth / baseWidth;
            var effectiveScale = fitScale * scale;
            var viewport = page.getViewport({ scale: effectiveScale, rotation: rotation });
            var outputScale = window.devicePixelRatio || 1;
            canvas.style.width = viewport.width + 'px';
            canvas.style.height = viewport.height + 'px';
            canvas.width = Math.floor(viewport.width * outputScale);
            canvas.height = Math.floor(viewport.height * outputScale);
            return page.render({
                canvasContext: context,
                viewport: viewport,
                transform: [outputScale, 0, 0, outputScale, 0, 0]
            }).promise;
        }).then(function () {
            isRendering = false;
            updateControls();
            if (pendingRender) {
                pendingRender = false;
                renderPage(currentPage);
            }
        }).catch(function (error) {
            isRendering = false;
            console.error('Erreur lors du rendu du PDF :', error);
            updateControls();
            if (pendingRender) {
                pendingRender = false;
                renderPage(currentPage);
            }
        });
    }

    function requestRender() {
        if (!pdfDocument) {
            return;
        }
        if (isRendering) {
            pendingRender = true;
        } else {
            renderPage(currentPage);
        }
    }

    function goToPage(offset) {
        if (!pdfDocument || isRendering) {
            return;
        }
        var target = currentPage + offset;
        if (target < 1 || target > totalPages) {
            return;
        }
        currentPage = target;
        renderPage(currentPage);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            goToPage(-1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            goToPage(1);
        });
    }

    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', function () {
            if (!pdfDocument || isRendering) {
                return;
            }
            scale = clamp(scale + SCALE_STEP, MIN_SCALE, MAX_SCALE);
            renderPage(currentPage);
        });
    }

    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', function () {
            if (!pdfDocument || isRendering) {
                return;
            }
            scale = clamp(scale - SCALE_STEP, MIN_SCALE, MAX_SCALE);
            renderPage(currentPage);
        });
    }

    if (rotateBtn) {
        rotateBtn.addEventListener('click', function () {
            if (!pdfDocument || isRendering) {
                return;
            }
            rotation = (rotation + ROTATION_STEP) % 360;
            renderPage(currentPage);
        });
    }

    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.7.570/pdf.worker.min.js';

    var encodedUrl = encodeURI(pdfUrl);
    pdfjsLib.getDocument({ url: encodedUrl }).promise.then(function (pdf) {
        pdfDocument = pdf;
        totalPages = pdf.numPages;
        if (currentPage > totalPages) {
            currentPage = totalPages;
        } else if (currentPage < 1) {
            currentPage = 1;
        }
        renderPage(currentPage);
    }).catch(function (error) {
        console.error('Erreur lors du chargement du PDF :', error);
        pdfDocument = null;
        totalPages = 0;
        updateControls();
    });

    window.addEventListener('ocr:pane-resized', requestRender);
    window.addEventListener('resize', requestRender);

    updateControls();
}

document.addEventListener('DOMContentLoaded', setupPdfViewer);
