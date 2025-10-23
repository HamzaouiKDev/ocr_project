$(document).ready(function () {
    var table = $('#sample_data').DataTable({
        order: [],
        rowId: 'id',
        rowReorder: {
            selector: 'td:first-child',
            dataSrc: 0,
            update: true
        },
        processing: true,
        serverSide: true,
        scrollX: true,
        paging: false,
        ordering: false,
        searching: false,
        info: false,
        language: {
            zeroRecords: 'Aucun enregistrement trouve',
            infoEmpty: 'Aucun enregistrement disponible',
            emptyTable: 'rien'
        },
        ajax: {
            url: '/getpage',
            type: 'POST',
            dataSrc: 'data'
        },
        columnDefs: [
            { data: 'id', targets: 0 },
            { data: 'code', targets: 1 },
            { data: 'label', targets: 2 },
            { data: 'notes', targets: 3 },
            { data: 'value_n', targets: 4 },
            { data: 'value_n1', targets: 5 },
            {
                targets: 6,
                data: null,
                orderable: false,
                searchable: false,
                className: 'table-action-cell',
                render: function (_data, type) {
                    if (type === 'display') {
                        return '<button type="button" class="row-delete-btn" title="Supprimer cette ligne">Supprimer</button>';
                    }
                    return '';
                }
            }
        ],
        createdRow: function (row, data) {
            $(row).attr('data-id', data['id']);
            $('td', row).each(function (colIndex) {
                if (colIndex === 6) {
                    $(this).addClass('table-action-cell');
                    return;
                }
                if (colIndex === 1) {
                    $(this).attr({
                        'data-name': 'code',
                        'class': 'code',
                        'data-type': 'select',
                        'data-pk': data['id']
                    });
                }
                if (colIndex === 2) {
                    $(this).attr({
                        'data-name': 'label',
                        'class': 'label',
                        'data-type': 'text',
                        'data-pk': data['id']
                    });
                }
                if (colIndex === 3) {
                    $(this).attr({
                        'data-name': 'notes',
                        'class': 'notes',
                        'data-type': 'text',
                        'data-pk': data['id']
                    });
                }
                if (colIndex === 4) {
                    $(this).attr({
                        'data-name': 'value_n',
                        'class': 'value_n',
                        'data-type': 'text',
                        'data-pk': data['id']
                    });
                }
                if (colIndex === 5) {
                    $(this).attr({
                        'data-name': 'value_n1',
                        'class': 'value_n1',
                        'data-type': 'text',
                        'data-pk': data['id']
                    });
                }
            });
        }
    });

    function adjustTableLayout() {
        if (table && $.fn.DataTable.isDataTable('#sample_data')) {
            table.columns.adjust().draw(false);
        }
    }

    window.addEventListener('ocr:pane-resized', function () {
        requestAnimationFrame(function () {
            adjustTableLayout();
        });
    });

    window.addEventListener('resize', function () {
        requestAnimationFrame(function () {
            adjustTableLayout();
        });
    });

    adjustTableLayout();

    $('#sample_data tbody').on('click', '.row-delete-btn', function (event) {
        event.stopPropagation();
        var row = table.row($(this).closest('tr'));
        var rowData = row.data();
        if (!rowData || !rowData.id) {
            return;
        }
        if (!window.confirm('Confirmez-vous la suppression de cette ligne ?')) {
            return;
        }
        $.ajax({
            url: '/supprimerLigne',
            type: 'POST',
            data: { id: rowData.id },
            success: function () {
                table.ajax.reload(null, false);
                $('#sample_data tbody tr.row-selected').removeClass('row-selected');
            },
            error: function (xhr, status, error) {
                console.error(error);
                alert('Une erreur est survenue lors de la suppression.');
            }
        });
    });

    $('#sample_data tbody').on('click', 'tr', function () {
        var $row = $(this);
        if ($row.hasClass('row-selected')) {
            $row.removeClass('row-selected');
        } else {
            $('#sample_data tbody tr.row-selected').removeClass('row-selected');
            $row.addClass('row-selected');
        }
    });

    $('#btnAjouter').on('click', function () {
        var selected = $('#sample_data tbody tr.row-selected');
        var payload = {};

        if (selected.length) {
            var data = table.row(selected).data();
            payload.targetId = data.id;
            var answer = window.prompt('Inserer avant ou apres la ligne selectionnee ? (avant/apres)', 'apres');
            payload.position = (answer && answer.toLowerCase() === 'avant') ? 'before' : 'after';
        }

        $.ajax({
            url: '/ajoutLigne',
            type: 'POST',
            data: payload,
            success: function () {
                table.ajax.reload(null, false);
                $('#sample_data tbody tr.row-selected').removeClass('row-selected');
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });

    $('#sample_data').editable({
        mode: 'inline',
        container: 'body',
        selector: 'td.code',
        url: '/updatecode',
        title: 'Code',
        type: 'POST',
        datatype: 'json',
        source: '/api/coders',
        validate: function (value) {
            if ($.trim(value) === '') {
                return 'Ce champ est obligatoire';
            }
        }
    });

    $('#sample_data').editable({
        mode: 'inline',
        container: 'body',
        selector: 'td.label',
        url: '/updatelabel',
        title: 'Label',
        type: 'POST',
        validate: function (value) {
            if ($.trim(value) === '') {
                return 'Ce champ est obligatoire';
            }
        }
    });

    $('#sample_data').editable({
        mode: 'inline',
        container: 'body',
        selector: 'td.notes',
        url: '/updateNotes',
        title: 'Notes',
        type: 'POST'
    });

    $('#sample_data').editable({
        mode: 'inline',
        container: 'body',
        selector: 'td.value_n',
        url: '/updateAnneeN',
        title: 'Annee N',
        type: 'POST',
        success: function () {
            table.draw();
        }
    });

    $('#sample_data').editable({
        mode: 'inline',
        container: 'body',
        selector: 'td.value_n1',
        url: '/updateAnneeN1',
        title: 'Annee N-1',
        type: 'POST'
    });

    table.on('row-reorder', function () {
        var payload = { order: [] };

        table.rows({ order: 'current' }).every(function (rowIdx) {
            var rowData = this.data();
            if (rowData && rowData.id) {
                payload.order.push({
                    id: rowData.id,
                    ligne: rowIdx + 1
                });
            }
        });

        $.ajax({
            url: '/reorderLignes',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function () {
                table.ajax.reload(null, false);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });
});



document.addEventListener('DOMContentLoaded', function () {
    var container = document.querySelector('[data-resize-container]');
    var resizer = container ? container.querySelector('[data-resizer]') : null;
    var viewerPane = container ? container.querySelector('[data-pane="viewer"]') : null;
    var tablePane = container ? container.querySelector('[data-pane="table"]') : null;

    if (!container || !resizer || !viewerPane || !tablePane) {
        return;
    }

    var MIN_PERCENT = 18;
    var DEFAULT_PERCENT = 45;
    var activePointerId = null;
    var isDragging = false;
    var currentPercent = null;
    var storageKey = 'ocrPaneSplitPercent';
    var resizeScheduled = false;

    function readStoredPercent() {
        try {
            var raw = window.localStorage.getItem(storageKey);
            var value = Number(raw);
            if (!Number.isNaN(value) && value >= MIN_PERCENT && value <= 100 - MIN_PERCENT) {
                return value;
            }
        } catch (error) {
            console.warn('Impossible de lire la configuration de redimensionnement :', error);
        }
        return null;
    }

    function persistPercent(percent) {
        try {
            window.localStorage.setItem(storageKey, String(percent));
        } catch (error) {
            console.warn('Impossible d\'enregistrer la configuration de redimensionnement :', error);
        }
    }

    function applyPercent(percent) {
        currentPercent = percent;
        viewerPane.style.flexBasis = percent + '%';
        tablePane.style.flexBasis = (100 - percent) + '%';
    }

    function emitResizeEvent() {
        resizeScheduled = false;
        window.dispatchEvent(new CustomEvent('ocr:pane-resized'));
    }

    function scheduleResizeEvent() {
        if (resizeScheduled) {
            return;
        }
        resizeScheduled = true;
        requestAnimationFrame(emitResizeEvent);
    }

    function isStackedLayout() {
        return window.innerWidth <= 1200;
    }

    function handlePointerMove(event) {
        if (!isDragging || event.pointerId !== activePointerId) {
            return;
        }
        var bounds = container.getBoundingClientRect();
        if (!bounds.width) {
            return;
        }
        var offsetX = event.clientX - bounds.left;
        var percent = (offsetX / bounds.width) * 100;
        var clamped = Math.min(100 - MIN_PERCENT, Math.max(MIN_PERCENT, percent));
        applyPercent(clamped);
        scheduleResizeEvent();
    }

    function stopDragging(event) {
        if (!isDragging || event.pointerId !== activePointerId) {
            return;
        }
        isDragging = false;
        resizer.classList.remove('is-active');
        resizer.releasePointerCapture(activePointerId);
        activePointerId = null;
        if (currentPercent !== null) {
            persistPercent(currentPercent);
        }
        emitResizeEvent();
    }

    resizer.addEventListener('pointerdown', function (event) {
        if (isStackedLayout()) {
            return;
        }
        if (event.pointerType === 'mouse' && event.button !== 0) {
            return;
        }
        event.preventDefault();
        activePointerId = event.pointerId;
        isDragging = true;
        resizer.classList.add('is-active');
        resizer.setPointerCapture(activePointerId);
        handlePointerMove(event);
    });

    resizer.addEventListener('pointermove', handlePointerMove);
    resizer.addEventListener('pointerup', stopDragging);
    resizer.addEventListener('pointercancel', stopDragging);

    resizer.addEventListener('dblclick', function () {
        if (isStackedLayout()) {
            return;
        }
        applyPercent(DEFAULT_PERCENT);
        persistPercent(DEFAULT_PERCENT);
        emitResizeEvent();
    });

    resizer.addEventListener('keydown', function (event) {
        if (isStackedLayout()) {
            return;
        }
        var step = 3;
        var handled = false;
        var base = currentPercent !== null ? currentPercent : DEFAULT_PERCENT;

        if (event.key === 'ArrowLeft') {
            base = Math.max(MIN_PERCENT, base - step);
            handled = true;
        } else if (event.key === 'ArrowRight') {
            base = Math.min(100 - MIN_PERCENT, base + step);
            handled = true;
        } else if (event.key === 'Home') {
            base = MIN_PERCENT;
            handled = true;
        } else if (event.key === 'End') {
            base = 100 - MIN_PERCENT;
            handled = true;
        }

        if (handled) {
            event.preventDefault();
            applyPercent(base);
            persistPercent(base);
            emitResizeEvent();
        }
    });

    function applyResponsiveState() {
        if (isStackedLayout()) {
            viewerPane.style.removeProperty('flex-basis');
            tablePane.style.removeProperty('flex-basis');
            resizer.setAttribute('aria-hidden', 'true');
        } else {
            resizer.removeAttribute('aria-hidden');
            var target = currentPercent;
            if (target === null) {
                var stored = readStoredPercent();
                if (stored !== null) {
                    target = stored;
                } else {
                    target = DEFAULT_PERCENT;
                }
            }
            applyPercent(target);
        }
        emitResizeEvent();
    }

    window.addEventListener('resize', applyResponsiveState);
    applyResponsiveState();
});

