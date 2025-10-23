
function myFunction(button) {
    var table = $('#sample_data').DataTable();
    var isLastPage = false;
    var nextUrl = '/suivant';
    var finalizeUrl = '/finaliser';

    if (button) {
        var dataset = button.dataset || {};
        isLastPage = dataset.lastPage === '1';
        if (dataset.nextUrl) {
            nextUrl = dataset.nextUrl;
        }
        if (dataset.finalizeUrl) {
            finalizeUrl = dataset.finalizeUrl;
        }
    }

    var redirectTriggered = false;

    function toNumber(value) {
        var parsed = parseFloat(value);
        return Number.isNaN(parsed) ? 0 : parsed;
    }

    function showValidationDialog() {
        return new Promise(function (resolve) {
            var modalId = 'validationConfirmModal';
            var confirmEvent = 'click.validationConfirm';
            var hiddenEvent = 'hidden.bs.modal.validationConfirm';
            var $modal = $('#' + modalId);

            if ($modal.length === 0) {
                var modalTemplate = [
                    '<div class="modal fade" id="' + modalId + '" tabindex="-1" role="dialog" aria-labelledby="' + modalId + 'Label" aria-hidden="true">',
                    '  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">',
                    '    <div class="modal-content shadow-lg border-0 rounded">',
                    '      <div class="modal-header bg-primary text-white">',
                    '        <h5 class="modal-title" id="' + modalId + 'Label">Une derni&egrave;re v&eacute;rification</h5>',
                    '        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">',
                    '          <span aria-hidden="true">&times;</span>',
                    '        </button>',
                    '      </div>',
                    '      <div class="modal-body text-center">',
                    '        <p class="font-weight-bold mb-2">&#x2728; Pr&ecirc;t &agrave; valider l&#39;ensemble de l&#39;&eacute;tat financier&nbsp;?</p>',
                    '        <p class="text-muted small mb-0">Prenez un instant pour v&eacute;rifier les montants et les notes avant de finaliser.</p>',
                    '      </div>',
                    '      <div class="modal-footer border-0 justify-content-between">',
                    '        <button type="button" class="btn btn-light" data-dismiss="modal">Continuer la v&eacute;rification</button>',
                    '        <button type="button" class="btn btn-primary" data-role="confirm-validation">Oui, valider</button>',
                    '      </div>',
                    '    </div>',
                    '  </div>',
                    '</div>'
                ].join('');
                $('body').append(modalTemplate);
                $modal = $('#' + modalId);
            }

            if ($modal.hasClass('show')) {
                resolve(false);
                return;
            }

            var $confirmButton = $modal.find('[data-role="confirm-validation"]');
            var resolved = false;

            function cleanup() {
                $confirmButton.off(confirmEvent);
                $modal.off(hiddenEvent);
            }

            function onConfirm() {
                if (resolved) {
                    return;
                }
                resolved = true;
                cleanup();
                $modal.modal('hide');
                resolve(true);
            }

            function onCancel() {
                if (resolved) {
                    return;
                }
                resolved = true;
                cleanup();
                resolve(false);
            }

            $confirmButton.off(confirmEvent);
            $modal.off(hiddenEvent);

            $confirmButton.on(confirmEvent, onConfirm);
            $modal.on(hiddenEvent, onCancel);
            $modal.modal('show');
        });
    }

    function redirectToPage() {
        if (isLastPage) {
            showValidationDialog().then(function (confirmed) {
                if (confirmed) {
                    window.location.href = finalizeUrl;
                }
            });
            return true;
        }

        window.location.href = nextUrl;
        return true;
    }

    var test = 0;

    table.rows().every(function () {
        var rowData = this.data();
        var rowNode = this.node();

        if (rowData['code'] === 'EFA.11') {
            test = 1;
            var var1 = 0;
            var var2 = 0;
            var var3 = 0;
            var table1 = $('#sample_data').DataTable();
            var rowNodeVar1;
            var rowNodeVar2;
            var rowNodeVar3;

            table1.rows().every(function () {
                var rowDataX = this.data();
                var rowNodeVarX = this.node();

                if (rowDataX['code'] === 'EFA.11.01.06') {
                    rowNodeVar1 = rowNodeVarX;
                    var1 = rowDataX['value_n'];
                }
                if (rowDataX['code'] === 'EFA.01.01.01.10') {
                    rowNodeVar2 = rowNodeVarX;
                    var2 = rowDataX['value_n'];
                }
                if (rowDataX['code'] === 'EFA.11.01.03') {
                    rowNodeVar3 = rowNodeVarX;
                    var3 = rowDataX['value_n'];
                }
            });

            var total = toNumber(var1) + toNumber(var2) + toNumber(var3);
            var expected = toNumber(rowData['value_n']);

            if (Math.abs(total - expected) > 0.000001) {
                alert("EFA.11 = EFA.11.01.06 + EFA.01.01.01.10 + EFA.11.01.03");
                $(rowNode).addClass('green-background');
                if (rowNodeVar1 !== undefined) {
                    $(rowNodeVar1).addClass('blink-background');
                }
                if (rowNodeVar2 !== undefined) {
                    $(rowNodeVar2).addClass('blink-background');
                }
                if (rowNodeVar3 !== undefined) {
                    $(rowNodeVar3).addClass('blink-background');
                }
            } else {
                redirectTriggered = redirectToPage();
                if (redirectTriggered) {
                    return false;
                }
            }
        }
    });

    if (!redirectTriggered && test === 0) {
        redirectToPage();
    }

    return false;
}
