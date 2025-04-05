<script setup lang="ts">
import LicenseLayout from '@/layouts/license/Layout.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { TransitionRoot } from '@headlessui/vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { MultiSelect } from '@/components/ui/combobox'

import HeadingSmall from '@/components/HeadingSmall.vue';

defineProps({
    packages: Array<any>,
    types: Object
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: '新增 License',
        href: '/license/create',
    },
];

const form = useForm({
    title: '',
    type: '',
    packages: [],
});

const submit = () => {
    form.post(route('license.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="新增 License" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <LicenseLayout>
            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <div class="flex flex-col space-y-6">
                        <HeadingSmall title="新增 License" description="新增 License" />
                        <div class="flex w-full flex-col">
                            <form @submit.prevent="submit" class="space-y-6">
                                <div class="grid gap-2">
                                    <Label for="name">License 名称</Label>
                                    <Input
                                        id="name"
                                        class="mt-1 block w-full"
                                        v-model="form.title"
                                        required
                                        autocomplete="title"
                                        placeholder="License 名称"
                                    />
                                    <InputError class="mt-2" :message="form.errors.title" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="short_name">License 订阅方式</Label>
                                    <Select v-model="form.type" required>
                                        <SelectTrigger>
                                            <SelectValue placeholder="License 订阅方式" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem :value="key" v-for="(item, key) in types" :key="key">
                                                    {{ item }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError class="mt-2" :message="form.errors.type" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="short_name">授权 package</Label>
                                    <MultiSelect
                                        v-model="form.packages"
                                        :options="packages"
                                        placeholder="请选择授权 package"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.packages" />
                                </div>

                                <div class="flex items-center justify-center gap-4">
                                    <Button :disabled="form.processing">保存</Button>

                                    <TransitionRoot
                                        :show="form.recentlySuccessful"
                                        enter="transition ease-in-out"
                                        enter-from="opacity-0"
                                        leave="transition ease-in-out"
                                        leave-to="opacity-0"
                                    >
                                        <p class="text-sm text-neutral-600">已保存</p>
                                    </TransitionRoot>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </LicenseLayout>
    </AppLayout>
</template>
