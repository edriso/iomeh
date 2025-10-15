<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Home, Dumbbell } from 'lucide-vue-next';

interface Props {
    title?: string;
    message?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Page Not Found',
    message: "The page you're looking for doesn't exist or has been moved.",
});
</script>

<template>
    <Head :title="props.title" />

    <AppLayout>
        <div class="mt-20 flex items-center justify-center px-4">
            <div class="w-full max-w-lg text-center">
                <!-- Error Icon -->
                <div class="mb-8">
                    <div
                        class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-primary/10"
                    >
                        <Dumbbell class="h-12 w-12 text-primary" />
                    </div>
                </div>

                <!-- Error Message -->
                <div class="mb-8">
                    <h1 class="mb-4 text-6xl text-primary">404</h1>
                    <h2 class="mb-4 text-2xl text-foreground">
                        {{ props.title }}
                    </h2>
                    <p class="text-lg text-muted-foreground">
                        {{ props.message }}
                    </p>
                </div>

                <!-- Action Button -->
                <div class="flex justify-center">
                    <Button as-child size="lg">
                        <Link href="/">
                            <Home class="mr-2 h-5 w-5" />
                            Go Home
                        </Link>
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
