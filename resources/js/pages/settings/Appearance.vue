<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { useTranslations } from '@/composables/useTranslations';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';

const { t, isRTL } = useTranslations();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('settings.appearance'),
        href: edit().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems" :dir="isRTL ? 'rtl' : 'ltr'">
        <Head :title="t('settings.appearance')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="t('settings.appearance')"
                    :description="t('appearance.description')"
                />

                <!-- Theme Settings -->
                <div class="space-y-4">
                    <div class="rounded-lg border bg-card p-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium">
                                    {{ t('appearance.theme') }}
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    {{ t('appearance.theme_description') }}
                                </p>
                            </div>

                            <AppearanceTabs />
                        </div>
                    </div>
                </div>

                <!-- Language Settings -->
                <div class="space-y-4">
                    <div class="rounded-lg border bg-card p-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium">
                                    {{ t('settings.language') }}
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    {{ t('appearance.language_description') }}
                                </p>
                            </div>

                            <LanguageSwitcher
                                :show-label="true"
                                variant="default"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
