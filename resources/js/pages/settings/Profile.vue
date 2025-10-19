<script setup lang="ts">
import { edit } from '@/routes/profile';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useTranslations } from '@/composables/useTranslations';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

interface Props {
    user: {
        username: string;
        name: string;
        bio: string | null;
        website_url: string | null;
        avatar: string | null;
        lifetime_points: number;
        week_starts_on: number;
    };
}

const props = defineProps<Props>();

// Use translations with reactive locale
const { t, isRTL } = useTranslations();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('profile.title'),
        href: edit().url,
    },
];

// Form data - initialize with user data
const username = ref(props.user.username || '');
const name = ref(props.user.name || '');
const bio = ref(props.user.bio || '');
const websiteUrl = ref(props.user.website_url || '');
const avatar = ref(props.user.avatar || '');
const weekStartsOn = ref(props.user.week_starts_on || 6);

// Computed property to show the selected day name
const selectedDayName = computed(() => {
    const dayNames: Record<number, string> = {
        0: t('profile.sunday'),
        1: t('profile.monday'),
        6: t('profile.saturday'),
    };
    return dayNames[weekStartsOn.value] || t('profile.sunday');
});

// Bio character counter
const bioCharCount = ref(bio.value.length);

// Watch bio changes to update character count
watch(bio, (newBio) => {
    bioCharCount.value = newBio.length;
});

// Client-side validation
const clientErrors = ref<Record<string, string>>({});
const recentlySuccessful = ref(false);

const validateForm = (formData: any) => {
    const errors: Record<string, string> = {};

    // Username validation
    const username = formData.username?.trim() || '';
    if (!username || username.length < 3) {
        errors.username = t('validation.username.min');
    } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        errors.username = t('validation.username.regex');
    }

    // Name validation
    const name = formData.name?.trim() || '';
    if (!name || name.length < 2) {
        errors.name = t('validation.name.min');
    } else if (name.length > 100) {
        errors.name = t('validation.name.max');
    }

    // Bio validation (if provided)
    const bio = formData.bio?.trim() || '';
    if (bio && bio.length > 255) {
        errors.bio = t('validation.bio.max');
    }

    // Website URL validation (if provided)
    const websiteUrl = formData.website_url?.trim() || '';
    if (websiteUrl) {
        try {
            new URL(websiteUrl);
        } catch {
            errors.website_url = t('validation.website_url.url');
        }
    }

    // Avatar URL validation (if provided)
    const avatar = formData.avatar?.trim() || '';
    if (avatar) {
        try {
            new URL(avatar);
        } catch {
            errors.avatar = t('validation.avatar.url');
        }
    }

    return errors;
};

const handleSubmit = (event: Event) => {
    // Clear previous errors
    clientErrors.value = {};

    // Get form data from reactive variables
    const formData = {
        username: username.value,
        name: name.value,
        bio: bio.value,
        website_url: websiteUrl.value,
        avatar: avatar.value,
        week_starts_on: weekStartsOn.value,
    };

    // Validate form data
    const errors = validateForm(formData);

    if (Object.keys(errors).length > 0) {
        clientErrors.value = errors;
        event.preventDefault(); // Prevent form submission
        return false;
    }

    // If validation passes, submit the form using Inertia
    router.patch('/settings/profile', formData, {
        preserveScroll: true,
        onSuccess: () => {
            // Form submitted successfully
            recentlySuccessful.value = true;
            setTimeout(() => {
                recentlySuccessful.value = false;
            }, 3000); // Hide after 3 seconds
        },
        onError: () => {
            // Handle server-side errors if needed
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems" :dir="isRTL ? 'rtl' : 'ltr'">
        <Head :title="t('profile.title')" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    :title="t('profile.title')"
                    :description="t('profile.description')"
                />

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Username -->
                    <div class="grid gap-2">
                        <Label for="username">{{
                            t('profile.username')
                        }}</Label>
                        <Input
                            id="username"
                            v-model="username"
                            class="mt-1 block w-full"
                            name="username"
                            required
                            autocomplete="username"
                            :placeholder="t('profile.choose_username_placeholder')"
                        />
                        <InputError
                            class="mt-2"
                            :message="clientErrors.username"
                        />
                    </div>

                    <!-- Name -->
                    <div class="grid gap-2">
                        <Label for="name">{{ t('profile.name') }}</Label>
                        <Input
                            id="name"
                            v-model="name"
                            class="mt-1 block w-full"
                            name="name"
                            required
                            autocomplete="name"
                            :placeholder="t('profile.enter_name_placeholder')"
                            maxlength="100"
                        />
                        <InputError class="mt-2" :message="clientErrors.name" />
                    </div>

                    <!-- Bio -->
                    <div class="grid gap-2">
                        <Label for="bio">{{ t('profile.bio') }}</Label>
                        <Textarea
                            id="bio"
                            v-model="bio"
                            class="mt-1 block w-full"
                            name="bio"
                            @input="bioCharCount = bio.length"
                            :placeholder="t('profile.bio_placeholder')"
                            :rows="4"
                            maxlength="255"
                        />
                        <p
                            v-if="bioCharCount >= 200 || bioCharCount > 255"
                            class="text-xs"
                            :class="
                                bioCharCount > 255
                                    ? 'text-destructive'
                                    : 'text-muted-foreground'
                            "
                        >
                            {{ bioCharCount }}/255 {{ t('profile.characters_count') }}
                        </p>
                        <InputError class="mt-2" :message="clientErrors.bio" />
                    </div>

                    <!-- Website URL -->
                    <div class="grid gap-2">
                        <Label for="website_url">{{
                            t('profile.website')
                        }}</Label>
                        <Input
                            id="website_url"
                            v-model="websiteUrl"
                            type="url"
                            class="mt-1 block w-full"
                            name="website_url"
                            :placeholder="t('profile.website_placeholder')"
                        />
                        <InputError
                            class="mt-2"
                            :message="clientErrors.website_url"
                        />
                    </div>

                    <!-- Week Start Setting -->
                    <div class="grid gap-2">
                        <Label for="week_starts_on">{{
                            t('profile.week_starts_on')
                        }}</Label>
                        <Select v-model="weekStartsOn">
                            <SelectTrigger>
                                <span>{{ selectedDayName }}</span>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="6">{{ t('profile.saturday') }}</SelectItem>
                                <SelectItem :value="0">{{ t('profile.sunday') }}</SelectItem>
                                <SelectItem :value="1">{{ t('profile.monday') }}</SelectItem>
                            </SelectContent>
                        </Select>
                        <p class="text-xs text-muted-foreground">
                            {{ t('profile.heatmap_description') }}
                        </p>
                        <InputError
                            class="mt-2"
                            :message="clientErrors.week_starts_on"
                        />
                    </div>

                    <!-- Avatar URL -->
                    <div class="grid gap-2">
                        <Label for="avatar">{{ t('profile.profile_picture_url') }}</Label>
                        <Input
                            id="avatar"
                            v-model="avatar"
                            type="url"
                            class="mt-1 block w-full"
                            name="avatar"
                            :placeholder="t('profile.avatar_placeholder')"
                        />
                        <p class="text-xs text-muted-foreground">
                            {{ t('profile.avatar_help_text') }}
                        </p>
                        <InputError
                            class="mt-2"
                            :message="clientErrors.avatar"
                        />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            type="submit"
                            data-test="update-profile-button"
                            @click="handleSubmit"
                            >{{ t('profile.save') }}</Button
                        >

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-muted-foreground"
                            >
                                {{ t('profile.saved') }}
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>

            <!-- <DeleteUser /> -->
        </SettingsLayout>
    </AppLayout>
</template>
