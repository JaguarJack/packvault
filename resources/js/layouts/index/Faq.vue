<script setup lang="ts">

</script>

<template>
    <section id="faq" class="container mx-auto px-4 py-20 relative z-10 fade-in-section">
        <!-- Section background with glassmorphic effect -->
        <div class="absolute inset-0 -z-10 overflow-hidden rounded-3xl">
            <div class="absolute inset-0  backdrop-blur-md"></div>
            <div class="absolute top-1/2 left-1/4 w-[320px] h-[320px]  rounded-full filter blur-[85px] opacity-60 "></div>
            <div class="absolute bottom-1/2 right-1/4 w-[260px] h-[260px] rounded-full filter blur-[65px] opacity-60 "></div>
        </div>
        <h2 class="text-2xl md:text-4xl font-bold text-center mb-16 text-slate-100">常见问题</h2>
        <div class="max-w-3xl mx-auto">
            <div class="space-y-6">
                <div class="p-6 rounded-xl backdrop-blur-sm bg-gray-800/70 border border-gray-700/50 shadow-md hover:shadow-lg transition-all fade-in-section" style="animation-delay: 0s;">
                    <h3 class="text-lg font-semibold mb-2 text-slate-100">如何开始使用私有包托管服务？</h3>
                    <p class="text-slate-200">注册账户并选择适合您的方案后，您将获得访问我们平台的权限。您可以按照我们的文档指南设置您的私有包仓库，并开始上传您的包。</p>
                </div>
                <div class="p-6 rounded-xl backdrop-blur-sm bg-gray-800/70 border border-gray-700/50 shadow-md hover:shadow-lg transition-all fade-in-section" style="animation-delay: 0.3s;">
                    <h3 class="text-lg font-semibold mb-2 text-slate-100">支持哪些版本控制系统？</h3>
                    <p class="text-slate-200">我们支持 Git 作为主要的版本控制系统，兼容 GitHub、Gitee 和 Coding 等平台。您可以轻松地将您的代码仓库与我们的服务集成。</p>
                </div>
                <div class="p-6 rounded-xl backdrop-blur-sm bg-gray-800/70 border border-gray-700/50 shadow-md hover:shadow-lg transition-all fade-in-section" style="animation-delay: 0.6s;">
                    <h3 class="text-lg font-semibold mb-2 text-slate-100">如何管理团队成员的访问权限？</h3>
                    <p class="text-slate-200">通过我们的团队管理界面，您可以邀请团队成员并分配不同级别的访问权限，包括只读、贡献者和管理员等角色。</p>
                </div>
                <div class="p-6 rounded-xl backdrop-blur-sm bg-gray-800/70 border border-gray-700/50 shadow-md hover:shadow-lg transition-all fade-in-section" style="animation-delay: 0.9s;">
                    <h3 class="text-lg font-semibold mb-2 text-slate-100">是否提供企业级支持？</h3>
                    <p class="text-slate-200">是的，我们的企业版方案包括专属的技术支持和定制服务，确保您的团队能够获得最佳的使用体验。</p>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>

</style>
