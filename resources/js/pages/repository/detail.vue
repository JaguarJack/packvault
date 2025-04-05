<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import TeamLayout from '@/layouts/team/Layout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Avatar } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import {broadcastOnPrivate, stopListeningOnChannelEvent} from '@/lib/utils';
import { Loader2 } from 'lucide-vue-next';
import { ScrollArea } from '@/components/ui/scroll-area'

const { getInitials } = useInitials();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'package 详情',
        href: '/#',
    },
];

interface Props {
    package: {
        name: string;
        description: string;
        installs?: number;
        tags?: {
            name: string;
            id: number | string;
        }[];
    };
}

const props = defineProps<Props>();
const page = usePage()
// 删除 tag
const showDeleteDialog = ref(false);
const tagToDelete = ref<{ id: number | string; name: string } | null>(null);

// Function to open delete confirmation dialog
const openDeleteDialog = (tag: any) => {
    tagToDelete.value = tag;
    showDeleteDialog.value = true;
};

// Function to delete a tag
const confirmDeleteTag = () => {
    if (tagToDelete.value) {
        router.delete(route('repository.delete.tag', tagToDelete.value.id));
        showDeleteDialog.value = false;
    }
};

// Function to cancel delete
const cancelDelete = () => {
    showDeleteDialog.value = false;
    tagToDelete.value = null;
};

// For tags display control
const showAllTags = ref(false);
const initialTagsCount = 5; // Number of tags to show initially

// Function to toggle showing all tags
const toggleShowAllTags = () => {
    showAllTags.value = !showAllTags.value;
};

// Compare version strings for sorting
const compareVersions = (a: string, b: string): number => {
    const aParts = a.split('.').map((part) => parseInt(part, 10));
    const bParts = b.split('.').map((part) => parseInt(part, 10));

    // Compare each part of the version
    for (let i = 0; i < Math.max(aParts.length, bParts.length); i++) {
        const aVal = i < aParts.length ? aParts[i] : 0;
        const bVal = i < bParts.length ? bParts[i] : 0;

        if (aVal !== bVal) {
            return bVal - aVal; // Descending order
        }
    }

    return 0;
};

// Computed property for sorted tags
const sortedTags = computed(() => {
    if (!props.package.tags) return [];
    return [...props.package.tags].sort((a, b) => compareVersions(a.name, b.name));
});

// Computed to get the tags to display
const displayedTags = computed(() => {
    return showAllTags.value ? sortedTags.value : sortedTags.value.slice(0, initialTagsCount);
});

const tag = ref({});
const tagInfo = (name: string = '') => {
    if (name) {
        props.package.tags.forEach((t) => {
            if (t.name === name) {
                tag.value = t;
            }
        });
    } else {
        tag.value = displayedTags.value[0];
    }
};
const content = ref('')
const isBuilding = ref(false)
const scrollContainerRef = ref(null)

// 滚动到底部的函数
const scrollToBottom = () => {
    if (scrollContainerRef.value) {
        // 直接设置滚动位置到底部
        setTimeout(() => {
            scrollContainerRef.value.scrollTop = scrollContainerRef.value.scrollHeight
        }, 50) // 短暂延迟确保内容已更新
    }
}

// 使用 watch 监听 content 变化
watch(content, () => {
        // 内容变化时滚动到底部
        scrollToBottom()
}, { immediate: false, deep: true })

const isOver = ref(true)
onMounted(() => {
    broadcastOnPrivate('package-build-output.' + props.package.id, '.package.build.output', (e) => {
        if (e.message !== 'over') {
            content.value = e.message
            isBuilding.value = true
            isOver.value = false
        } else {
            isOver.value = true;
        }
    })
    tagInfo()
})

