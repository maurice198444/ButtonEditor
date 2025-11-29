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
            <div class="field" v-if="isIcon">
                <label>Icon</label>
                <input
                    v-model="local.icon"
                    type="text"
                    placeholder="mdi:lightbulb"
                    @change="applyIcon"
                />
            </div>

            <!-- Text-spezifisch -->
            <div v-if="isText" class="field">
                <label>Text</label>
                <input
                    v-model="local.text"
                    type="text"
                    placeholder="Anzeigename"
                    @change="applyText"
                />
            </div>

            <div v-if="isText" class="field-group">
                <div class="field">
                    <label>Schriftgröße</label>
                    <input
                        v-model.number="local.fontSize"
                        type="number"
                        min="8"
                        @change="applyTypography"
                    />
                </div>
                <div class="field">
                    <label>Stärke</label>
                    <select
                        v-model="local.fontWeight"
                        @change="applyTypography"
                    >
                        <option value="300">Light (300)</option>
                        <option value="400">Normal (400)</option>
                        <option value="600">Semi Bold (600)</option>
                        <option value="700">Bold (700)</option>
                    </select>
                </div>
            </div>

            <div v-if="isText" class="field">
                <label>Ausrichtung</label>
                <select v-model="local.textAlign" @change="applyTypography">
                    <option value="left">Links</option>
                    <option value="center">Zentriert</option>
                    <option value="right">Rechts</option>
                </select>
            </div>

            <!-- Position -->
            <div class="field-group">
                <div class="field">
                    <label>X</label>
                    <input
                        v-model.number="local.x"
                        type="number"
                        @change="applyPosition"
                    />
                </div>
                <div class="field">
                    <label>Y</label>
                    <input
                        v-model.number="local.y"
                        type="number"
                        @change="applyPosition"
                    />
                </div>
            </div>

            <!-- Größe -->
            <div class="field-group">
                <div class="field">
                    <label>Breite</label>
                    <input
                        v-model.number="local.width"
                        type="number"
                        @change="applySize"
                    />
                </div>
                <div class="field">
                    <label>Höhe</label>
                    <input
                        v-model.number="local.height"
                        type="number"
                        @change="applySize"
                    />
                </div>
            </div>

            <!-- Farbe (für Icon & Text gleich) -->
            <div class="field">
                <label>Farbe</label>
                <input v-model="local.color" type="color" @input="applyColor" />
            </div>
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
    if (!documentStore.document || !selectionStore.selectedId) return null;
    return documentStore.document.elements.find(
        (el) => el.id === selectionStore.selectedId
    );
});

const isIcon = computed(() => selectedElement.value?.type === "icon");
const isText = computed(() => selectedElement.value?.type === "text");

const local = reactive({
    icon: "",
    text: "",
    x: 0,
    y: 0,
    width: 80,
    height: 80,
    color: "#ffffff",
    fontSize: 16,
    fontWeight: 400,
    textAlign: "left",
});

watch(
    selectedElement,
    (el) => {
        if (!el) return;

        // gemeinsame Werte
        local.x = el.position?.x ?? 0;
        local.y = el.position?.y ?? 0;
        local.width = el.size?.width ?? 80;
        local.height = el.size?.height ?? 80;
        local.color = el.style?.color ?? "#ffffff";

        if (el.type === "icon") {
            local.icon = el.data?.icon ?? "mdi:lightbulb";
        }

        if (el.type === "text") {
            local.text = el.data?.text ?? "";
            local.fontSize = el.style?.fontSize ?? 16;
            local.fontWeight = el.style?.fontWeight ?? 400;
            local.textAlign = el.style?.textAlign ?? "left";
        }
    },
    { immediate: true }
);

const applyIcon = () => {
    if (!selectedElement.value || !isIcon.value) return;
    documentStore.updateElement(selectedElement.value.id, {
        data: {
            ...(selectedElement.value.data || {}),
            icon: local.icon,
        },
    });
};

const applyText = () => {
    if (!selectedElement.value || !isText.value) return;
    documentStore.updateElement(selectedElement.value.id, {
        data: {
            ...(selectedElement.value.data || {}),
            text: local.text,
        },
    });
};

const applyTypography = () => {
    if (!selectedElement.value || !isText.value) return;
    documentStore.updateElement(selectedElement.value.id, {
        style: {
            ...(selectedElement.value.style || {}),
            fontSize: local.fontSize,
            fontWeight: local.fontWeight,
            textAlign: local.textAlign,
            color: local.color,
        },
    });
};

const applyPosition = () => {
    if (!selectedElement.value) return;
    documentStore.updateElement(selectedElement.value.id, {
        position: { x: local.x, y: local.y },
    });
};

const applySize = () => {
    if (!selectedElement.value) return;
    documentStore.updateElement(selectedElement.value.id, {
        size: { width: local.width, height: local.height },
    });
};

const applyColor = () => {
    if (!selectedElement.value) return;
    documentStore.updateElement(selectedElement.value.id, {
        style: {
            ...(selectedElement.value.style || {}),
            color: local.color,
        },
    });
};
</script>

<style scoped>
.panel {
    font-size: 14px;
}
.panel-title {
    font-weight: 600;
    margin-bottom: 1rem;
}
.muted {
    color: #6b7280;
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
.field-group {
    display: flex;
    gap: 0.5rem;
}
label {
    font-size: 12px;
    color: #9ca3af;
}
input[type="text"],
input[type="number"],
select {
    background: #020617;
    border: 1px solid #374151;
    color: #e5e7eb;
    border-radius: 4px;
    padding: 4px 6px;
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
