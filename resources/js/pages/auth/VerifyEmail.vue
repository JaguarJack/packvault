<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <AuthLayout title="邮箱验证" description="请点击我们刚刚发送到您邮箱的链接来验证您的电子邮件地址。">
        <Head title="Email verification" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            一个新的验证链接已发送至您注册时提供的电子邮件地址。
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button :disabled="form.processing" variant="secondary">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                重新发送验证邮件
            </Button>

            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm"> 登 出 </TextLink>
        </form>
    </AuthLayout>
</template>