onUnmounted(() => {
    stopListeningOnChannelEvent('package-build-output.' + props.package.id, '.package.build.output')
})
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Package 详情" />
        <TeamLayout>
            <div class="flex-1 space-y-6 md:max-w-5xl">
                <!-- Package Header Section -->
                <div class="space-y-4">
                    <div class="flex items-center">
                        <span class="mr-2 text-muted-foreground">★</span>
                        <h1 class="text-xl font-semibold md:text-2xl">{{ package.name }}</h1>
                    </div>

                    <p class="text-sm text-muted-foreground">
                        {{ package.description || '' }}
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <Popover>
                            <PopoverTrigger>
                                <Button variant="destructive" size="sm">删除</Button>
                            </PopoverTrigger>
                            <PopoverContent class="flex items-center justify-center gap-x-3">
                                <span class="text-sm">确定删除 <span class="text-base text-red-700">{{ package.name }}</span> 包吗? 删除之后用户将无法访问</span>
                                <Link :href="route('repository.delete', package.id)" method="delete"><Button size="sm">确认</Button></Link>
                            </PopoverContent>
                        </Popover>
                        <Link :href="route('repository.update', package.id)" method="put">
                            <Button variant="default" size="sm" v-if="! page.props.is_expired">
                                <Loader2 class="w-4 h-4 animate-spin" v-if="!isOver"/>
                                <span v-else>构建</span>
                            </Button>
                        </Link>
                    </div>
                </div>

                <Separator />

                <!-- Package Details Section -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Left Column - Main Info -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Version Info -->
                        <Card>
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <span class="text-base">{{ tag?.name || 'none' }}</span>
                                    <span class="text-xs text-muted-foreground">{{ tag?.updated_at || 'none' }}</span>
                                </div>
                            </CardHeader>
                        </Card>

                        <!-- Requirements Section -->
                        <Card>
                            <CardContent class="p-6">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div>
                                        <h3 class="mb-3 text-sm font-medium">requires</h3>
                                        <ul class="list-disc space-y-1.5 pl-5 text-sm text-muted-foreground">
                                            <li v-for="(req, index) in tag?.require || []" :key="index">{{ index }}: {{ req }}</li>
                                        </ul>
                                    </div>

                                    <div>
                                        <h3 class="mb-3 text-sm font-medium">requires (dev)</h3>
                                        <div
                                            class="text-sm text-muted-foreground"
                                            v-if="!props.package.requires_dev || props.package.requires_dev.length === 0"
                                        >
                                            None
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- License and Author Info -->
                        <Card v-if="tag?.authors?.length > 0">
                            <CardContent class="space-y-2 p-6">
                                <div class="flex items-center" v-for="author in tag.authors" :key="author.email">
                                    <span class="mr-2"
                                        ><Avatar>{{ getInitials(author.name) }}</Avatar></span
                                    >
                                    <span class="text-sm">{{ author.name }} &lt;{{ author.email }}&gt;</span>
                                </div>
                            </CardContent>
                        </Card>

                        <div v-if="isBuilding" ref="scrollContainerRef" class="h-48 p-5 overflow-auto bg-gray-50 dark:bg-gray-900 rounded-md border border-gray-200 dark:border-gray-700">
                            <div class="font-mono text-sm whitespace-pre-wrap p-4 text-gray-800 dark:text-gray-200">
                                {{ content }}
                            </div>
                            <Loader2 class="w-4 h-4 mr-2 ml-4 animate-spin" v-if="!isOver"/>
                        </div>
                    </div>

                    <!-- Right Column - Stats & Versions -->
                    <div class="lg:col-span-1">
                        <Card>
                            <CardHeader>
                                <CardTitle class="text-base">详情</CardTitle>
                            </CardHeader>

                            <CardContent class="space-y-4">
                                <div class="space-y-2">
                                    <div>
                                        <a href="#" class="text-sm text-primary hover:underline">仓库地址 : {{ package.url }}</a>
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="space-y-2 pt-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-muted-foreground">安装量 :</span>
                                        <span class="text-sm font-medium">{{ package.downloads || 0 }}</span>
                                    </div>
                                </div>

                                <!-- tags -->
                                <ScrollArea class="pt-4 h-72">
                                    <h3 class="mb-3 text-sm font-medium">版本</h3>
                                    <div class="space-y-1">
                                        <div v-if="package.tags && package.tags.length > 0">
                                            <div
                                                v-for="(tag, index) in displayedTags"
                                                :key="index"
                                                class="flex items-center justify-between border-b border-border py-1.5 last:border-0 hover:cursor-pointer"
                                            >
                                                <div class="w-full text-sm" @click="tagInfo(tag.name)">
                                                    {{ tag.name }}
                                                </div>
                                                <Button variant="ghost" size="icon" class="h-6 w-6" @click.prevent="openDeleteDialog(tag)">
                                                    <span class="text-muted-foreground"> × </span>
                                                </Button>
                                            </div>

                                            <!-- Show more/less button -->
                                            <div v-if="props.package.tags.length > initialTagsCount" class="mt-2 text-center">
                                                <Button variant="outline" size="sm" @click="toggleShowAllTags" class="w-full text-xs">
                                                    {{ showAllTags ? '收起' : `显示全部 (${props.package.tags.length})` }}
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </ScrollArea>

                                <!-- Auto-update Info -->
                                <div class="pt-2 text-sm text-muted-foreground">
                                    <!--<p>This package is {{ props.package.is_auto_updated ? 'auto-updated' : 'manually updated' }}.</p>-->
                                    <p class="mt-1">最近更新于: {{ tag?.updated_at || 'none' }}</p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </TeamLayout>
    </AppLayout>

    <!-- Delete Confirmation Dialog -->
    <Dialog :open="showDeleteDialog" @close="cancelDelete">
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle>确认删除标签</DialogTitle>
            </DialogHeader>

            <div class="py-4">
                <DialogDescription>确定要删除标签 {{ tagToDelete?.name }} 吗?</DialogDescription>
            </div>

            <DialogFooter>
                <Button variant="outline" size="sm" @click="cancelDelete">取消</Button>
                <Button variant="destructive" size="sm" @click="confirmDeleteTag">确认删除</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
