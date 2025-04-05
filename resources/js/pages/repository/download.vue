<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import TeamLayout from '@/layouts/team/Layout.vue';
import { Head, Link, } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Gitlab, Gitee, Github, Gitea, Coding} from '@/components/icons';
import { Loader2 } from 'lucide-vue-next'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pagination, PaginationEllipsis, PaginationList, PaginationListItem } from '@/components/ui/pagination';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: '团队设置',
        href: '/team/setting',
    },
];

interface Props {
    downloads?: object
}

defineProps<Props>();
</script>
<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Package 仓库设置" />
        <TeamLayout>
            <div class="flex-1 flex-col gap-y-2 md:max-w-5xl">
                <div class="flex-1 md:max-w-5xl mt-2">
                    <Table class="mt-5">
                        <TableHeader>
                            <TableRow>
                                <TableHead class="text-center">
                                    ID
                                </TableHead>
                                <TableHead class="text-center">下载用户邮箱</TableHead>
                                <TableHead class="text-center">包名</TableHead>
                                <TableHead class="text-center">版本</TableHead>
                                <TableHead class="text-center">ip</TableHead>
                                <TableHead class="text-center">文件地址</TableHead>
                                <TableHead class="text-center">创建时间</TableHead>
                                <TableHead class="text-center">操作</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody v-if="downloads.total" class="flex justify-center">
                            <TableRow v-for="download in downloads.data" :key="download.id">
                                <TableCell class="font-medium text-center">{{ download.username }}</TableCell>
                                <TableCell class="text-center">{{ download.package_name }}</TableCell>
                                <TableCell class="text-center">{{ download.version }}</TableCell>
                                <TableCell class="text-center">{{ download.ip }}</TableCell>
                                <TableCell class="text-center">{{ download.source }}</TableCell>
                                <TableCell class="text-center">{{ download.created_at }}</TableCell>
                            </TableRow>
                        </TableBody>

                    </Table>
                    <div class="flex w-full p-5 justify-center items-center" v-if="! downloads.total">
                            暂无用户下载
                    </div>
                    <div class="mt-5 flex justify-center" v-if="downloads.total && downloads.total <= downloads.per_page">
                        <Pagination :items-per-page="downloads.per_page" :total="downloads.total" :sibling-count="1" show-edges :default-page="1">
                            <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                                <template v-for="(item, index) in items">
                                    <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                        <a :href="`?page=` + item.value">
                                            <Button class="w-10 h-10 p-0" :variant="item.value === downloads.current_page ? 'default' : 'outline'">
                                                {{ item.value }}
                                            </Button>
                                        </a>
                                    </PaginationListItem>
                                    <PaginationEllipsis v-else :key="item.type" :index="index" />
                                </template>
                            </PaginationList>
                        </Pagination>
                    </div>
                </div>
            </div>
        </TeamLayout>
    </AppLayout>
</template>
