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
    const raw = props.element.data?.icon ?? "mdi:lightbulb-outline";

    // erlaubt sowohl "mdi:lightbulb" als auch "mdi-lightbulb"
    if (raw.startsWith("mdi:")) {
        return raw.replace("mdi:", "mdi-");
    }

    if (raw.startsWith("mdi-")) {
        return raw;
    }

    return `mdi-${raw}`;
});

const iconStyle = computed(() => {
    const s = props.element.style ?? {};
    const fontSize = s.fontSize ?? 24;

    return {
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        width: "100%",
        height: "100%",
        color: s.color ?? "#ffffff",
        fontSize: fontSize + "px",
        lineHeight: 1,
    };
});
</script>

<style scoped>
.card-icon {
    line-height: 1;
}
</style>
