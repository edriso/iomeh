<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertCircle,
    Clock,
    Home,
    RefreshCw,
    Shield,
    Wrench,
    Zap,
} from 'lucide-vue-next';

interface Props {
    status: number;
    message?: string;
}

const props = withDefaults(defineProps<Props>(), {
    message: 'An error occurred',
});

const getErrorInfo = (status: number) => {
    switch (status) {
        case 403:
            return {
                title: '403',
                heading: 'Access Forbidden',
                description:
                    "You don't have permission to access this resource.",
                icon: Shield,
                color: 'text-orange-500',
                bgColor: 'bg-orange-500/10',
                showRefresh: false,
            };
        case 419:
            return {
                title: '419',
                heading: 'Session Expired',
                description:
                    'Your session has expired. Please refresh the page to continue.',
                icon: Clock,
                color: 'text-yellow-500',
                bgColor: 'bg-yellow-500/10',
                showRefresh: true,
            };
        case 429:
            return {
                title: '429',
                heading: 'Too Many Requests',
                description:
                    "You've made too many requests. Please wait a moment.",
                icon: Zap,
                color: 'text-blue-500',
                bgColor: 'bg-blue-500/10',
                showRefresh: true,
            };
        case 503:
            return {
                title: '503',
                heading: 'Service Unavailable',
                description:
                    'The service is temporarily unavailable. Please try again later.',
                icon: Wrench,
                color: 'text-purple-500',
                bgColor: 'bg-purple-500/10',
                showRefresh: true,
            };
        default:
            return {
                title: status.toString(),
                heading: 'Error',
                description: props.message,
                icon: AlertCircle,
                color: 'text-destructive',
                bgColor: 'bg-destructive/10',
                showRefresh: true,
            };
    }
};

const errorInfo = getErrorInfo(props.status);

const refreshPage = () => {
    window.location.reload();
};
</script>

<template>
    <Head :title="errorInfo.heading" />

    <AppLayout>
        <div class="mt-20 flex items-center justify-center px-4">
            <div class="w-full max-w-lg text-center">
                <!-- Error Icon -->
                <div class="mb-8">
                    <div
                        class="mx-auto flex h-24 w-24 items-center justify-center rounded-full"
                        :class="errorInfo.bgColor"
                    >
                        <component
                            :is="errorInfo.icon"
                            class="h-12 w-12"
                            :class="errorInfo.color"
                        />
                    </div>
                </div>

                <!-- Error Message -->
                <div class="mb-8">
                    <h1 class="mb-4 text-6xl" :class="errorInfo.color">
                        {{ errorInfo.title }}
                    </h1>
                    <h2 class="mb-4 text-2xl text-foreground">
                        {{ errorInfo.heading }}
                    </h2>
                    <p class="text-lg text-muted-foreground">
                        {{ errorInfo.description }}
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
                        v-if="errorInfo.showRefresh"
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
