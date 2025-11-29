<template>
    <div class="canvas" :style="canvasStyle" @mousedown="onCanvasClick">
        <div
            v-for="el in doc.elements"
            :key="el.id"
            class="element-wrapper"
            :style="elementWrapperStyle(el)"
            :data-id="el.id"
            @mousedown.stop="onElementMouseDown(el, $event)"
        >
            <CardIcon v-if="el.type === 'icon'" :element="el" />
            <CardText v-else-if="el.type === 'text'" :element="el" />

            <!-- Resize-Handle unten rechts -->
            <div
                class="resize-handle"
                @mousedown.stop="onResizeMouseDown(el, $event)"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { computed, reactive } from "vue";
import { useDocumentStore } from "../stores/documentStore";
import { useSelectionStore } from "../stores/selectionStore";
import CardIcon from "./elements/CardIcon.vue";
import CardText from "./elements/CardText.vue";

const documentStore = useDocumentStore();
const selectionStore = useSelectionStore();

const doc = computed(
    () => documentStore.document ?? { meta: {}, elements: [] }
);

const canvasStyle = computed(() => {
    const meta = doc.value.meta ?? {};
    return {
        position: "relative",
        width: (meta.width ?? 320) + "px",
        height: (meta.height ?? 180) + "px",
        background: meta.background ?? "#111827",
        border: "1px solid #374151",
        borderRadius: "0.75rem",
        boxSizing: "border-box",
        overflow: "hidden",
    };
});

/**
 * Wrapper-Style:
 * - Position absolut
 * - Text & Icon: Größe primär über fontSize -> Rahmen am Inhalt
 * - andere Typen: Größe über size
 */
function elementWrapperStyle(el) {
    const pos = el.position ?? {};
    const size = el.size ?? {};
    const style = el.style ?? {};
    const selected = selectionStore.selectedId === el.id;

    const base = {
        position: "absolute",
        left: (pos.x ?? 0) + "px",
        top: (pos.y ?? 0) + "px",
        display: "inline-flex",
        alignItems: "center",
        justifyContent: "center",
        boxSizing: "border-box",
        border: selected ? "1px solid #3b82f6" : "1px solid transparent",
        borderRadius: "0.25rem",
        pointerEvents: "auto",
        userSelect: "none",
        background: "transparent",
        cursor: "move",
    };

    if (el.type === "text") {
        const fontSize = style.fontSize ?? 16;
        const paddingX = 12;
        const paddingY = 8;

        const height = fontSize * 1.8;
        const width = size.width ?? fontSize * 9;

        return {
            ...base,
            minHeight: height + "px",
            minWidth: width + "px",
            padding: `${paddingY}px ${paddingX}px`,
        };
    }

    if (el.type === "icon") {
        const fontSize = style.fontSize ?? 24;
        const padding = 8;
        const box = fontSize * 2; // Rahmen ungefähr 2x Font-Size

        return {
            ...base,
            minWidth: box + padding * 2 + "px",
            minHeight: box + padding * 2 + "px",
            padding: padding + "px",
        };
    }

    // Fallback für andere Typen
    return {
        ...base,
        width: (size.width ?? 40) + "px",
        height: (size.height ?? 40) + "px",
    };
}

/* Drag-State */
const dragState = reactive({
    id: null,
    startMouseX: 0,
    startMouseY: 0,
    startX: 0,
    startY: 0,
    dragging: false,
});

/* Resize-State */
const resizeState = reactive({
    id: null,
    startMouseX: 0,
    startMouseY: 0,
    startWidth: 0,
    startHeight: 0,
    startFontSize: 0,
    isText: false,
    isIcon: false,
    resizing: false,
});

function onCanvasClick() {
    selectionStore.clear();
}

/* DRAG: Element bewegen */
function onElementMouseDown(el, event) {
    event.preventDefault();

    selectionStore.select(el.id);

    dragState.id = el.id;
    dragState.dragging = true;
    dragState.startMouseX = event.clientX;
    dragState.startMouseY = event.clientY;
    dragState.startX = el.position?.x ?? 0;
    dragState.startY = el.position?.y ?? 0;

    window.addEventListener("mousemove", onMouseMove);
    window.addEventListener("mouseup", onMouseUp);
}

