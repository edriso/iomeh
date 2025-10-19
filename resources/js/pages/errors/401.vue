<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useTranslations } from '@/composables/useTranslations';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Home, Lock, LogIn, RefreshCw } from 'lucide-vue-next';

interface Props {
    title?: string;
    message?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Authentication Required',
    message: 'You need to be logged in to access this page.',
});

const { t, isRTL } = useTranslations();

const refreshPage = () => {
    window.location.reload();
};
</script>

<template>
    <Head :title="props.title" />

    <AppLayout>
        <div
            class="mt-20 flex items-center justify-center px-4"
            :dir="isRTL ? 'rtl' : 'ltr'"
        >
            <div class="w-full max-w-lg text-center">
                <!-- Error Icon -->
                <div class="mb-8">
                    <div
                        class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-red-500/10"
                    >
                        <Lock class="h-12 w-12 text-red-500" />
                    </div>
                </div>

                <!-- Error Message -->
                <div class="mb-8">
                    <h1 class="mb-4 text-6xl text-red-500">401</h1>
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
                        <Link href="/login">
                            <LogIn class="mr-2 h-5 w-5" />
                            {{ t('errors.login') }}
                        </Link>
                    </Button>

                    <Button
                        as-child
                        variant="outline"
                        size="lg"
                        class="w-full sm:w-auto"
                    >
                        <Link href="/">
                            <Home class="mr-2 h-5 w-5" />
                            {{ t('errors.go_home') }}
                        </Link>
                    </Button>

                    <Button
                        variant="outline"
                        size="lg"
                        class="w-full sm:w-auto"
                        @click="refreshPage"
                    >
                        <RefreshCw class="mr-2 h-5 w-5" />
                        {{ t('errors.refresh_page') }}
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
