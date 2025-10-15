<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Account settings',
        href: '/settings/account',
    },
];

// Logout function
const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <Head title="Account Settings" />

    <AppLayout :breadcrumb-items="breadcrumbItems">
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Account actions"
                    description="Manage your account session and security"
                />

                <div class="space-y-4">
                    <div
                        class="flex items-center justify-between rounded-lg border border-destructive/20 bg-destructive/5 p-4"
                    >
                        <div>
                            <h3 class="text-sm text-foreground">Sign out</h3>
                            <p class="text-sm text-muted-foreground">
                                Sign out of your account on this device
                            </p>
                        </div>
                        <Button
                            variant="destructive"
                            @click="logout"
                            class="ml-4"
                        >
                            Sign out
                        </Button>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
