<template>
    <div class="card-text" :style="textStyle" :data-el="element.id">
        {{ displayText }}
    </div>
</template>

<script setup>
import { computed } from "vue";
// useDocumentStore ist aktuell nicht zwingend nötig, könnte aber später
// für Bindings genutzt werden.
// import { useDocumentStore } from "../../stores/documentStore";

const props = defineProps({
    element: {
        type: Object,
        required: true,
    },
});

const displayText = computed(() => {
    const data = props.element.data ?? {};
    if (data.text && data.text.trim() !== "") {
        return data.text;
    }

    // Platzhalter, falls kein Text gesetzt ist
    return "Text";
});

const textStyle = computed(() => {
    const s = props.element.style ?? {};

    return {
        color: s.color ?? "#ffffff",
        fontSize: (s.fontSize ?? 16) + "px",
        fontWeight: s.fontWeight ?? 400,
        textAlign: s.textAlign ?? "left",
        lineHeight: 1.2,
        whiteSpace: "nowrap",
        pointerEvents: "none", // Selection/Drag über Wrapper
    };
});
</script>

<style scoped>
.card-text {
    pointer-events: none;
}
</style>
