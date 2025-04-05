<script setup lang="ts">
import AdminLayout from '@/layouts/admin/Layout.vue'
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pagination, PaginationEllipsis, PaginationList, PaginationListItem } from '@/components/ui/pagination';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: '后台-团队管理',
        href: 'admin/team',
    },
];

defineProps({
    teams: Object
})

</script>

<template>
    <Head title="团队管理" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AdminLayout title="团队管理" description="团队管理">
            <div  class="flex-col flex w-full">
                <Table class="mt-5">
                    <TableHeader>
                        <TableRow>
                            <TableHead class="text-center">
                                ID
                            </TableHead>
                            <TableHead class="text-center">团队名称</TableHead>
                            <TableHead class="text-center">创建人</TableHead>
                            <TableHead class="text-center">团队短名称</TableHead>
                            <TableHead class="text-center">目前 Vcs</TableHead>
                            <TableHead class="text-center">Package 数量</TableHead>
                            <TableHead class="text-center">成员数量</TableHead>
                            <TableHead class="text-center">创建时间</TableHead>
                            <TableHead class="text-center">操作</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="team in teams.data" :key="team.id">
                            <TableCell class="font-medium text-center">{{ team.id }}</TableCell>
                            <TableCell class="text-center">{{ team.name }}</TableCell>
                            <TableCell class="text-center">{{ team.creator }}</TableCell>
                            <TableCell class="text-center">{{ team.short_name }}</TableCell>
                            <TableCell class="text-center">{{ team.private_repo_keep }}</TableCell>
                            <TableCell class="text-center">{{ team.packages_count }}</TableCell>
                            <TableCell class="text-center">{{ team.members_count }}</TableCell>
                            <TableCell class="text-center">{{ team.created_at }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <div class="mt-5 flex justify-center">
                    <Pagination :items-per-page="teams.per_page" :total="teams.total" :sibling-count="1" show-edges :default-page="1">
                        <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                            <template v-for="(item, index) in items">
                                <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                    <Link :href="route('admin.team.index', {page: item.value})">
                                        <Button class="w-10 h-10 p-0" :variant="item.value === teams.current_page ? 'default' : 'outline'">
                                            {{ item.value }}
                                        </Button>
                                    </Link>
                                </PaginationListItem>
                                <PaginationEllipsis v-else :key="item.type" :index="index" />
                            </template>
                        </PaginationList>
                    </Pagination>
                </div>
            </div>
        </AdminLayout>
    </AppLayout>
</template>

<style scoped>

</style>
