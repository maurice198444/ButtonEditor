<template>
    <main class="editor-page">
        <section class="editor-main">
            <CanvasEngine v-if="documentStore.document" />
        </section>

        <aside class="editor-sidebar">
            <PropertiesPanel />
        </aside>
    </main>
</template>

<script setup>
import { onMounted } from "vue";
import CanvasEngine from "../components/CanvasEngine.vue";
import PropertiesPanel from "../components/PropertiesPanel.vue";
import { useDocumentStore } from "../stores/documentStore";

const documentStore = useDocumentStore();
const appElement = document.getElementById("app");
const cardId = appElement?.dataset.cardId;

/**
 * Load card document on mount and initialize the document store.
 */
onMounted(async () => {
    if (!cardId) {
        console.error("Missing cardId on app element");
        return;
    }

    try {
        const response = await fetch(`/cards/${cardId}`);
        if (!response.ok) {
            console.error("Failed to fetch card", response.statusText);
            return;
        }

        const payload = await response.json();
        documentStore.init(cardId, payload.document);
    } catch (error) {
        console.error("Error fetching card", error);
    }
});
</script>

<style scoped>
.editor-page {
    display: flex;
    min-height: 100vh;
    background: #111827;
    color: #e5e7eb;
}

.editor-main {
    flex: 1;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 1.5rem;
}

.editor-sidebar {
    width: 320px;
    border-left: 1px solid #374151;
    padding: 1.5rem;
    background: #020617;
}
</style>
