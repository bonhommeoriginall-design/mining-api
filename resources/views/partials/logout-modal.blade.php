<div class="logout-modal" id="logout-modal" role="dialog" aria-modal="true" aria-labelledby="logout-modal-title" hidden>
    <div class="logout-modal-backdrop" data-logout-close></div>
    <div class="logout-modal-card">
        <div class="logout-modal-icon" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
        </div>
        <h2 class="logout-modal-title" id="logout-modal-title">Se déconnecter ?</h2>
        <p class="logout-modal-text">
            Vous allez quitter votre session MINING IA. Vous pourrez vous reconnecter à tout moment.
        </p>
        <div class="logout-modal-actions">
            <button type="button" class="logout-modal-btn logout-modal-btn-cancel" data-logout-close>
                Annuler
            </button>
            <button type="button" class="logout-modal-btn logout-modal-btn-confirm" id="logout-modal-confirm">
                Déconnexion
            </button>
        </div>
    </div>
</div>

<form id="global-logout-form" action="{{ route('logout') }}" method="POST" hidden>
    @csrf
</form>

<script>
(() => {
    const modal = document.getElementById('logout-modal');
    const confirmBtn = document.getElementById('logout-modal-confirm');
    const defaultLogoutForm = document.getElementById('global-logout-form');
    if (!modal || !confirmBtn) return;

    let pendingForm = null;

    function openModal(form) {
        pendingForm = form;
        modal.hidden = false;
        document.body.classList.add('logout-modal-open');
        confirmBtn.focus();
    }

    function closeModal() {
        modal.hidden = true;
        document.body.classList.remove('logout-modal-open');
        pendingForm = null;
    }

    document.querySelectorAll('[data-logout-trigger]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const form = btn.closest('form') ?? defaultLogoutForm;
            if (form) openModal(form);
        });
    });

    modal.querySelectorAll('[data-logout-close]').forEach((el) => {
        el.addEventListener('click', closeModal);
    });

    confirmBtn.addEventListener('click', () => {
        if (pendingForm) pendingForm.submit();
    });

    document.addEventListener('keydown', (e) => {
        if (!modal.hidden && e.key === 'Escape') closeModal();
    });
})();
</script>
