<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { setLocale, useTranslations } from '@/composables/useTranslations';
import { router, usePage } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import { Globe } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    showLabel?: boolean;
    variant?: 'default' | 'compact' | 'single';
}

withDefaults(defineProps<Props>(), {
    showLabel: true,
    variant: 'default',
});

// Get current locale from shared Inertia data and initialize
const page = usePage();
const initialLocale = (page.props.currentLocale as string) || 'en';

const { t, currentLocale, isRTL } = useTranslations(initialLocale);
const isLoading = ref(false);
const showDropdown = ref(false);
const dropdownRef = ref(null);

// Close dropdown when clicking outside
onClickOutside(dropdownRef, () => {
    showDropdown.value = false;
});

const languages = [
    { code: 'en', name: 'English', flag: '🇺🇸' },
    { code: 'ar', name: 'العربية', flag: '🇸🇦' },
];

const currentLanguage = computed(() => {
    return (
        languages.find((lang) => lang.code === currentLocale.value) ||
        languages[0]
    );
});

const toggleLanguage = () => {
    showDropdown.value = !showDropdown.value;
};

const changeLanguage = async (locale: string) => {
    if (locale === currentLocale.value) {
        showDropdown.value = false;
        return;
    }

    isLoading.value = true;
    showDropdown.value = false;

    try {
        await router.post(
            '/language/switch',
            { locale },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    // Update the global locale state immediately
                    setLocale(locale);
                    isLoading.value = false;
                },
                onError: () => {
                    isLoading.value = false;
                },
            },
        );
    } catch {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="space-y-2">
        <label v-if="showLabel" class="text-sm font-medium text-foreground">
            {{ t('settings.language') }}
        </label>

        <div v-if="variant === 'compact'" class="flex items-center space-x-2">
            <Button
                v-for="language in languages"
                :key="language.code"
                :variant="
                    currentLocale === language.code ? 'default' : 'outline'
                "
                size="sm"
                :disabled="isLoading"
                @click="changeLanguage(language.code)"
                class="flex items-center space-x-1"
            >
                <span>{{ language.flag }}</span>
                <span class="hidden sm:inline">{{ language.name }}</span>
            </Button>
        </div>

        <div
            v-else-if="variant === 'single'"
            ref="dropdownRef"
            class="relative"
        >
            <Button
                variant="ghost"
                size="sm"
                :disabled="isLoading"
                @click="toggleLanguage"
                class="flex items-center space-x-2 rounded-lg border border-border/50 bg-background/50 px-3 py-2 transition-all duration-200 hover:border-border hover:bg-background/80"
            >
                <Globe class="h-4 w-4 text-muted-foreground" />
                <span class="text-sm font-medium"
                    >{{ currentLanguage.flag }} {{ currentLanguage.name }}</span
                >
                <svg
                    class="h-3 w-3 text-muted-foreground transition-transform duration-200"
                    :class="{ 'rotate-180': showDropdown }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </Button>

            <!-- Dropdown Menu -->
            <div
                v-if="showDropdown"
                :class="[
                    'absolute top-full z-50 mt-2 w-48 overflow-hidden rounded-lg border border-border bg-background shadow-lg',
                    isRTL ? 'left-0' : 'right-0',
                ]"
            >
                <div class="py-1">
                    <button
                        v-for="language in languages"
                        :key="language.code"
                        @click="changeLanguage(language.code)"
                        :class="[
                            'flex w-full items-center space-x-3 px-4 py-3 text-left transition-colors duration-150 hover:bg-muted/50',
                            currentLocale === language.code
                                ? 'bg-primary/10 text-primary'
                                : 'text-foreground',
                        ]"
                    >
                        <span class="text-lg">{{ language.flag }}</span>
                        <div class="flex-1">
                            <div class="font-medium">{{ language.name }}</div>
                            <div class="text-xs text-muted-foreground">
                                {{ language.code.toUpperCase() }}
                            </div>
                        </div>
                        <svg
                            v-if="currentLocale === language.code"
                            class="h-4 w-4 text-primary"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <Select
            v-else
            :model-value="currentLocale"
            @update:model-value="changeLanguage"
            :disabled="isLoading"
        >
            <SelectTrigger class="w-full">
                <div class="flex items-center space-x-2">
                    <Globe class="h-4 w-4" />
                    <SelectValue>
                        <div class="flex items-center space-x-2">
                            <span>{{ currentLanguage.flag }}</span>
                            <span>{{ currentLanguage.name }}</span>
                        </div>
                    </SelectValue>
                </div>
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="language in languages"
                    :key="language.code"
                    :value="language.code"
                >
                    <div class="flex items-center space-x-2">
                        <span>{{ language.flag }}</span>
                        <span>{{ language.name }}</span>
                    </div>
                </SelectItem>
            </SelectContent>
        </Select>
    </div>
</template>
