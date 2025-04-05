<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/',
    },
];

defineProps<{
    name?: string;
    team: object;
    new_members: Array<any>;
    new_packages: Array<any>;
    new_downloads: Array<any>;
}>();

const page = usePage()
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-8">
                    <h2 class="text-2xl">基本信息</h2>
                    <div class="flex flex-col gap-y-2 mt-2 p-2">
                        <div class="flex justify-between"><div>团队名称</div> <div class="text-2xl">{{ team.name }}</div></div>
                        <div class="flex justify-between">
                            <div>创建者</div>
                            <div class="py-1 px-3 bg-gray-100 text-gray-500 rounded">{{ page.props.auth.user.name }}</div>
                        </div>
                        <div class="flex justify-between">
                            <div>成员数</div>
                            <div class="py-1 px-3 bg-gray-100 text-gray-500 rounded">{{ team.members_count}} 个</div>
                        </div>
                        <div class="flex justify-between">
                            <div>License 数</div>
                            <div class="py-1 px-3 bg-gray-100 text-gray-500 rounded">{{ team.licenses_count}} 个</div>
                        </div>
                        <div class="flex justify-between">
                            <div>Package</div>
                            <div class="py-1 px-3 bg-gray-100 text-gray-500 rounded">{{ team.packages_count}} 个</div>
                        </div>
                        <div class="flex justify-between">
                            <div>短名称</div>
                            <div class="py-1 px-3 bg-gray-100 text-gray-500 rounded">{{ team.short_name}}</div>
                        </div>
                        <div class="flex justify-between">
                            <div>Vcs 平台</div>
                            <div class="py-1 px-3 bg-gray-100 text-gray-500 rounded">{{ team.private_repo_keep}}</div>
                        </div>
                        <div class="flex justify-between">
                            <div>创建时间</div>
                            <div class="py-1 px-3 bg-gray-100 text-gray-500 rounded">{{ team.created_at}}</div>
                        </div>
                    </div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
