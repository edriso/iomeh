<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { useTranslations } from '@/composables/useTranslations';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';

const { t, isRTL, currentLocale } = useTranslations();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('settings.appearance'),
        href: edit().url,
    },
];

const currentLanguage = computed(() => {
    return currentLocale.value === 'ar' ? 'العربية' : 'English';
});
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

                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-medium">
                                        {{ t('appearance.current_language') }}
                                    </h4>
                                    <p class="text-sm text-muted-foreground">
                                        {{ t('appearance.currently_using') }}:
                                        <strong>{{ currentLanguage }}</strong>
                                    </p>
                                </div>

                                <LanguageSwitcher
                                    :show-label="true"
                                    variant="default"
                                />

                                <div class="text-sm text-muted-foreground">
                                    <p>
                                        {{ t('appearance.language_help_text') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
