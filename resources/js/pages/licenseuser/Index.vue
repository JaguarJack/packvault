<script setup lang="ts">
import LicenseUserLayout from '@/layouts/licenseuser/Layout.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import {
    Pagination,
    PaginationEllipsis,
    PaginationList,
    PaginationListItem,
} from '@/components/ui/pagination'

defineProps({
    users: Object
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: '用户管理',
        href: '/license/user',
    },
];
</script>

<template>
    <Head title="团队设置" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <LicenseUserLayout>
            <div  class="flex-col flex w-full">
                <div>
                    <Link :href="route('license.user.create')">
                        <Button>新增用户</Button>
                    </Link>
                </div>
                <Table class="mt-5">
                    <TableHeader>
                        <TableRow>
                            <TableHead class="text-center">
                                ID
                            </TableHead>
                            <TableHead class="text-center">邮箱</TableHead>
                            <TableHead class="text-center">License</TableHead>
                            <TableHead class="text-center">允许IP地址数</TableHead>
                            <TableHead class="text-center">状态</TableHead>
                            <TableHead class="text-center">创建时间</TableHead>
                            <TableHead class="text-center">操作</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell class="font-medium text-center">
                                {{ user.id }}
                            </TableCell>
                            <TableCell class="text-center">{{ user.email }}</TableCell>
                            <TableCell class="text-center">{{ user.license }}</TableCell>
                            <TableCell class="text-center">{{ user.allow_ip_number }}</TableCell>
                            <TableCell class="text-center">
                                <Link :href="route('license.user.activate', user.id)" method="put">
                                    <Button variant="secondary">
                                        {{ !user.status ? '禁用' : '启用'}}
                                    </Button>
                                </Link>
                            </TableCell>
                            <TableCell class="text-center">
                                {{ user.created_at }}
                            </TableCell>
                            <TableCell class="flex justify-center gap-x-2">
                                <Link :href="route('license.user.edit', user.id)">
                                    <Button variant="secondary">
                                        更新
                                    </Button>
                                </Link>
                                <Link :href="route('license.user.delete', user.id)" method="delete">
                                    <Button variant="destructive">
                                        删除
                                    </Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <div class="mt-5 flex justify-center">
                    <Pagination :items-per-page="users.per_page" :total="users.total" :sibling-count="1" show-edges :default-page="1">
                        <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                            <template v-for="(item, index) in items">
                                <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                    <Link :href="route('license.user.index', {page: item.value})">
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
        </LicenseUserLayout>
    </AppLayout>
</template>
