<script setup lang="ts">
import LicenseLayout from '@/layouts/license/Layout.vue'
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
    licenses: Object
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'license 管理',
        href: '/licenses',
    },
];
</script>

<template>
    <Head title="License 管理" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <LicenseLayout>
            <div  class="flex-col flex w-full">
                <div>
                    <Link :href="route('license.create')">
                        <Button>新增 License</Button>
                    </Link>
                </div>
                <Table class="mt-5">
                    <TableHeader>
                        <TableRow>
                            <TableHead class="text-center">
                                ID
                            </TableHead>
                            <TableHead class="text-center">License 名称</TableHead>
                            <TableHead class="text-center">授权 packages</TableHead>
                            <TableHead class="text-center">过期时间</TableHead>
                            <TableHead class="text-center">状态</TableHead>
                            <TableHead class="text-center">创建时间</TableHead>
                            <TableHead class="text-center">操作</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="license in licenses.data" :key="license.id">
                            <TableCell class="font-medium text-center">
                                {{ license.id }}
                            </TableCell>
                            <TableCell class="text-center">{{ license.title }}</TableCell>
                            <TableCell class="text-center flex flex-col gap-x-2 gap-y-2">
                                <div v-for="p in license.packages" :key="p.id">
                                    {{ p.name }}
                                </div>
                            </TableCell>
                            <TableCell class="text-center">{{ license.expired_at }}</TableCell>
                            <TableCell class="text-center">
                                <Link :href="route('license.activate', license.id)" method="put">
                                    <Button variant="secondary">
                                        {{ !license.status ? '禁用' : '启用'}}
                                    </Button>
                                </Link>
                            </TableCell>
                            <TableCell class="text-center">
                                {{ license.created_at }}
                            </TableCell>
                            <TableCell class="flex justify-center gap-x-2">
                                <Link :href="route('license.edit', license.id)">
                                    <Button variant="secondary">
                                        更新
                                    </Button>
                                </Link>
                                <Link :href="route('license.delete', license.id)" method="delete">
                                    <Button variant="destructive">
                                        删除
                                    </Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <div class="mt-5 flex justify-center">
                    <Pagination :items-per-page="licenses.per_page" :total="licenses.total" :sibling-count="1" show-edges :default-page="1">
                        <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                            <template v-for="(item, index) in items">
                                <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                    <Link :href="route('license.index', {page: item.value})">
                                        <Button class="w-10 h-10 p-0" :variant="item.value === licenses.current_page ? 'default' : 'outline'">
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
        </LicenseLayout>
    </AppLayout>

</template>
