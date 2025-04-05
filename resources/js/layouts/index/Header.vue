<script setup lang="ts">

import { Link, usePage } from '@inertiajs/vue3';
import type { SharedData, User } from '@/types';
import { onMounted, ref } from 'vue';
import UserInfo from '@/components/UserInfo.vue';
const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const header = ref(null);
const atTop = ref(true);
const isDarkMode = ref(false);
const handleScroll = () => {
    if (window.scrollY > 20) {
        atTop.value = false;
    } else {
        atTop.value = true;
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    // Check initial scroll position
    handleScroll();

    // Check if dark mode was previously set
    const savedDarkMode = localStorage.getItem('darkMode');
    if (savedDarkMode === 'true') {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
    }
});
// Toggle dark mode
const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
    }
};

</script>

<template>
    <div class="fixed top-0 left-0 w-full flex justify-center z-50 px-4 transition-all duration-700">
        <header
            ref="header"
            class="transition-all duration-700 w-full md:max-w-[60%] md:mx-auto py-6 px-4 md:px-6 flex items-center justify-between relative"
            :class="{
            'rounded-full mt-4 scale-95 md:scale-95': !atTop,
            'scale-100': atTop
          }"
        >
            <!-- Background with blur effect -->
            <div
                class="absolute inset-0 rounded-full transition-all duration-700"
                :class="{
              'bg-background/70 backdrop-blur-md shadow-md dark:bg-gray-900/80': !atTop,
              'bg-transparent': atTop
            }"
            ></div>

            <!-- Content positioned above the blurred background -->
            <div class="flex items-center z-10 relative">
                <h2 class="text-xl font-bold transition-all dark:text-white" :class="{'scale-90': !atTop}">Composer Vault</h2>
            </div>
            <nav class="hidden md:flex space-x-10 z-10 relative">
                <a href="#features" class="text-lg font-medium text-foreground/80 hover:text-foreground transition-colors dark:text-gray-300 dark:hover:text-white">功 能</a>
                <a href="#pricing" @click.prevent="scrollToAnchor('pricing')" class="text-lg font-medium text-foreground/80 hover:text-foreground transition-colors dark:text-gray-300 dark:hover:text-white">价 格</a>
                <a href="#faq" @click.prevent="scrollToAnchor('faq')" class="text-lg font-medium text-foreground/80 hover:text-foreground transition-colors dark:text-gray-300 dark:hover:text-white">常见问题</a>
            </nav>
            <div class="z-10 relative flex items-center gap-4">
                <!-- Dark mode toggle -->
                <button @click="toggleDarkMode" class="p-2 rounded-full bg-background/50 dark:bg-gray-800/50 backdrop-blur-sm hover:bg-background/70 dark:hover:bg-gray-700/70 transition-colors">
                    <svg v-if="isDarkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700 dark:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                    </svg>
                </button>
                <Link v-if="user" :href="route('home')" class="flex items-center gap-x-2">
                    <UserInfo :user="user" />
                </Link>

                <a v-else :href="route('login')" class="inline-flex h-10 items-center justify-center rounded-md bg-primary/90 backdrop-blur-sm px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition-all hover:bg-primary/100 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring dark:bg-primary/80 dark:hover:bg-primary/90">
                    登 录
                </a>
            </div>
        </header>
    </div>
</template>
