<script setup lang="ts"></script>

<template>
    <section id="pricing" class="relative z-10 py-20 fade-in-section">
        <!-- Section background with glassmorphic effect -->
        <div class="absolute inset-0 -z-10">
            <div
                class="absolute inset-0 bg-gradient-to-l backdrop-blur-md from-yellow-500/10 to-orange-500/10 dark:from-yellow-500/5 dark:to-orange-500/5"
            ></div>
            <div
                class="absolute right-0 top-0 h-[600px] w-[600px] rounded-full bg-yellow-500/20 opacity-60 blur-[120px] filter dark:bg-yellow-500/10"
            ></div>
            <div
                class="absolute bottom-0 left-0 h-[500px] w-[500px] rounded-full bg-orange-500/20 opacity-60 blur-[100px] filter dark:bg-orange-500/10"
            ></div>
        </div>
        <div class="container px-4 mx-auto">
            <h2 class="mb-16 text-2xl font-bold text-center dark:text-white md:text-4xl">Pricing for every stage</h2>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <!-- Open Source Plan -->
                <div
                    class="p-6 rounded-xl border shadow-md backdrop-blur-sm transition-all hover-lift fade-in-section border-white/20 bg-white/10 hover:shadow-lg dark:border-gray-800/30 dark:bg-gray-900/10"
                    style="animation-delay: 0s"
                >
                    <h3 class="mb-4 text-xl font-bold dark:text-white">Open Source</h3>
                    <p class="mb-2 text-muted-foreground dark:text-gray-300">MIT License</p>
                    <ul class="flex-grow mb-8 space-y-3">
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Authentication (Clerk/Supabase/Magic)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Form (react-hook-form)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Modals manager</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Hotkeys</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Web3 components</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">And much more...</span>
                        </li>
                    </ul>
                    <a
                        href="#signup"
                        class="inline-flex justify-center items-center px-4 py-2 w-full h-10 text-sm font-medium rounded-md shadow-sm backdrop-blur-sm transition-all bg-primary/90 text-primary-foreground hover:bg-primary/100 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring dark:bg-primary/80 dark:hover:bg-primary/90"
                    >
                        Sign Up
                    </a>
                </div>

                <!-- Bootstrap Plan -->
                <div
                    class="p-6 rounded-xl border shadow-md backdrop-blur-sm transition-all hover-lift fade-in-section border-white/20 bg-white/10 hover:shadow-lg dark:border-gray-800/30 dark:bg-gray-900/10"
                    style="animation-delay: 0.5s"
                >
                    <h3 class="mb-4 text-xl font-bold dark:text-white">Bootstrap</h3>
                    <p class="mb-2 text-muted-foreground dark:text-gray-300">One project</p>
                    <ul class="flex-grow mb-8 space-y-3">
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">One developer</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Advanced components</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Multiple themes</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Next.js and Electron boilerplates</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Private discord community</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">1 year of updates</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Private beta access</span>
                        </li>
                    </ul>
                    <a
                        href="#signup"
                        class="inline-flex justify-center items-center px-4 py-2 w-full h-10 text-sm font-medium rounded-md shadow-sm backdrop-blur-sm transition-all bg-primary/90 text-primary-foreground hover:bg-primary/100 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring dark:bg-primary/80 dark:hover:bg-primary/90"
                    >
                        Sign Up
                    </a>
                </div>

                <!-- Startup Plan -->
                <div
                    class="p-6 rounded-xl border shadow-md backdrop-blur-sm transition-all hover-lift fade-in-section border-primary/20 bg-primary/5 hover:shadow-lg"
                    style="animation-delay: 1s"
                >
                    <h3 class="mb-4 text-xl font-bold dark:text-white">Startup</h3>
                    <div class="mb-4">
                        <span class="line-through text-muted-foreground dark:text-gray-400">€999,-</span>
                        <p class="text-2xl font-bold dark:text-white">€499,-</p>
                    </div>
                    <ul class="flex-grow mb-8 space-y-3">
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Unlimited projects</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Unlimited developers</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">1 year of updates</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Everything from Bootstrap</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-primary dark:text-yellow-400">✓</span>
                            <span class="text-muted-foreground dark:text-gray-300">Private beta access</span>
                        </li>
                    </ul>
                    <a
                        href="#signup"
                        class="inline-flex justify-center items-center px-4 py-2 w-full h-10 text-sm font-medium rounded-md shadow-sm backdrop-blur-sm transition-all bg-primary/90 text-primary-foreground hover:bg-primary/100 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring dark:bg-primary/80 dark:hover:bg-primary/90"
                    >
                        Sign Up
                    </a>
                    <p class="mt-4 text-xs text-muted-foreground dark:text-gray-400">VAT may be applicable depending on your location.</p>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped></style>
