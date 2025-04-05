<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import TeamLayout from '@/layouts/team/Layout.vue';
import { Head, Link } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button'
import SetPersonToken from './SetPersonToken.vue';
import { Coding, Gitee, Github} from '@/components/icons';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: '仓库授权',
        href: '/team/connect/app',
    },
];

interface Props {
    is_github_connected: boolean;
    is_gitee_connected: boolean;
    is_gitlab_connected: boolean;
    is_gitea_connected: boolean;
    is_coding_connected: boolean;

    is_support_github: boolean;
    is_support_gitee: boolean;
    is_support_coding: boolean;
    is_support_gitlab: boolean;
    is_support_gitea: boolean;

}

defineProps<Props>();

// const page = usePage<SharedData>();
// const user = page.props.auth.user as User;

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="仓库授权" />

        <TeamLayout>
            <div class="flex-1 md:max-w-2xl">

                <section class="max-w-xl space-y-12">
                    <div class="flex flex-col space-y-6">
                        <HeadingSmall title="仓库授权" description="授权 github/gitee 以便更好同步 Package" />
                        <div class="flex flex-col gap-y-6">
                            <div class="flex items-center justify-between border border-solid border-gray-200 p-4 rounded-xl shadow-md" v-if="is_support_github">
                                <div class="flex items-center ">
                                    <Github/>
                                    <span class="ml-2">Github</span>
                                </div>
                                <Link :href="route('disconnect.vcs', 'github')" v-if="is_github_connected">
                                    <Button variant="secondary" >取消授权</Button>
                                </Link>
                                <a :href="route('github.redirect')" v-else>
                                    <Button variant="secondary">授权</Button>
                                </a>
                            </div>

                            <div class="flex items-center justify-between border border-solid border-gray-200 p-4 rounded-xl shadow-md" v-if="is_support_gitee">
                                <div class="flex items-center">
                                    <Gitee/>
                                    <span class="ml-2">Gitee</span>
                                </div>
                                <Link :href="route('disconnect.vcs', 'gitee')" v-if="is_gitee_connected">
                                    <Button variant="secondary" >取消授权</Button>
                                </Link>
                                <a :href="route('gitee.redirect')" v-else>
                                    <Button variant="secondary">授权</Button>
                                </a>
                            </div>

                            <div class="flex items-center justify-between border border-solid border-gray-200 p-4 rounded-xl shadow-md" v-if="is_support_coding">
                                <div class="flex items-center">
                                    <Coding class-name="w-48"/>
                                </div>
                                <SetPersonToken :is_auth="is_coding_connected" vcs="coding">
                                    <p>请到 <a href="https://coding.net/" target="_blank">Coding</a> 项目中选择对应的项目</p>
                                    <p>选择<span class="underline px-1"> 项目设置里面的 </span> 开发者选项</p>
                                    <p>创建项目令牌，指定仓库的读取权即可·</p>
                                </SetPersonToken>
                            </div>
                            <div class="flex items-center justify-between border border-solid border-gray-200 p-4 rounded-xl shadow-md" v-if="is_support_gitlab">
                                <div class="flex items-center">
                                    <Gitlab/>
                                    <span class="ml-2">Gitlab</span>
                                </div>

                                <SetPersonToken :is_auth="is_gitlab_connected" vcs="gitlab">
                                    请到 Gitlab 平台创建「永久期限」的私人令牌，
                                    勾选<span class="underline px-1"> repo </span> 权限
                                </SetPersonToken>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </TeamLayout>
    </AppLayout>
</template>
