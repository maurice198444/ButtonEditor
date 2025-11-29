<template>
    <div class="panel">
        <h2 class="panel-title">Eigenschaften</h2>

        <p v-if="!selectedElement" class="muted">
            Kein Element ausgewählt. Klicke ein Element im Canvas.
        </p>

        <div v-else class="fields">
            <!-- Basis -->
            <div class="field">
                <label>Element-ID</label>
                <div class="value">{{ selectedElement.id }}</div>
            </div>

            <div class="field">
                <label>Typ</label>
                <div class="value">{{ selectedElement.type }}</div>
            </div>

            <!-- Icon-spezifisch -->
            <template v-if="isIcon">
                <div class="field">
                    <label>Icon</label>
                    <input
                        v-model="form.icon"
                        type="text"
                        placeholder="mdi:lightbulb-outline"
                        @input="applyIcon"
                    />
                </div>

                <div class="field">
                    <label>Farbe</label>
                    <input
                        v-model="form.color"
                        type="color"
                        @input="applyColor"
                    />
                </div>
            </template>

            <!-- Text-spezifisch -->
            <template v-if="isText">
                <div class="field">
                    <label>Text</label>
                    <input
                        v-model="form.text"
                        type="text"
                        placeholder="Wohnzimmer Lampe"
                        @input="applyText"
                    />
                </div>

                <div class="field">
                    <label>Farbe</label>
                    <input
                        v-model="form.color"
                        type="color"
                        @input="applyColor"
                    />
                </div>

                <div class="field">
                    <label>Font-Size (px)</label>
                    <input
                        v-model.number="form.fontSize"
                        type="number"
                        min="8"
                        max="64"
                        @input="applyFontSize"
                    />
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { computed, reactive, watch } from "vue";
import { useDocumentStore } from "../stores/documentStore";
import { useSelectionStore } from "../stores/selectionStore";

const documentStore = useDocumentStore();
const selectionStore = useSelectionStore();

const selectedElement = computed(() => {
    const doc = documentStore.document;
    if (!doc) return null;

    return (
        doc.elements.find((el) => el.id === selectionStore.selectedId) ?? null
    );
});

const isIcon = computed(() => selectedElement.value?.type === "icon" || false);
const isText = computed(() => selectedElement.value?.type === "text" || false);

const form = reactive({
    text: "",
    color: "#ffffff",
    fontSize: 16,
    icon: "",
});

// wenn Auswahl wechselt -> Form updaten
watch(
    selectedElement,
    (el) => {
        if (!el) return;

        const s = el.style ?? {};
        const d = el.data ?? {};

        form.text = d.text ?? "";
        form.color = s.color ?? "#ffffff";
        form.fontSize = s.fontSize ?? 16;
        form.icon = d.icon ?? "mdi:lightbulb-outline";
    },
    { immediate: true }
);

function ensureSelected() {
    if (!selectedElement.value) return false;
    return true;
}

function applyIcon() {
    if (!ensureSelected()) return;

    documentStore.updateElementData(selectedElement.value.id, {
        icon: form.icon,
    });
}

function applyText() {
    if (!ensureSelected()) return;

    documentStore.updateElementData(selectedElement.value.id, {
        text: form.text,
    });
}

function applyColor() {
    if (!ensureSelected()) return;

    documentStore.updateElementStyle(selectedElement.value.id, {
        color: form.color,
    });
}

function applyFontSize() {
    if (!ensureSelected()) return;

    documentStore.updateElementStyle(selectedElement.value.id, {
        fontSize: form.fontSize,
    });
}
</script>

<style scoped>
.panel {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    font-size: 0.875rem;
    color: #e5e7eb;
}

.panel-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.muted {
    font-size: 0.875rem;
    color: #9ca3af;
}

.fields {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.field {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #9ca3af;
}

input[type="text"],
input[type="number"] {
    padding: 0.4rem 0.5rem;
    border-radius: 0.375rem;
    border: 1px solid #374151;
    background: #020617;
    color: #e5e7eb;
    font-size: 0.875rem;
}

input[type="color"] {
    width: 100%;
    height: 32px;
    padding: 0;
    border-radius: 4px;
    border: 1px solid #374151;
    background: #020617;
}

.value {
    font-family: monospace;
    color: #e5e7eb;
}
</style>
