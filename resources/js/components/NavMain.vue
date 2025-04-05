<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();
const page = usePage<SharedData>();

const activeUrl = (href: string, url: string) => {
    return href.split('/').filter((v) => v.length > 0)[0] === url.split('/').filter((v) => v.length > 0)[0]
}
</script>

<template>

    <SidebarGroup class="px-2 py-0" v-for="(item, key) in items" :key="key">
        <template v-if="item.children?.length > 0">
            <SidebarGroupLabel v-if="item.children?.length > 0">{{ item.title }}</SidebarGroupLabel>
            <SidebarMenu>
                <SidebarMenuItem :key="i.title" v-for="i in item.children">
                    <SidebarMenuButton as-child :is-active="page.url.indexOf(i.href) !== -1">

                        <Link :href="i.href">
                            <component :is="i.icon" />
                            <span>{{ i.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </template>
        <template v-else>
            <SidebarMenu>
                <SidebarMenuItem :key="item.title">
                    <SidebarMenuButton as-child :is-active="page.url.indexOf(item.href) !== -1 || activeUrl(item.href, page.url)">
                        <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </template>
    </SidebarGroup>
</template>
