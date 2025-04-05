<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import TeamLayout from '@/layouts/team/Layout.vue';
import { Head,  useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { TransitionRoot } from '@headlessui/vue';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea'
import { Input } from '@/components/ui/input';

interface Props {
    // apps?: Array<string>;
    repositories?: Array<{name: string,url: string, description: string}>
}

defineProps<Props>();

// const page = usePage<SharedData>();
// const user = page.props.auth.user as User;

const form = useForm({
    url: '',
    name: '',
    repo_name: '',
    app_id: null,
    description: ''
});

const submit = () => {
    form.post(route('repository.store'), {
        preserveScroll: true,
    });
};
</script>
<template>
    <AppLayout>
        <Head title="添加 Package" />

        <TeamLayout>
            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <div class="flex flex-col space-y-6">
                        <HeadingSmall title="添加 Package" description="添加 Package" />

                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid gap-2">
                                <Label for="url">Package 仓库</Label>
                                <Input id="url" class="mt-1 block w-full" v-model="form.url" required autocomplete="url" placeholder="Package 仓库 URL" />
                                <InputError class="mt-2" :message="form.errors.url" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="description">Package 说明</Label>

                                <Textarea placeholder="Package 说明" v-model="form.description"/>

                                <InputError :message="form.errors.description" />
                            </div>

                            <div class="flex items-center gap-4">
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
                </section>
            </div>

        </TeamLayout>
    </AppLayout>
</template>

