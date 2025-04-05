<script setup lang="ts">
import Heading from '@/components/Heading.vue';


</script>

<template>
    <div class="px-4 py-6">
        <Heading title="用户管理" description="管理 License 用户" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
            <slot/>
        </div>
    </div>
</template>