/* RESIZE: Handle unten rechts */
function onResizeMouseDown(el, event) {
    event.preventDefault();

    selectionStore.select(el.id);

    const size = el.size ?? {};
    const style = el.style ?? {};

    resizeState.id = el.id;
    resizeState.resizing = true;
    resizeState.startMouseX = event.clientX;
    resizeState.startMouseY = event.clientY;
    resizeState.startWidth = size.width ?? 40;
    resizeState.startHeight = size.height ?? 40;
    resizeState.startFontSize =
        style.fontSize ?? (el.type === "icon" ? 24 : 16);
    resizeState.isText = el.type === "text";
    resizeState.isIcon = el.type === "icon";

    window.addEventListener("mousemove", onMouseMove);
    window.addEventListener("mouseup", onMouseUp);
}

function onMouseMove(event) {
    /* Drag */
    if (dragState.dragging && dragState.id && !resizeState.resizing) {
        const dx = event.clientX - dragState.startMouseX;
        const dy = event.clientY - dragState.startMouseY;

        const newX = dragState.startX + dx;
        const newY = dragState.startY + dy;

        documentStore.updateElement(dragState.id, {
            position: {
                x: newX,
                y: newY,
            },
        });
    }

    /* Resize */
    if (resizeState.resizing && resizeState.id) {
        const dx = event.clientX - resizeState.startMouseX;
        const dy = event.clientY - resizeState.startMouseY;

        // gemeinsame Parameter
        const delta = Math.max(dx, dy);
        const factor = 0.3; // wie "schnell" die Größe steigt/fällt

        if (resizeState.isText) {
            let newFontSize = resizeState.startFontSize + delta * factor;
            newFontSize = clamp(newFontSize, 8, 72);

            const height = newFontSize * 1.8;
            const width = newFontSize * 9;

            documentStore.updateElement(resizeState.id, {
                style: {
                    fontSize: newFontSize,
                },
                size: {
                    width,
                    height,
                },
            });
        } else if (resizeState.isIcon) {
            let newFontSize = resizeState.startFontSize + delta * factor;
            newFontSize = clamp(newFontSize, 8, 96);

            const box = newFontSize * 2;

            documentStore.updateElement(resizeState.id, {
                style: {
                    fontSize: newFontSize,
                },
                size: {
                    width: box,
                    height: box,
                },
            });
        } else {
            // Fallback für andere Typen: klassische width/height
            let newWidth = resizeState.startWidth + dx;
            let newHeight = resizeState.startHeight + dy;

            const minSize = 16;
            newWidth = Math.max(minSize, newWidth);
            newHeight = Math.max(minSize, newHeight);

            documentStore.updateElement(resizeState.id, {
                size: {
                    width: newWidth,
                    height: newHeight,
                },
            });
        }
    }
}

function onMouseUp() {
    if (dragState.dragging || resizeState.resizing) {
        dragState.dragging = false;
        dragState.id = null;

        resizeState.resizing = false;
        resizeState.id = null;
        resizeState.isText = false;
        resizeState.isIcon = false;

        window.removeEventListener("mousemove", onMouseMove);
        window.removeEventListener("mouseup", onMouseUp);
    }
}

function clamp(value, min, max) {
    return Math.max(min, Math.min(max, value));
}
</script>

<style scoped>
.canvas {
    box-sizing: border-box;
}

.element-wrapper {
    position: absolute;
    box-sizing: border-box;
}

/* kleiner Resize-Handle unten rechts */
.resize-handle {
    position: absolute;
    right: -3px;
    bottom: -3px;
    width: 10px;
    height: 10px;
    border-radius: 2px;
    background: #3b82f6;
    border: 1px solid #0f172a;
    cursor: se-resize;
}
</style>
