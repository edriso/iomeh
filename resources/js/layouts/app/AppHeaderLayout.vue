<script setup lang="ts">
import AppHeader from '@/components/AppHeader.vue';
import BackToTop from '@/components/BackToTop.vue';
import LogActivityModal from '@/components/LogActivityModal.vue';
import { useLogActivity } from '@/composables/useLogActivity';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const { showLogActivityModal } = useLogActivity();

// Get interests from shared Inertia data
const interests = computed(() => (page.props.auth?.interests || []) as any[]);
</script>

<template>
    <div class="min-h-screen bg-background">
        <AppHeader />
        <main class="container mx-auto max-w-7xl px-4 py-8">
            <slot />
        </main>
        <BackToTop />

        <!-- Global Log Activity Modal -->
        <LogActivityModal
            v-model:open="showLogActivityModal"
            :interests="interests"
        />
    </div>
</template>
