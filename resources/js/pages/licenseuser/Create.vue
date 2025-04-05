<script setup lang="ts">
import LicenseUserLayout from '@/layouts/licenseuser/Layout.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { TransitionRoot } from '@headlessui/vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    NumberField,
    NumberFieldContent,
    NumberFieldDecrement,
    NumberFieldIncrement,
    NumberFieldInput,
} from '@/components/ui/number-field'

import HeadingSmall from '@/components/HeadingSmall.vue';

defineProps({
    licenses: Array<any>
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: '新增用户',
        href: '/license/user/create',
    },
];

const form = useForm({
    email: '',
    allow_ip_number: 1,
    license_id: null
});

const submit = () => {
    form.post(route('license.user.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="新增用户" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <LicenseUserLayout>
            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <div class="flex flex-col space-y-6">
                        <HeadingSmall title="新增用户" description="新增用户" />
                        <div class="flex w-full flex-col">
                            <form @submit.prevent="submit" class="space-y-6">
                                <div class="grid gap-2">
                                    <Label for="name">用户邮箱</Label>
                                    <Input
                                        id="name"
                                        class="mt-1 block w-full"
                                        v-model="form.email"
                                        required
                                        type="email"
                                        autocomplete="title"
                                        placeholder="用户邮箱"
                                    />
                                    <InputError class="mt-2" :message="form.errors.email" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="short_name">授权 License</Label>
                                    <Select v-model="form.license_id" required>
                                        <SelectTrigger>
                                            <SelectValue placeholder="选择授权 License" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem :value="license.id" v-for="license in licenses" :key="license.id">
                                                    {{ license.title }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <div class="flex justify-end text-sm" v-if="!licenses?.length">还没有创建任何 License?去
                                        <Link :href="route('license.create')" class="text-indigo-600"> 添加</Link>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.license_id" />
                                </div>
                                <div class="grid gap-2">
                                    <NumberField v-model="form.allow_ip_number">
                                        <Label>允许访问的 IP 数</Label>
                                        <NumberFieldContent>
                                            <NumberFieldDecrement />
                                            <NumberFieldInput />
                                            <NumberFieldIncrement />
                                        </NumberFieldContent>
                                    </NumberField>
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
        </LicenseUserLayout>
    </AppLayout>
</template>
