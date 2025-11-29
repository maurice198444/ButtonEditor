// resources/js/stores/documentStore.js

import { defineStore } from "pinia";

/**
 * Hält den aktuellen Dokument-State der Card.
 *
 * Struktur z. B.:
 * {
 *   meta: { width, height, background },
 *   elements: [
 *     {
 *       id: "el-1",
 *       type: "icon",
 *       position: { x, y },
 *       size: { width, height },
 *       style: { color, fontSize, ... },
 *       data: { icon, text, binding, ... },
 *       states: [...]
 *     }
 *   ]
 * }
 */
export const useDocumentStore = defineStore("document", {
    state: () => ({
        cardId: null,
        document: {
            meta: {
                width: 320,
                height: 180,
                background: "#020617",
            },
            elements: [],
        },
    }),

    getters: {
        doc(state) {
            return state.document;
        },
    },

    actions: {
        /**
         * Initialisierung mit Card-ID und Dokument vom Backend.
         */
        init(cardId, document) {
            this.cardId = Number(cardId);

            const fallback = {
                meta: {
                    width: 320,
                    height: 180,
                    background: "#020617",
                },
                elements: [],
            };

            if (!document) {
                this.document = fallback;
                return;
            }

            this.document = {
                meta: {
                    width: document.meta?.width ?? fallback.meta.width,
                    height: document.meta?.height ?? fallback.meta.height,
                    background:
                        document.meta?.background ?? fallback.meta.background,
                },
                elements: Array.isArray(document.elements)
                    ? document.elements
                    : [],
            };
        },

        /**
         * Kompletten Dokument-State setzen (z. B. nach Laden).
         */
        setDocument(document) {
            this.init(this.cardId ?? 0, document);
        },

        /**
         * Generischer Update-Helper für ein Element.
         */
        updateElement(id, patch) {
            if (!this.document) return;

            const index = this.document.elements.findIndex(
                (el) => el.id === id,
            );
            if (index === -1) return;

            const current = this.document.elements[index];

            this.document.elements[index] = {
                ...current,
                ...patch,
                style: {
                    ...(current.style ?? {}),
                    ...(patch.style ?? {}),
                },
                position: {
                    ...(current.position ?? {}),
                    ...(patch.position ?? {}),
                },
                size: {
                    ...(current.size ?? {}),
                    ...(patch.size ?? {}),
                },
                data: {
                    ...(current.data ?? {}),
                    ...(patch.data ?? {}),
                },
            };
        },

        /**
         * Nur Style eines Elements patchen.
         */
        updateElementStyle(id, stylePatch) {
            this.updateElement(id, { style: stylePatch });
        },

        /**
         * Nur Data eines Elements patchen (z. B. Text, Icon-Name).
         */
        updateElementData(id, dataPatch) {
            this.updateElement(id, { data: dataPatch });
        },
    },
});
