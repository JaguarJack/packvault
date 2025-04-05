<script setup lang="ts">
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { Toaster, toast } from 'vue-sonner'
import { usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted, watch } from 'vue';
import {broadcastOnPrivate, stopListeningOnChannelEvent } from '@/lib/utils';

const page = usePage()

const toastMsg = () => {
    const message = page.props.message
    if (message?.success) {
        toast.success(message?.success)
    }
    if (message?.error) {
        toast.error(message?.error)
    }
}

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const packageBuildNotification = () => {
    if (page.props.auth.user?.id) {
        broadcastOnPrivate(`package-build-notification.${page.props.auth.user.id}`, '.package.build.notification', (e) => {
            toast.success(e.message)
        })
    }
}

onMounted(() => {
    packageBuildNotification()

    watch(() => page.props, () => {
        toastMsg()
    }, { deep: true, immediate: true })
})

onUnmounted(() => {
    if (page.props.auth.user?.id) {
        stopListeningOnChannelEvent(`package-build-notification.${page.props.auth.user.id}`, '.package.build.notification')
    }
})

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
    <Toaster position="bottom-center" richColors theme="dark" />
</template>
