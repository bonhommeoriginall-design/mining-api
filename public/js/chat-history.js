/**
 * Historique local des conversations chat (localStorage, par utilisateur).
 */
window.MiningChatHistory = class MiningChatHistory {
    constructor(userId) {
        this.storageKey = `chat_conversations_v1_user_${userId}`;
        this.activeKey = `chat_active_conversation_id_v1_user_${userId}`;
        this.maxConversations = 30;
    }

    loadAll() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            if (!raw) return [];
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    }

    saveAll(conversations) {
        localStorage.setItem(
            this.storageKey,
            JSON.stringify(conversations.slice(0, this.maxConversations)),
        );
    }

    getActiveId() {
        return localStorage.getItem(this.activeKey);
    }

    setActiveId(id) {
        if (id) {
            localStorage.setItem(this.activeKey, id);
        } else {
            localStorage.removeItem(this.activeKey);
        }
    }

    titleFromMessages(messages) {
        for (const m of messages) {
            if (m.is_user && String(m.text || '').trim()) {
                const t = String(m.text).trim();
                return t.length <= 60 ? t : `${t.slice(0, 57)}…`;
            }
        }
        return 'Conversation';
    }

    saveConversation(messages, id = null) {
        if (!messages?.length) return null;

        const conversationId = id || this.getActiveId() || `conv_${Date.now()}`;
        const conversation = {
            id: conversationId,
            title: this.titleFromMessages(messages),
            updatedAt: messages[messages.length - 1]?.at || new Date().toISOString(),
            messages: messages.map((m) => ({
                id: m.id,
                text: m.text,
                is_user: !!m.is_user,
                citations: m.citations || [],
                used_llm: !!m.used_llm,
                at: m.at || new Date().toISOString(),
            })),
        };

        const current = this.loadAll().filter((c) => c.id !== conversationId);
        this.saveAll([conversation, ...current]);
        this.setActiveId(conversationId);
        return conversationId;
    }

    deleteConversation(id) {
        const updated = this.loadAll().filter((c) => c.id !== id);
        this.saveAll(updated);
        if (this.getActiveId() === id) {
            this.setActiveId(null);
        }
        return updated;
    }

    clearAll() {
        localStorage.removeItem(this.storageKey);
        localStorage.removeItem(this.activeKey);
    }

    findById(id) {
        return this.loadAll().find((c) => c.id === id) || null;
    }
};
