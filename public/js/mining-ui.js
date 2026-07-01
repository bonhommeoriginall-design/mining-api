/**
 * Comportements UI communs — MINING IA (portail web).
 */
(() => {
    document.querySelectorAll('.alert[data-auto-dismiss]').forEach((alert) => {
        const delay = Number(alert.dataset.autoDismiss) || 5000;
        window.setTimeout(() => {
            alert.classList.add('alert-hide');
            window.setTimeout(() => alert.remove(), 320);
        }, delay);
    });

    document.querySelectorAll('[data-alert-dismiss]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const alert = btn.closest('.alert');
            alert?.classList.add('alert-hide');
            window.setTimeout(() => alert?.remove(), 320);
        });
    });

    document.querySelectorAll('[data-password-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const wrap = btn.closest('.password-field');
            const input = wrap?.querySelector('input');
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.setAttribute('aria-pressed', show ? 'true' : 'false');
            btn.setAttribute('aria-label', show ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        });
    });

    document.querySelectorAll('[data-example-question]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = document.getElementById('chat-input');
            if (!input) return;
            input.value = btn.dataset.exampleQuestion || btn.textContent?.trim() || '';
            input.focus();
            if (input.tagName === 'TEXTAREA') {
                input.style.height = 'auto';
                input.style.height = `${input.scrollHeight}px`;
            }
        });
    });

    document.querySelectorAll('[data-file-input]').forEach((input) => {
        const label = input.closest('.file-drop')?.querySelector('[data-file-label]');
        if (!label) return;
        input.addEventListener('change', () => {
            const name = input.files?.[0]?.name;
            label.textContent = name || label.dataset.fileDefault || 'Choisir un fichier';
        });
    });
})();
