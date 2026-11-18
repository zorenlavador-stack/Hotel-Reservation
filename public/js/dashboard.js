/* ==========================================================
   GRAND STAY - dashboard scripts
   Opens the Pay and Cancel windows (modals) on the dashboard
   ========================================================== */

(function () {
    var payModal    = document.getElementById('payModal');
    var cancelModal = document.getElementById('cancelModal');

    // Nothing to do if the modals are not on this page
    if (!payModal || !cancelModal) {
        return;
    }

    // "Pay now": fill the window with that reservation's details, then open it
    document.querySelectorAll('[data-pay]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('payForm').action = btn.dataset.action;
            document.getElementById('payInfo').textContent = btn.dataset.info;
            document.getElementById('payBalance').textContent = 'Balance due: ₱' + btn.dataset.balanceText;
            document.getElementById('pay_method').value = '';

            var amount = document.getElementById('pay_amount');
            amount.value = btn.dataset.balance;
            amount.max = btn.dataset.balance;

            payModal.showModal();
        });
    });

    // "Cancel": same idea for the confirmation window
    document.querySelectorAll('[data-cancel]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('cancelForm').action = btn.dataset.action;
            document.getElementById('cancelInfo').textContent = btn.dataset.info;
            document.getElementById('cancelNote').textContent = btn.dataset.note;

            cancelModal.showModal();
        });
    });

    // Close with the X, the secondary button, or a click on the dark background
    document.querySelectorAll('dialog.modal').forEach(function (dialog) {
        dialog.querySelectorAll('[data-close]').forEach(function (closeBtn) {
            closeBtn.addEventListener('click', function () {
                dialog.close();
            });
        });

        dialog.addEventListener('click', function (e) {
            if (e.target === dialog) {
                dialog.close();
            }
        });
    });
})();