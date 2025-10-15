<script setup lang="ts">
import { edit } from '@/routes/profile';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useNumberFormat } from '@/composables/useNumberFormat';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { Info } from 'lucide-vue-next';

interface Props {
    user: {
        username: string;
        name: string;
        bio: string | null;
        website_url: string | null;
        avatar: string | null;
        lifetime_points: number;
    };
    profile_picture_points_required: number;
}

const props = defineProps<Props>();
const { formatNumber } = useNumberFormat();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
];

const page = usePage();
const authUser = page.props.auth.user;

// Form data - initialize with user data
const username = ref(props.user.username || '');
const name = ref(props.user.name || '');
const bio = ref(props.user.bio || '');
const websiteUrl = ref(props.user.website_url || '');
const avatar = ref(props.user.avatar || '');

// Bio character counter
const bioCharCount = ref(bio.value.length);

// Watch bio changes to update character count
watch(bio, (newBio) => {
    bioCharCount.value = newBio.length;
});

// Point thresholds - get from backend
const PROFILE_PICTURE_POINTS = props.profile_picture_points_required || 50;

const canUploadAvatar = computed(
    () => (authUser.lifetime_points || 0) >= PROFILE_PICTURE_POINTS,
);

// Client-side validation
const clientErrors = ref<Record<string, string>>({});
const recentlySuccessful = ref(false);

const validateForm = (formData: any) => {
    const errors: Record<string, string> = {};

    // Username validation
    const username = formData.username?.trim() || '';
    if (!username || username.length < 3) {
        errors.username = 'Username must be at least 3 characters long.';
    } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        errors.username =
            'Username can only contain letters, numbers, and underscores.';
    }

    // Name validation
    const name = formData.name?.trim() || '';
    if (!name || name.length < 2) {
        errors.name = 'Name must be at least 2 characters long.';
    } else if (name.length > 100) {
        errors.name = 'Name must not exceed 100 characters.';
    }

    // Bio validation (if provided)
    const bio = formData.bio?.trim() || '';
    if (bio && bio.length > 255) {
        errors.bio = 'Bio must be 255 characters or less.';
    }

    // Website URL validation (if provided)
    const websiteUrl = formData.website_url?.trim() || '';
    if (websiteUrl) {
        try {
            new URL(websiteUrl);
        } catch {
            errors.website_url =
                'Please enter a valid URL (e.g., https://example.com).';
        }
    }

    // Avatar URL validation (if provided)
    const avatar = formData.avatar?.trim() || '';
    if (avatar) {
        try {
            new URL(avatar);
        } catch {
            errors.avatar =
                'Please enter a valid URL (e.g., https://example.com/image.jpg).';
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
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Profile information"
                    description="Update your profile details and settings"
                />

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Username -->
                    <div class="grid gap-2">
                        <Label for="username">Username</Label>
                        <Input
                            id="username"
                            v-model="username"
                            class="mt-1 block w-full"
                            name="username"
                            required
                            autocomplete="username"
                            placeholder="Choose your username"
                        />
                        <InputError
                            class="mt-2"
                            :message="clientErrors.username"
                        />
                    </div>

                    <!-- Name -->
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            v-model="name"
                            class="mt-1 block w-full"
                            name="name"
                            required
                            autocomplete="name"
                            placeholder="Enter your name"
                            maxlength="100"
                        />
                        <InputError class="mt-2" :message="clientErrors.name" />
                    </div>

                    <!-- Bio -->
                    <div class="grid gap-2">
                        <Label for="bio">Bio</Label>
                        <Textarea
                            id="bio"
                            v-model="bio"
                            class="mt-1 block w-full"
                            name="bio"
                            @input="bioCharCount = bio.length"
                            placeholder="Share a little about yourself and your interests..."
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
                            {{ bioCharCount }}/255 characters
                        </p>
                        <InputError class="mt-2" :message="clientErrors.bio" />
                    </div>

                    <!-- Website URL -->
                    <div class="grid gap-2">
                        <Label for="website_url">Personal Website</Label>
                        <Input
                            id="website_url"
                            v-model="websiteUrl"
                            type="url"
                            class="mt-1 block w-full"
                            name="website_url"
                            placeholder="Enter your website URL"
                        />
                        <InputError
                            class="mt-2"
                            :message="clientErrors.website_url"
                        />
                    </div>

                    <!-- Avatar URL -->
                    <div class="grid gap-2">
                        <Label for="avatar">Profile Picture URL</Label>
                        <Input
                            id="avatar"
                            v-model="avatar"
                            type="url"
                            class="mt-1 block w-full"
                            name="avatar"
                            :disabled="!canUploadAvatar"
                            placeholder="Enter URL for your profile picture"
                        />
                        <Alert v-if="!canUploadAvatar" variant="destructive">
                            <Info class="h-4 w-4" />
                            <AlertDescription>
                                You need at least
                                {{ formatNumber(PROFILE_PICTURE_POINTS) }} coins
                                to set a profile picture. You currently have
                                {{ formatNumber(user.lifetime_points) }} points.
                            </AlertDescription>
                        </Alert>
                        <p v-else class="text-xs text-muted-foreground">
                            Enter a direct URL to your profile picture
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
                            >Save</Button
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
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>

            <!-- <DeleteUser /> -->
        </SettingsLayout>
    </AppLayout>
</template>
