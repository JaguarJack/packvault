<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    is_auth: Boolean,
    vcs: String,
});

const form = useForm({
    access_token: '',
    name: '',
    vcs: props.vcs,
});

const submit = () => {
    form.post(route('vcs.set.accessToken'), {
        preserveScroll: true,
        onSuccess: () => (open.value = false),
    });
};

const open = ref(false);
</script>

<template>
    <!-- 设置 personal token -->
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button variant="secondary">
                {{ is_auth ? '更新授权' : '授权' }}
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>设置 {{vcs}} 的个人令牌</DialogTitle>
                <DialogDescription>
                    <slot />
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid gap-2">
                    <Label for="name">Person token 用户名</Label>
                    <Input
                        id="name"
                        class="mt-1 block w-full"
                        required
                        v-model="form.name"
                        autocomplete="personal_token"
                        placeholder="设置 Person token 用户名"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="name">设置 Personal Token</Label>
                    <Input
                        id="name"
                        class="mt-1 block w-full"
                        v-model="form.access_token"
                        required
                        autocomplete="personal_token"
                        placeholder="设置 Personal Token"
                    />
                    <InputError class="mt-2" :message="form.errors.access_token" />
                </div>

                <DialogFooter>
                    <Button :disabled="form.processing"> 保存令牌 </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

<style scoped></style>
