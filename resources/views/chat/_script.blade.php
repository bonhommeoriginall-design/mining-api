<script src="{{ asset('js/chat-history.js') }}"></script>
<script>
(() => {
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const messages = document.getElementById('chat-messages');
    const submitBtn = document.getElementById('chat-submit');
    const csrf = document.querySelector('input[name="_token"]')?.value;
    const forceDark = {{ ($dark ?? false) ? 'true' : 'false' }};
    const STAFF_THEME_KEY = 'mining-staff-chat-theme';

    function isDark() {
        if (forceDark) return true;
        return document.getElementById('chat-page-staff')?.classList.contains('chat-staff-dark') === true;
    }
    const userId = {{ auth()->id() }};
    const initialMessages = @json($initialMessages ?? []);
    const history = new MiningChatHistory(userId);

    let sessionMessages = [...initialMessages];

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatWhen(at) {
        if (!at) return '';
        const date = new Date(at);
        if (Number.isNaN(date.getTime())) return '';
        const now = new Date();
        const diffMin = Math.floor((now - date) / 60000);
        if (diffMin < 1) return 'À l’instant';
        if (diffMin < 60) return `Il y a ${diffMin} min`;
        const diffH = Math.floor(diffMin / 60);
        if (diffH < 24) return `Il y a ${diffH} h`;
        const d = String(date.getDate()).padStart(2, '0');
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const h = String(date.getHours()).padStart(2, '0');
        const min = String(date.getMinutes()).padStart(2, '0');
        return `${d}/${m}/${date.getFullYear()} ${h}:${min}`;
    }

    function ensureThread() {
        let thread = messages.querySelector('.agent-chat-thread') || messages.querySelector('.chat-thread');
        if (!thread) {
            messages.querySelector('#chat-empty')?.remove();
            messages.querySelector('.chat-welcome')?.remove();
            messages.querySelector('.chat-staff-welcome')?.remove();
            thread = document.createElement('div');
            thread.className = isDark() ? 'agent-chat-thread' : 'chat-thread';
            messages.appendChild(thread);
        }
        return thread;
    }

    function renderCitations(citations) {
        if (!citations?.length) return '';
        if (isDark()) {
            let html = '<div class="agent-citations">';
            citations.forEach(c => {
                html += `<details class="agent-cite-item"><summary>${escapeHtml(c.filename)} · ~${c.score}%</summary>`;
                html += `<p>${escapeHtml(c.excerpt)}</p></details>`;
            });
            return html + '</div>';
        }
        let html = '<div class="chat-citations-staff">';
        citations.forEach(c => {
            html += `<details class="chat-cite-card"><summary><span class="chat-cite-name">${escapeHtml(c.filename)}</span> <span class="chat-cite-score">~${c.score}%</span></summary>`;
            html += `<p>${escapeHtml(c.excerpt)}</p></details>`;
        });
        return html + '</div>';
    }

    function appendMessage({ text, isUser, citations, usedLlm }, scroll = true) {
        const thread = ensureThread();
        const bubble = document.createElement('div');

        if (isDark()) {
            bubble.className = `agent-msg ${isUser ? 'agent-msg-user' : 'agent-msg-ai'}`;
            let inner = `<div class="agent-msg-content">${escapeHtml(text).replace(/\n/g, '<br>')}</div>`;
            if (!isUser && citations?.length) inner += renderCitations(citations);
            bubble.innerHTML = inner;
        } else {
            bubble.className = `chat-bubble ${isUser ? 'chat-bubble-user' : 'chat-bubble-ai'}`;
            let inner = `<div class="chat-bubble-text">${escapeHtml(text).replace(/\n/g, '<br>')}</div>`;
            if (!isUser && citations?.length) inner += renderCitations(citations);
            if (!isUser && usedLlm) inner += '<div class="chat-llm-tag">Réponse IA</div>';
            bubble.innerHTML = inner;
        }

        thread.appendChild(bubble);
        if (scroll) messages.scrollTop = messages.scrollHeight;
    }

    function renderAllMessages(list) {
        messages.innerHTML = '';
        if (!list.length) {
            if (isDark()) {
                messages.innerHTML = '<div class="agent-chat-empty" id="chat-empty"><div class="agent-chat-hero"><img src="{{ asset('images/mininglogo.png') }}" alt="" class="agent-chat-hero-logo" aria-hidden="true"><h1>Par quoi commençons-nous ?</h1><p class="agent-chat-hero-sub">Interrogez la réglementation minière avec des réponses documentées.</p></div><div class="chat-suggestions chat-suggestions-dark agent-chat-suggestions"><button type="button" class="chat-suggestion" data-example-question="Quelles sont les obligations des titulaires de permis miniers ?">Obligations des titulaires</button><button type="button" class="chat-suggestion" data-example-question="Que dit le texte sur les coopératives minières ?">Coopératives minières</button><button type="button" class="chat-suggestion" data-example-question="Quelles procédures pour l\'exploration ?">Procédures d\'exploration</button></div></div>';
            } else {
                messages.innerHTML = `
                    <div class="chat-staff-welcome" id="chat-empty">
                        <div class="chat-staff-hero">
                            <img src="{{ asset('images/mininglogo.png') }}" alt="" class="chat-staff-hero-logo" aria-hidden="true">
                            <h2>Par quoi commençons-nous ?</h2>
                            <p>Interrogez la réglementation minière avec des réponses documentées et des sources citées.</p>
                        </div>
                        <div class="chat-suggestions chat-staff-suggestions">
                            <button type="button" class="chat-suggestion" data-example-question="Quelles sont les obligations des titulaires de permis miniers ?">Obligations des titulaires</button>
                            <button type="button" class="chat-suggestion" data-example-question="Que dit le texte sur les coopératives minières ?">Coopératives minières</button>
                            <button type="button" class="chat-suggestion" data-example-question="Quelles procédures pour l'exploration ?">Procédures d'exploration</button>
                        </div>
                    </div>`;
            }
            return;
        }

        list.forEach((m) => appendMessage({
            text: m.text,
            isUser: m.is_user,
            citations: m.citations || [],
            usedLlm: m.used_llm,
        }, false));
        messages.scrollTop = messages.scrollHeight;
    }

    function persistSessionMessages() {
        if (!sessionMessages.length) return;
        history.saveConversation(sessionMessages);
    }

    function syncInitialSessionToHistory() {
        if (!sessionMessages.length) return;
        const activeId = history.getActiveId();
        const existing = activeId ? history.findById(activeId) : null;
        if (!existing || existing.messages.length !== sessionMessages.length) {
            history.saveConversation(sessionMessages, activeId || undefined);
        }
    }

    function appendTyping() {
        const thread = ensureThread();
        const el = document.createElement('div');
        el.className = isDark() ? 'agent-msg agent-msg-ai agent-typing' : 'chat-bubble chat-bubble-ai chat-typing';
        el.id = 'chat-typing';
        el.textContent = 'Réflexion en cours…';
        thread.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
    }

    function removeTyping() {
        document.getElementById('chat-typing')?.remove();
    }

    async function restoreOnServer(conversationId, msgs) {
        await fetch('{{ route('chat.restore') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({
                conversation_id: conversationId,
                messages: msgs,
            }),
        });
    }

    const confirmModal = document.getElementById('chat-confirm-modal');
    const confirmTitle = document.getElementById('chat-confirm-title');
    const confirmText = document.getElementById('chat-confirm-text');
    const confirmPreview = document.getElementById('chat-confirm-preview');
    const confirmHint = document.getElementById('chat-confirm-hint');
    const confirmIcon = document.getElementById('chat-confirm-icon');
    const confirmOkBtn = document.getElementById('chat-confirm-ok');
    let confirmResolver = null;

    function conversationPreview() {
        if (!sessionMessages.length) return '';
        const lastUser = [...sessionMessages].reverse().find((m) => m.is_user);
        const text = (lastUser?.text || sessionMessages[sessionMessages.length - 1]?.text || '').trim();
        if (!text) return '';
        return text.length > 90 ? `${text.slice(0, 87)}…` : text;
    }

    function closeConfirmModal(result = false) {
        if (!confirmModal) return;
        confirmModal.hidden = true;
        document.body.classList.remove('logout-modal-open');
        const resolve = confirmResolver;
        confirmResolver = null;
        resolve?.(result);
    }

    function openConfirmModal({
        title,
        text,
        hint = '',
        preview = '',
        confirmLabel,
        variant = 'primary',
    }) {
        return new Promise((resolve) => {
            if (!confirmModal) {
                resolve(window.confirm(`${title}\n\n${text}`));
                return;
            }

            confirmResolver = resolve;
            confirmTitle.textContent = title;
            confirmText.textContent = text;
            confirmHint.textContent = hint;
            confirmHint.hidden = !hint;
            confirmPreview.textContent = preview ? `« ${preview} »` : '';
            confirmPreview.hidden = !preview;
            confirmOkBtn.textContent = confirmLabel;
            confirmOkBtn.classList.remove('chat-confirm-btn-primary', 'chat-confirm-btn-danger');
            confirmOkBtn.classList.add(
                variant === 'danger' ? 'chat-confirm-btn-danger' : 'chat-confirm-btn-primary',
            );
            confirmIcon?.classList.toggle('chat-confirm-icon-danger', variant === 'danger');
            confirmIcon?.classList.toggle('chat-confirm-icon-new', variant !== 'danger');

            confirmModal.hidden = false;
            document.body.classList.add('logout-modal-open');
            confirmOkBtn.focus();
        });
    }

    confirmModal?.querySelectorAll('[data-chat-confirm-close]').forEach((el) => {
        el.addEventListener('click', () => closeConfirmModal(false));
    });
    confirmOkBtn?.addEventListener('click', () => closeConfirmModal(true));
    document.addEventListener('keydown', (e) => {
        if (!confirmModal?.hidden && e.key === 'Escape') closeConfirmModal(false);
    });

    async function confirmNewChat() {
        if (!sessionMessages.length) return true;
        const preview = conversationPreview();
        const count = sessionMessages.length;
        return openConfirmModal({
            title: 'Nouvelle conversation ?',
            text: 'Vous allez quitter le fil actuel et ouvrir un chat vide.',
            hint: 'La conversation en cours sera conservée dans l’historique. Vous pourrez la rouvrir à tout moment.',
            preview,
            confirmLabel: 'Nouvelle conversation',
            variant: 'primary',
        });
    }

    async function confirmClearHistory() {
        return openConfirmModal({
            title: 'Effacer tout l’historique ?',
            text: 'Toutes les conversations enregistrées sur cet appareil seront supprimées.',
            hint: 'Cette action est définitive et ne peut pas être annulée.',
            confirmLabel: 'Tout effacer',
            variant: 'danger',
        });
    }

    async function newChat(skipConfirm = false) {
        if (!skipConfirm && sessionMessages.length && !await confirmNewChat()) {
            return;
        }
        persistSessionMessages();
        await fetch('{{ route('chat.clear') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        });
        sessionMessages = [];
        history.setActiveId(null);
        renderAllMessages([]);
        closeHistoryPanel();
        input?.focus();
    }

    async function loadConversation(conversationId) {
        const conv = history.findById(conversationId);
        if (!conv) return;

        sessionMessages = conv.messages.map((m) => ({ ...m }));
        history.setActiveId(conv.id);
        await restoreOnServer(conv.id, sessionMessages);
        renderAllMessages(sessionMessages);
        closeHistoryPanel();
        input?.focus();
    }

    const panel = document.getElementById('chat-history-panel');
    const listEl = document.getElementById('chat-history-list');
    const emptyEl = document.getElementById('chat-history-empty');

    function renderHistoryList() {
        const conversations = history.loadAll();
        const activeId = history.getActiveId();
        listEl.innerHTML = '';

        if (!conversations.length) {
            emptyEl.hidden = false;
            return;
        }

        emptyEl.hidden = true;
        conversations.forEach((conv) => {
            const li = document.createElement('li');
            li.className = 'chat-history-item' + (conv.id === activeId ? ' is-active' : '');

            const preview = conv.messages.length
                ? conv.messages[conv.messages.length - 1].text
                : '';

            li.innerHTML = `
                <button type="button" class="chat-history-item-btn" data-id="${escapeHtml(conv.id)}">
                    <span class="chat-history-item-title">${escapeHtml(conv.title)}</span>
                    ${preview ? `<span class="chat-history-item-preview">${escapeHtml(preview)}</span>` : ''}
                    <span class="chat-history-item-meta">${conv.messages.length} message(s) · ${formatWhen(conv.updatedAt)}</span>
                </button>
                <button type="button" class="chat-history-item-delete" data-id="${escapeHtml(conv.id)}" title="Supprimer" aria-label="Supprimer">&times;</button>
            `;
            listEl.appendChild(li);
        });
    }

    function openHistoryPanel() {
        renderHistoryList();
        panel.hidden = false;
        panel.setAttribute('aria-hidden', 'false');
        document.body.classList.add('chat-history-open');
    }

    function closeHistoryPanel() {
        panel.hidden = true;
        panel.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('chat-history-open');
    }

    document.getElementById('chat-history-open')?.addEventListener('click', openHistoryPanel);
    document.getElementById('agent-chat-history')?.addEventListener('click', openHistoryPanel);
    document.getElementById('chat-history-close')?.addEventListener('click', closeHistoryPanel);
    document.getElementById('chat-history-backdrop')?.addEventListener('click', closeHistoryPanel);
    document.getElementById('chat-history-new')?.addEventListener('click', () => newChat(false));
    document.getElementById('chat-history-clear-all')?.addEventListener('click', async () => {
        if (!await confirmClearHistory()) return;
        history.clearAll();
        await newChat(true);
        renderHistoryList();
    });

    listEl?.addEventListener('click', async (e) => {
        const deleteBtn = e.target.closest('.chat-history-item-delete');
        if (deleteBtn) {
            e.stopPropagation();
            const id = deleteBtn.dataset.id;
            if (id === history.getActiveId()) {
                await newChat(true);
            }
            history.deleteConversation(id);
            renderHistoryList();
            return;
        }

        const btn = e.target.closest('.chat-history-item-btn');
        if (btn) {
            await loadConversation(btn.dataset.id);
        }
    });

    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        const userMsg = {
            id: `u_${Date.now()}`,
            text,
            is_user: true,
            citations: [],
            at: new Date().toISOString(),
        };
        sessionMessages.push(userMsg);
        appendMessage({ text, isUser: true, citations: [] });
        input.value = '';
        autoResizeInput();
        submitBtn.disabled = true;
        appendTyping();

        try {
            const res = await fetch('{{ route('chat.ask') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ message: text }),
            });
            const data = await res.json();
            removeTyping();
            if (!res.ok) throw new Error(data.message || 'Erreur serveur');

            const aiMsg = {
                id: `a_${Date.now()}`,
                text: data.reply,
                is_user: false,
                citations: data.citations || [],
                used_llm: !!data.used_llm,
                at: new Date().toISOString(),
            };
            sessionMessages.push(aiMsg);
            persistSessionMessages();

            appendMessage({
                text: data.reply,
                isUser: false,
                citations: data.citations,
                usedLlm: data.used_llm,
            });
        } catch (err) {
            removeTyping();
            sessionMessages.pop();
            appendMessage({
                text: 'Désolé, une erreur est survenue : ' + err.message,
                isUser: false,
                citations: [],
            });
        } finally {
            submitBtn.disabled = false;
            input.focus();
        }
    });

    document.getElementById('agent-new-chat')?.addEventListener('click', () => newChat(false));
    document.getElementById('chat-clear')?.addEventListener('click', () => newChat(false));

    syncInitialSessionToHistory();

    messages?.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-example-question]');
        if (!btn) return;
        if (!input) return;
        input.value = btn.dataset.exampleQuestion || btn.textContent?.trim() || '';
        autoResizeInput();
        input.focus();
    });

    function autoResizeInput() {
        if (!input || input.tagName !== 'TEXTAREA') return;
        input.style.height = 'auto';
        input.style.height = `${Math.min(input.scrollHeight, 120)}px`;
    }

    input?.addEventListener('input', autoResizeInput);

    input?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form?.requestSubmit();
        }
    });

    autoResizeInput();

    function updateThemeToggle(dark) {
        const btn = document.getElementById('chat-theme-toggle');
        if (!btn) return;
        btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
        btn.title = dark ? 'Thème clair' : 'Thème sombre';
        btn.querySelector('.icon-sun')?.toggleAttribute('hidden', dark);
        btn.querySelector('.icon-moon')?.toggleAttribute('hidden', !dark);
    }

    function applyStaffTheme(dark, { rerender = true } = {}) {
        const page = document.getElementById('chat-page-staff');
        if (!page || forceDark) return;

        page.classList.toggle('chat-staff-dark', dark);
        document.body.classList.toggle('chat-staff-dark-mode', dark);
        document.documentElement.classList.remove('staff-chat-dark-boot');
        localStorage.setItem(STAFF_THEME_KEY, dark ? 'dark' : 'light');
        updateThemeToggle(dark);

        if (rerender) {
            renderAllMessages(sessionMessages);
        }
    }

    function initStaffTheme() {
        if (forceDark) return;
        const dark = localStorage.getItem(STAFF_THEME_KEY) === 'dark'
            || document.documentElement.classList.contains('staff-chat-dark-boot');
        if (!dark) {
            updateThemeToggle(false);
            document.documentElement.classList.remove('staff-chat-dark-boot');
            return;
        }
        applyStaffTheme(true);
    }

    document.getElementById('chat-theme-toggle')?.addEventListener('click', () => {
        applyStaffTheme(!isDark());
    });

    initStaffTheme();
})();
</script>
