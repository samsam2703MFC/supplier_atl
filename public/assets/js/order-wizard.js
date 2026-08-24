/* Order validation wizard — navigation layer over the existing order page.
 * Shows/hides the page's [data-wiz] sections per step; all business actions
 * remain the panels' own buttons and JS. State: URL hash (#wiz-N) + a
 * localStorage flag for wizard vs classic mode.
 */
(function () {
    'use strict';

    var stepper = document.getElementById('order-wizard-stepper');
    if (!stepper) return;

    var STEPS = 5;
    var MODE_KEY = 'supplierOrderWizardMode';

    var allowed = [];
    try { allowed = JSON.parse(stepper.dataset.allowedActions || '[]') || []; } catch (e) { allowed = []; }
    var can = function (a) { return allowed.indexOf(a) !== -1; };

    // First step the supplier can act on; consultation lands on documents.
    var initialStep = 5;
    if (can('accept') || can('reject')) initialStep = 1;
    else if (can('edit_final_items')) initialStep = 2;
    else if (can('edit_transport')) initialStep = 3;
    else if (can('finalization_check') || can('finalize')) initialStep = 4;

    var current = initialStep;
    var hashMatch = (window.location.hash || '').match(/^#wiz-([1-5])$/);
    if (hashMatch) current = parseInt(hashMatch[1], 10);

    var mode = 'wizard';
    try { mode = localStorage.getItem(MODE_KEY) || 'wizard'; } catch (e) { /* private mode */ }

    var sections = Array.prototype.slice.call(document.querySelectorAll('[data-wiz]'));
    var dots = Array.prototype.slice.call(stepper.querySelectorAll('.wiz-step'));
    var nav = document.getElementById('order-wizard-nav');
    var btnPrev = document.getElementById('wiz-prev');
    var btnNext = document.getElementById('wiz-next');
    var navNote = document.getElementById('wiz-note');
    var toggles = Array.prototype.slice.call(document.querySelectorAll('.js-wiz-toggle'));

    function labelFor(step) {
        var dot = dots[step - 1];
        return dot ? (dot.querySelector('.wiz-lab') || {}).textContent || '' : '';
    }

    function render() {
        document.body.classList.toggle('wiz-mode', mode === 'wizard');
        toggles.forEach(function (t) {
            t.textContent = mode === 'wizard' ? t.dataset.labelClassic : t.dataset.labelWizard;
        });
        if (mode !== 'wizard') return;

        sections.forEach(function (s) {
            var steps = (s.dataset.wiz || '').split(' ');
            s.classList.toggle('wiz-visible', steps.indexOf(String(current)) !== -1);
        });
        dots.forEach(function (d, i) {
            var n = i + 1;
            d.classList.toggle('done', n < current);
            d.classList.toggle('active', n === current);
            d.classList.toggle('todo', n > current);
            var dotEl = d.querySelector('.wiz-dot');
            if (dotEl) dotEl.innerHTML = n < current ? '<i class="bi bi-check"></i>' : String(n);
        });
        if (btnPrev) btnPrev.disabled = current === 1;
        if (btnNext) {
            if (current === STEPS) {
                btnNext.innerHTML = '<i class="bi bi-check2-all me-1"></i>' + (nav.dataset.labelFinish || 'Terminer');
            } else {
                btnNext.innerHTML = (nav.dataset.labelNext || 'Continuer') + ' — ' + labelFor(current + 1) + ' <i class="bi bi-arrow-right ms-1"></i>';
            }
        }
        if (navNote) navNote.textContent = labelFor(current);
        if (window.history && history.replaceState) {
            history.replaceState(null, '', '#wiz-' + current);
        }
    }

    function go(step) {
        current = Math.min(STEPS, Math.max(1, step));
        render();
        var heading = document.querySelector('.page-heading') || document.body;
        heading.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    dots.forEach(function (d, i) {
        d.addEventListener('click', function () { go(i + 1); });
    });
    if (btnPrev) btnPrev.addEventListener('click', function () { go(current - 1); });
    if (btnNext) btnNext.addEventListener('click', function () {
        if (current === STEPS) { window.location.href = nav.dataset.ordersUrl || '#'; return; }
        go(current + 1);
    });
    toggles.forEach(function (t) {
        t.addEventListener('click', function () {
            mode = mode === 'wizard' ? 'classic' : 'wizard';
            try { localStorage.setItem(MODE_KEY, mode); } catch (e) { /* private mode */ }
            render();
        });
    });

    render();
})();
