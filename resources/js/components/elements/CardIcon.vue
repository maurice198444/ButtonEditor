<template>
    <i
        class="card-icon mdi"
        :class="mdiClass"
        :style="iconStyle"
        :data-el="element.id"
    ></i>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    element: {
        type: Object,
        required: true,
    },
});

const mdiClass = computed(() => {
    // "mdi:lightbulb" → "mdi-lightbulb"
    const icon = props.element.data?.icon ?? "mdi:lightbulb";
    return icon.replace(":", "-");
});

const iconStyle = computed(() => {
    const width = props.element.size?.width ?? 80;
    const fontSize = width * 0.6;

    return {
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        width: "100%",
        height: "100%",
        color: props.element.style?.color ?? "#fff",
        fontSize: fontSize + "px",
    };
});
</script>

<style scoped>
.card-icon {
    line-height: 1;
}
</style>
