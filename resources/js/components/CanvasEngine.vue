<!-- resources/js/components/CanvasEngine.vue -->
<template>
    <div class="canvas" :style="canvasStyle" @mousedown="onCanvasClick">
        <!-- Elemente -->
        <div
            v-for="el in doc.elements"
            :key="el.id"
            class="element-wrapper"
            :style="elementWrapperStyle(el)"
            :data-id="el.id"
            :ref="setElementRef"
            @mousedown.stop="select(el.id)"
        >
            <CardIcon v-if="el.type === 'icon'" :element="el" />
            <CardText v-else-if="el.type === 'text'" :element="el" />
            <!-- weitere Typen später hier -->
        </div>

        <!-- EIN Moveable, das auf das aktuell selektierte Element zeigt -->
        <Moveable
            v-if="selectedId && elementRefs[selectedId]"
            :target="elementRefs[selectedId]"
            :draggable="true"
            :resizable="true"
            :rotatable="true"
            :snappable="false"
            @drag="onDrag"
            @resize="onResize"
            @rotateEnd="onRotateEnd"
        />
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import Moveable from "vue3-moveable";
import { useDocumentStore } from "../stores/documentStore";
import { useSelectionStore } from "../stores/selectionStore";
import CardIcon from "./elements/CardIcon.vue";
import CardText from "./elements/CardText.vue";

const store = useDocumentStore();
const selectionStore = useSelectionStore();

/**
 * Dokument aus dem Store
 */
const doc = computed(
    () =>
        store.document ?? {
            canvas: { width: 600, height: 350, bg: { color: "#111827" } },
            elements: [],
        }
);

/**
 * aktuell selektierte ID aus dem Selection-Store
 */
const selectedId = computed(() => selectionStore.selectedId);

/**
 * Canvas-Styling
 */
const canvasStyle = computed(() => ({
    position: "relative",
    width: (doc.value.canvas?.width ?? 600) + "px",
    height: (doc.value.canvas?.height ?? 350) + "px",
    backgroundColor: doc.value.canvas?.bg?.color ?? "#111827",
    border: "1px solid #4b5563",
    margin: "2rem auto",
}));

/**
 * Wrapper-Styling je Element
 */
const elementWrapperStyle = (el) => ({
    position: "absolute",
    left: (el.position?.x ?? 0) + "px",
    top: (el.position?.y ?? 0) + "px",
    width: (el.size?.width ?? 80) + "px",
    height: (el.size?.height ?? 80) + "px",
    border: "1px dashed rgba(148,163,184,0.6)",
    boxSizing: "border-box",
});

/**
 * DOM-Refs für Moveable
 */
const elementRefs = ref({});

const setElementRef = (el) => {
    if (!el) return;
    const id = el.dataset.id;
    if (!id) return;
    elementRefs.value[id] = el;
};

/**
 * Auswahl steuern
 */
const select = (id) => {
    selectionStore.select(id);
};

const onCanvasClick = (e) => {
    if (e.target.classList.contains("canvas")) {
        selectionStore.clear();
    }
};

/**
 * Moveable-Events → zurück in den Store schreiben
 */
const onDrag = (e) => {
    if (!selectedId.value) return;

    const { left, top, target } = e;

    store.updateElement(selectedId.value, {
        position: { x: left, y: top },
    });

    target.style.left = `${left}px`;
    target.style.top = `${top}px`;
    target.style.transform = "translate(0, 0)";
};

const onResize = (e) => {
    if (!selectedId.value) return;

    const { width, height, drag, target } = e;
    const left = drag?.left ?? 0;
    const top = drag?.top ?? 0;

    store.updateElement(selectedId.value, {
        position: { x: left, y: top },
        size: { width, height },
    });

    target.style.left = `${left}px`;
    target.style.top = `${top}px`;
    target.style.width = `${width}px`;
    target.style.height = `${height}px`;
    target.style.transform = "translate(0, 0)";
};

const onRotateEnd = (e) => {
    if (!selectedId.value) return;
    const rotate = e.lastEvent.rotate;
    const el = doc.value.elements.find((x) => x.id === selectedId.value);
    const prevScale = el?.transform?.scale ?? 1;

    store.updateElement(selectedId.value, {
        transform: { rotation: rotate, scale: prevScale },
    });
};
</script>

<style scoped>
.canvas {
    box-sizing: border-box;
}

.element-wrapper {
    position: absolute;
    box-sizing: border-box;
}
</style>
