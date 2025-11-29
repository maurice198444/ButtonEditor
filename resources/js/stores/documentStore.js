// resources/js/stores/documentStore.js

import { defineStore } from 'pinia';

/* Document store holding the current card document state. */
export const useDocumentStore = defineStore('document', {
    state: () => ({
        cardId: null,
        document: null,
    }),
    actions: {
        /**
         * Initialize the store with a card ID and document payload.
         */
        init(cardId, document) {
            this.cardId = Number(cardId);
            this.document = document;
        },
        /**
         * Replace the entire document.
         */
        setDocument(document) {
            this.document = document;
        },
        /**
         * Update a document element by merging a partial payload.
         */
        updateElement(id, partial) {
            if (!this.document?.elements) {
                return;
            }

            const index = this.document.elements.findIndex((el) => el.id === id);
            if (index === -1) {
                return;
            }

            this.document.elements[index] = Object.assign(
                {},
                this.document.elements[index],
                partial,
            );
        },
    },
});
