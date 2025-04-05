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
        title: '后台-用户管理',
        href: 'admin/team',
    },
];

defineProps({
    users: Object
})
</script>

<template>
    <Head title="用户管理" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AdminLayout title="用户管理" description="用户管理">
            <div  class="flex-col flex w-full">
                <Table class="mt-5">
                    <TableHeader>
                        <TableRow>
                            <TableHead class="text-center">
                                ID
                            </TableHead>
                            <TableHead class="text-center">用户名</TableHead>
                            <TableHead class="text-center">邮箱</TableHead>
                            <TableHead class="text-center">邮箱验证时间</TableHead>
                            <TableHead class="text-center">体验期</TableHead>
                            <TableHead class="text-center">创建时间</TableHead>
                            <TableHead class="text-center">操作</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell class="font-medium text-center">{{ user.id }}</TableCell>
                            <TableCell class="text-center">{{ user.name }}</TableCell>
                            <TableCell class="text-center">{{ user.email }}</TableCell>
                            <TableCell class="text-center">{{ user.email_verified_at }}</TableCell>
                            <TableCell class="text-center">{{ user.experience_at }}</TableCell>
                            <TableCell class="text-center">{{ user.created_at }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <div class="mt-5 flex justify-center">
                    <Pagination :items-per-page="users.per_page" :total="users.total" :sibling-count="1" show-edges :default-page="1">
                        <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                            <template v-for="(item, index) in items">
                                <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                    <Link :href="route('admin.user.index', {page: item.value})">
                                        <Button class="w-10 h-10 p-0" :variant="item.value === users.current_page ? 'default' : 'outline'">
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
