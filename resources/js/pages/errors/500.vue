<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Home, RefreshCw, Server } from 'lucide-vue-next';

const refreshPage = () => {
    window.location.reload();
};
</script>

<template>
    <Head title="Server Error" />

    <AppLayout>
        <div class="mt-20 flex items-center justify-center px-4">
            <div class="w-full max-w-lg text-center">
                <!-- Error Icon -->
                <div class="mb-8">
                    <div
                        class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-destructive/10"
                    >
                        <Server class="h-12 w-12 text-destructive" />
                    </div>
                </div>

                <!-- Error Message -->
                <div class="mb-8">
                    <h1 class="mb-4 text-6xl text-destructive">500</h1>
                    <h2 class="mb-4 text-2xl text-foreground">Server Error</h2>
                    <p class="text-lg text-muted-foreground">
                        Something went wrong on our end. Please try again later.
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
                        Try Again
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
