<script setup lang="ts">
import AppHeader from '@/components/AppHeader.vue';
import BackToTop from '@/components/BackToTop.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div class="min-h-screen bg-background">
        <AppHeader />
        <main class="container mx-auto max-w-7xl px-4 py-8">
            <slot />
        </main>
        <BackToTop />
    </div>
</template>
