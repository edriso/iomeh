<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { useTranslations } from '@/composables/useTranslations';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

const { t, isRTL } = useTranslations();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('settings.account'),
        href: '/settings/account',
    },
];

// Logout function
const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <Head :title="t('settings.account')" />

    <AppLayout :breadcrumb-items="breadcrumbItems" :dir="isRTL ? 'rtl' : 'ltr'">
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="t('account.actions')"
                    :description="t('account.actions_description')"
                />

                <div class="space-y-4">
                    <div
                        class="flex items-center justify-between rounded-lg border border-destructive/20 bg-destructive/5 p-4"
                    >
                        <div>
                            <h3 class="text-sm text-foreground">
                                {{ t('account.sign_out') }}
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                {{ t('account.sign_out_description') }}
                            </p>
                        </div>
                        <Button
                            variant="destructive"
                            @click="logout"
                            class="ml-4"
                        >
                            {{ t('account.sign_out') }}
                        </Button>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
