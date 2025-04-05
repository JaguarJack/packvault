<script setup lang="ts">
import Heading from '@/components/Heading.vue';


</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Package 构建任务" description="管理 Package 构建任务" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
            <slot/>
        </div>
    </div>
</template>
