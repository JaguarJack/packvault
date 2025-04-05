<script setup lang="ts">
import Layout from '@/layouts/job/Layout.vue'
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
import { Badge } from '@/components/ui/badge';
import { Loader2 } from 'lucide-vue-next'

const props = defineProps({
    jobs: Object
})

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Package 构建',
        href: '/jobs',
    },
];

const changeStatus = (status:number) => {
    return ['未开始', '进行中', '成功', '失败'][status]
}
</script>

<template>
    <Head title="Package 构建" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <Layout>
            <div  class="flex-col flex w-full">
                <Table class="mt-5">
                    <TableHeader>
                        <TableRow>
                            <TableHead class="text-center">
                                任务Id
                            </TableHead>
                            <TableHead class="text-center">Package 名称</TableHead>
                            <TableHead class="text-center">状态</TableHead>
                            <TableHead class="text-center">输出</TableHead>
                            <TableHead class="text-center">创建时间</TableHead>
                            <TableHead class="text-center">更新时间</TableHead>
                            <TableHead class="text-center">操作</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="job in jobs.data" :key="job.id">
                            <TableCell class="font-medium text-center">
                                {{ job.id }}
                            </TableCell>
                            <TableCell class="text-center"><Badge v-if="!job.package" variant="destructive" size="small">已删</Badge> {{ job.package_name }} </TableCell>
                            <TableCell class="text-center flex justify-center">
                                <div class="flex items-center gap-x-1">
                                    <Loader2 class="w-4 h-4 animate-spin" v-if="job.status === 1"/>
                                    <span>{{ changeStatus(job.status)}}</span>
                                </div>
                            </TableCell>
                            <TableCell class="w-64">
                                <div class="w-full flex justify-center">
                                    {{ job?.output?.substring(-1, 150) }} {{ job?.output?.length > 150 ? '...' : ''}}
                                </div>
                            </TableCell>
                            <TableCell class="text-center">
                                {{ job.created_at }}
                            </TableCell>
                            <TableCell class="text-center">
                                {{ job.updated_at }}
                            </TableCell>
                            <TableCell class="flex justify-center gap-x-2">
                                <Link :href="route('package.job.delete', job.id)" method="delete">
                                    <Button variant="destructive">
                                        删除
                                    </Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <div class="mt-5 flex justify-center">
                    <Pagination :items-per-page="jobs.per_page" :total="jobs.total" :sibling-count="1" show-edges :default-page="1">
                        <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                            <template v-for="(item, index) in items">
                                <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                    <Link :href="route('package.job', {page: item.value})">
                                        <Button class="w-10 h-10 p-0" :variant="item.value === jobs.current_page ? 'default' : 'outline'">
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
        </Layout>
    </AppLayout>

</template>
