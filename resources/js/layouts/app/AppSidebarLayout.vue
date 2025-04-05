<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType, NavItem } from '@/types';
import {  ShieldHalf, Copyright, UsersRound, Wrench  } from 'lucide-vue-next';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const navItems: NavItem[] = [
    {
        title: '基础设置',
        href: '/connect/vcs',
        icon: ShieldHalf ,
    },
    {
        title: '用户管理',
        href: '/users',
        icon: UsersRound ,
    },
    {
        title: 'License管理',
        href: '/license',
        icon: Copyright ,
    },
    {
        title: '构建任务',
        href: '/job',
        icon: Wrench,
    }
]
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar :main-nav-items="navItems"/>
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
