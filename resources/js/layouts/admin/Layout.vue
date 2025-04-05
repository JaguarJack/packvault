<script setup lang="ts">
import Heading from '@/components/Heading.vue';

defineProps({
    title: String,
    description: String
})

</script>

<template>
    <div class="px-4 py-6">
        <Heading :title="title" :description="description" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
            <slot/>
        </div>
    </div>
</template>
