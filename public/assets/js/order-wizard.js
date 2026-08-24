/* Order validation stepper — single-page flow.
 * Every section stays visible; the stepper shows how far the order is in
 * the validation process (from allowed_actions / status) and scrolls to a
 * step's first section on click. All business actions remain the panels'
 * own buttons and JS.
 */
(function () {
    'use strict';

    var stepper = document.getElementById('order-wizard-stepper');
    if (!stepper) return;

    var allowed = [];
    try { allowed = JSON.parse(stepper.dataset.allowedActions || '[]') || []; } catch (e) { allowed = []; }
    var can = function (a) { return allowed.indexOf(a) !== -1; };
    var status = (stepper.dataset.orderStatus || '').toUpperCase();

    // Progress: the first step the supplier can still act on.
    var progress = 5;
    if (can('accept') || can('reject')) progress = 1;
    else if (can('edit_final_items')) progress = 2;
    else if (can('edit_transport')) progress = 3;
    else if (can('finalization_check') || can('finalize')) progress = 4;
    var allDone = status === 'FINALIZED' || status === 'ARCHIVED' || status === 'CANCELLED' || status === 'REJECTED';

    var dots = Array.prototype.slice.call(stepper.querySelectorAll('.wiz-step'));
    dots.forEach(function (d, i) {
        var n = i + 1;
        var done = allDone || n < progress;
        var active = !allDone && n === progress;
        d.classList.toggle('done', done);
        d.classList.toggle('active', active);
        d.classList.toggle('todo', !done && !active);
        var dotEl = d.querySelector('.wiz-dot');
        if (dotEl && done) dotEl.innerHTML = '<i class="bi bi-check"></i>';

        d.addEventListener('click', function () {
            var target = document.querySelector('[data-wiz~="' + n + '"]');
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
})();
