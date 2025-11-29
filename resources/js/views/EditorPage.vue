<template>
    <main class="editor-page">
        <section class="editor-main">
            <CanvasEngine v-if="docLoaded" />
        </section>

        <aside class="editor-sidebar">
            <PropertiesPanel />
        </aside>
    </main>
</template>

<script setup>
import { computed, onMounted } from "vue";
import CanvasEngine from "../components/CanvasEngine.vue";
import PropertiesPanel from "../components/PropertiesPanel.vue";
import { useDocumentStore } from "../stores/documentStore";

const props = defineProps({
    cardId: {
        type: Number,
        required: true,
    },
});

const documentStore = useDocumentStore();

const docLoaded = computed(
    () =>
        !!documentStore.document &&
        Array.isArray(documentStore.document.elements)
);

onMounted(async () => {
    // Erwartet, dass /cards/{card} JSON mit { document: {...} } liefert
    const response = await fetch(`/cards/${props.cardId}`);
    if (!response.ok) {
        console.error("Failed to load card document");
        return;
    }

    const payload = await response.json();
    documentStore.init(props.cardId, payload.document);
});
</script>

<style scoped>
.editor-page {
    display: flex;
    height: 100vh;
    background: #020617;
    color: #e5e7eb;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI",
        sans-serif;
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
