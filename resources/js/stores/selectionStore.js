import { defineStore } from 'pinia';

export const useSelectionStore = defineStore('selection', {
    state: () => ({
        selectedId: null,
    }),
    actions: {
        select(id) {
            this.selectedId = id;
        },
        clear() {
            this.selectedId = null;
        },
    },
});
