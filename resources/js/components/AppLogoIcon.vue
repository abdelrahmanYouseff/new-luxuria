<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img
        src="/images_car/new-logo3.png"
        alt="Luxuria UAE Logo"
        :class="className"
        v-bind="$attrs"
        style="object-fit: contain;"
    />
</template>
