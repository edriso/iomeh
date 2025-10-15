<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Clock, Home, RefreshCw } from 'lucide-vue-next';

interface Props {
    title?: string;
    message?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Session Expired',
    message: 'Your session has expired. Please refresh the page to continue.',
});

const refreshPage = () => {
    window.location.reload();
};
</script>

<template>
    <Head :title="props.title" />

    <AppLayout>
        <div class="mt-20 flex items-center justify-center px-4">
            <div class="w-full max-w-lg text-center">
                <!-- Error Icon -->
                <div class="mb-8">
                    <div
                        class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-yellow-500/10"
                    >
                        <Clock class="h-12 w-12 text-yellow-500" />
                    </div>
                </div>

                <!-- Error Message -->
                <div class="mb-8">
                    <h1 class="mb-4 text-6xl text-yellow-500">419</h1>
                    <h2 class="mb-4 text-2xl text-foreground">
                        {{ props.title }}
                    </h2>
                    <p class="text-lg text-muted-foreground">
                        {{ props.message }}
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <Button as-child size="lg" class="w-full sm:w-auto">
                        <Link href="/">
                            <Home class="mr-2 h-5 w-5" />
                            Go Home
                        </Link>
                    </Button>

                    <Button
                        variant="outline"
                        size="lg"
                        class="w-full sm:w-auto"
                        @click="refreshPage"
                    >
                        <RefreshCw class="mr-2 h-5 w-5" />
                        Refresh Page
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
