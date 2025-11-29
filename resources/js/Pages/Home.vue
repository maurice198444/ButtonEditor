<script setup>
defineProps({
    user: String,
});
</script>

<template>
    <Head :title="$page.component" />
    <div>
        <h1>Hallo {{ user }}</h1>
    </div>
</template>
