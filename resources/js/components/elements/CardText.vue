<template>
    <div class="card-text" :style="textStyle" :data-el="element.id">
        {{ displayText }}
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useDocumentStore } from "../../stores/documentStore";

const props = defineProps({
    element: {
        type: Object,
        required: true,
    },
});

// später kannst du hier Bindings (bindings.name etc.) auflösen
const documentStore = useDocumentStore();

const displayText = computed(() => {
    const data = props.element.data || {};
    // falls binding:name → aus documentStore.document.bindings.name lesen
    if (data.binding === "name" && documentStore.document?.bindings?.name) {
        return documentStore.document.bindings.name;
    }
    return data.text ?? "";
});

const textStyle = computed(() => {
    const s = props.element.style || {};
    return {
        width: "100%",
        height: "100%",
        display: "flex",
        alignItems: "center",
        justifyContent:
            s.textAlign === "right"
                ? "flex-end"
                : s.textAlign === "center"
                ? "center"
                : "flex-start",
        color: s.color ?? "#ffffff",
        fontSize: (s.fontSize ?? 16) + "px",
        fontWeight: s.fontWeight ?? 400,
        lineHeight: 1.2,
        whiteSpace: "nowrap",
    };
});
</script>

<style scoped>
.card-text {
    pointer-events: none; /* Selection/Drag passiert über Wrapper */
}
</style>
