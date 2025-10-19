<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { useTranslations } from '@/composables/useTranslations';
import { usePage } from '@inertiajs/vue3';
import { Monitor, Moon, Sun } from 'lucide-vue-next';

// Get current locale from shared Inertia data and initialize
const page = usePage();
const initialLocale = (page.props.currentLocale as string) || 'en';

const { t } = useTranslations(initialLocale);
const { appearance, updateAppearance } = useAppearance();

const tabs = [
    { value: 'light', Icon: Sun, label: t('appearance.light') },
    { value: 'dark', Icon: Moon, label: t('appearance.dark') },
    { value: 'system', Icon: Monitor, label: t('appearance.system') },
] as const;
</script>

<template>
    <div class="inline-flex gap-1 rounded-lg bg-muted p-1">
        <button
            v-for="{ value, Icon, label } in tabs"
            :key="value"
            @click="updateAppearance(value)"
            :class="[
                'flex items-center gap-1.5 rounded-md px-3.5 py-1.5 transition-colors',
                appearance === value
                    ? 'bg-background text-foreground shadow-xs'
                    : 'text-muted-foreground hover:bg-muted/60 hover:text-foreground',
            ]"
        >
            <component :is="Icon" class="h-4 w-4" />
            <span class="text-sm">{{ label }}</span>
        </button>
    </div>
</template>
