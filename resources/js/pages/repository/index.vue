<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import TeamLayout from '@/layouts/team/Layout.vue';
import { Head, Link, } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Gitlab, Gitee, Github, Gitea, Coding} from '@/components/icons';
import { Loader2 } from 'lucide-vue-next'

interface Props {
    repositories?: Array<any>;
}

defineProps<Props>();
</script>
<template>
    <AppLayout>
        <Head title="Package 仓库设置" />
        <TeamLayout>
            <div class="flex-1 flex-col gap-y-2 md:max-w-5xl">
                <div v-if="repositories?.length > 0" class="w-full flex justify-end">
                    <Link :href="route('repository.create')">
                        <Button><Plus />添加 Package 仓库</Button>
                    </Link>
                </div>
                <div class="flex-1 md:max-w-5xl mt-2">
                    <section class="max-w-5xl space-y-12">
                        <div v-if="repositories?.length > 0" class="grid max-w-5xl grid-cols-4 gap-x-2 gap-y-2">
                            <Card class="w-full" v-for="repository in repositories" :key="repository.id">
                                <CardHeader>
                                    <Link :href="route('repository.show', repository.id)">
                                    <CardTitle class="min-h-16 text-lg flex items-center">
                                        <Loader2 class="w-4 h-4 mr-2 animate-spin" v-if="repository.build_status === 1 || repository.build_status === 0"/>
                                        {{ repository.name }}</CardTitle>
                                    </Link>
                                    <CardDescription class="min-h-16">{{ repository.description }}</CardDescription>
                                </CardHeader>
                                <CardFooter class="w-18 flex justify-between px-6 pb-6 gap-x-2">
                                    <Link :href="route('repository.download', repository.id)" class="text-sm">
                                        下载详情
                                    </Link>
                                    <!--<a :href="repository.url+ '/settings/hooks'" target="_blank">
                                        <Button variant="secondary">设置 webhook</Button>
                                    </a>-->
                                    <div>
                                        <div v-if="repository.stay_at === 'github'"><Github class-name="h-8 w-8"/></div>
                                        <div v-if="repository.stay_at === 'gitea'"><Gitea class-name="h-8 w-8"/></div>
                                        <div v-if="repository.stay_at === 'gitee'"><Gitee class-name="h-8 w-8"/></div>
                                        <div v-if="repository.stay_at === 'gitlab'"><Gitlab class-name="h-8 w-8"/></div>
                                        <div v-if="repository.stay_at === 'coding'"><Coding class-name="h-10 w-28"/></div>
                                    </div>
                                </CardFooter>
                            </Card>
                        </div>
                        <div v-else>
                            <Card class="w-[300px]">
                                <CardContent class="flex justify-center">
                                    <div class="flex h-[200px] w-[100px] cursor-pointer items-center justify-center">
                                        <Link :href="route('repository.create')">
                                            <Button><Plus />添加第一个 Package</Button>
                                        </Link>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </section>
                </div>
            </div>
        </TeamLayout>
    </AppLayout>
</template>
