@php
    $portalErrors = $errors ?? null;
    $initialSettingsTab = session('settings_tab');
    if (! $initialSettingsTab && $portalErrors && $portalErrors->any() && $portalErrors->hasAny(['current_password', 'password', 'password_confirmation'])) {
        $initialSettingsTab = 'security';
    }
    $initialProfileOpen = $portalErrors && $portalErrors->hasAny(['name', 'username', 'avatar']);
@endphp
<script>
(() => {
    const accountTrigger = document.getElementById('agent-account-trigger');
    const accountMenu = document.getElementById('agent-account-menu');
    const helpModal = document.getElementById('agent-help-modal');
    const helpBtn = document.getElementById('agent-account-help');
    const settingsModal = document.getElementById('agent-settings-modal');
    const profileModal = document.getElementById('agent-profile-modal');
    const profileNameInput = document.getElementById('agent-profile-name');
    const profileAvatarImg = document.getElementById('agent-profile-avatar-img');
    const profileAvatarInitials = document.getElementById('agent-profile-avatar-initials');
    const profileAvatarInput = document.getElementById('agent-profile-avatar-input');
    const profileAvatarBtn = document.getElementById('agent-profile-avatar-btn');
    const profileRemovePhotoBtn = document.getElementById('agent-profile-remove-photo');
    const profileRemoveAvatarField = document.getElementById('agent-profile-remove-avatar');
    let profileAvatarObjectUrl = null;

    function closeAccountMenu() {
        accountMenu?.setAttribute('hidden', '');
        accountTrigger?.setAttribute('aria-expanded', 'false');
        accountTrigger?.classList.remove('is-open');
    }

    function openAccountMenu() {
        accountMenu?.removeAttribute('hidden');
        accountTrigger?.setAttribute('aria-expanded', 'true');
        accountTrigger?.classList.add('is-open');
    }

    accountTrigger?.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = accountTrigger.getAttribute('aria-expanded') === 'true';
        if (isOpen) closeAccountMenu();
        else openAccountMenu();
    });

    document.addEventListener('click', (e) => {
        if (!accountMenu || accountMenu.hasAttribute('hidden')) return;
        if (e.target.closest('.agent-account-wrap')) return;
        closeAccountMenu();
    });

    function closeProfileModal() {
        profileModal?.setAttribute('hidden', '');
        document.body.classList.remove('agent-profile-open');
    }

    function openProfileModal() {
        closeAccountMenu();
        closeSettingsModal();
        profileModal?.removeAttribute('hidden');
        document.body.classList.add('agent-profile-open');
        profileNameInput?.focus();
    }

    function switchSettingsTab(tab) {
        if (!settingsModal) return;
        settingsModal.querySelectorAll('[data-settings-tab]').forEach((btn) => {
            btn.classList.toggle('is-active', btn.dataset.settingsTab === tab);
        });
        settingsModal.querySelectorAll('[data-settings-panel]').forEach((panel) => {
            panel.classList.toggle('is-active', panel.dataset.settingsPanel === tab);
        });
    }

    function closeSettingsModal() {
        settingsModal?.setAttribute('hidden', '');
        document.body.classList.remove('agent-settings-open');
    }

    function openSettingsModal(tab = 'general') {
        closeAccountMenu();
        settingsModal?.removeAttribute('hidden');
        document.body.classList.add('agent-settings-open');
        switchSettingsTab(tab);
    }

    accountMenu?.querySelectorAll('[data-logout-trigger]').forEach((btn) => {
        btn.addEventListener('click', () => {
            closeAccountMenu();
            closeSettingsModal();
            closeProfileModal();
        });
    });

    document.querySelectorAll('[data-agent-profile-open]').forEach((el) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            openProfileModal();
        });
    });

    profileModal?.querySelectorAll('[data-agent-profile-close]').forEach((el) => {
        el.addEventListener('click', closeProfileModal);
    });

    profileNameInput?.addEventListener('input', () => {
        if (profileAvatarImg && !profileAvatarImg.hidden && profileAvatarImg.getAttribute('src')) return;
        const parts = profileNameInput.value.trim().split(/\s+/).filter(Boolean);
        let initials = parts.slice(0, 2).map((p) => p.charAt(0).toUpperCase()).join('');
        if (!initials) initials = profileNameInput.value.trim().slice(0, 2).toUpperCase();
        if (profileAvatarInitials) profileAvatarInitials.textContent = initials || '?';
    });

    function showProfileAvatarPhoto(src) {
        if (!profileAvatarImg || !profileAvatarInitials) return;
        profileAvatarImg.src = src;
        profileAvatarImg.hidden = false;
        profileAvatarInitials.hidden = true;
        profileRemovePhotoBtn?.removeAttribute('hidden');
    }

    function showProfileAvatarInitials() {
        if (!profileAvatarImg || !profileAvatarInitials) return;
        if (profileAvatarObjectUrl) {
            URL.revokeObjectURL(profileAvatarObjectUrl);
            profileAvatarObjectUrl = null;
        }
        profileAvatarImg.removeAttribute('src');
        profileAvatarImg.hidden = true;
        profileAvatarInitials.hidden = false;
        profileRemovePhotoBtn?.setAttribute('hidden', '');
    }

    profileAvatarBtn?.addEventListener('click', () => profileAvatarInput?.click());

    profileAvatarInput?.addEventListener('change', () => {
        const file = profileAvatarInput.files?.[0];
        if (!file) return;
        if (profileAvatarObjectUrl) URL.revokeObjectURL(profileAvatarObjectUrl);
        profileAvatarObjectUrl = URL.createObjectURL(file);
        showProfileAvatarPhoto(profileAvatarObjectUrl);
        if (profileRemoveAvatarField) profileRemoveAvatarField.value = '0';
    });

    profileRemovePhotoBtn?.addEventListener('click', () => {
        if (profileAvatarInput) profileAvatarInput.value = '';
        if (profileRemoveAvatarField) profileRemoveAvatarField.value = '1';
        showProfileAvatarInitials();
    });

    document.querySelectorAll('[data-agent-settings-open]').forEach((el) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            openSettingsModal(el.dataset.settingsTab || 'general');
        });
    });

    settingsModal?.querySelectorAll('[data-settings-tab]').forEach((btn) => {
        btn.addEventListener('click', () => switchSettingsTab(btn.dataset.settingsTab));
    });

    settingsModal?.querySelectorAll('[data-settings-tab-jump]').forEach((btn) => {
        btn.addEventListener('click', () => switchSettingsTab(btn.dataset.settingsTabJump));
    });

    settingsModal?.querySelectorAll('[data-agent-settings-close]').forEach((el) => {
        el.addEventListener('click', closeSettingsModal);
    });

    document.getElementById('agent-settings-open-history')?.addEventListener('click', () => {
        closeSettingsModal();
        document.getElementById('agent-chat-history')?.click();
    });

    document.getElementById('agent-settings-clear-history')?.addEventListener('click', () => {
        document.getElementById('chat-history-clear-all')?.click();
    });

    function closeHelpModal() {
        helpModal?.setAttribute('hidden', '');
        document.body.classList.remove('agent-help-open');
    }

    function openHelpModal() {
        closeAccountMenu();
        helpModal?.removeAttribute('hidden');
        document.body.classList.add('agent-help-open');
    }

    helpBtn?.addEventListener('click', openHelpModal);
    helpModal?.querySelectorAll('[data-agent-help-close]').forEach((el) => {
        el.addEventListener('click', closeHelpModal);
    });

    @if ($initialProfileOpen)
    openProfileModal();
    @elseif ($initialSettingsTab)
    openSettingsModal(@json($initialSettingsTab));
    @endif

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (profileModal && !profileModal.hasAttribute('hidden')) closeProfileModal();
        else if (settingsModal && !settingsModal.hasAttribute('hidden')) closeSettingsModal();
        else if (helpModal && !helpModal.hasAttribute('hidden')) closeHelpModal();
        else closeAccountMenu();
    });
})();
</script>
