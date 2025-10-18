<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes/index';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

// Timezone detection
const detectedTimezone = ref('UTC');

// Client-side validation
const clientErrors = ref<Record<string, string>>({});

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

    // Email validation
    const email = formData.email?.trim() || '';
    if (!email) {
        errors.email = 'Email address is required.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.email = 'Please enter a valid email address.';
    }

    // Password validation
    if (!formData.password || formData.password.length < 8) {
        errors.password = 'Password must be at least 8 characters long.';
    }

    // Password confirmation validation
    if (!formData.password_confirmation) {
        errors.password_confirmation = 'Please confirm your password.';
    } else if (formData.password !== formData.password_confirmation) {
        errors.password_confirmation = 'Passwords do not match.';
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

    return errors;
};

const handleSubmit = (event: Event) => {
    // Clear previous errors
    clientErrors.value = {};

    // Get form data from the form state
    const formData = {
        username:
            (
                document.querySelector(
                    'input[name="username"]',
                ) as HTMLInputElement
            )?.value || '',
        name:
            (
                document.querySelector(
                    'input[name="name"]',
                ) as HTMLInputElement
            )?.value || '',
        email:
            (document.querySelector('input[name="email"]') as HTMLInputElement)
                ?.value || '',
        password:
            (
                document.querySelector(
                    'input[name="password"]',
                ) as HTMLInputElement
            )?.value || '',
        password_confirmation:
            (
                document.querySelector(
                    'input[name="password_confirmation"]',
                ) as HTMLInputElement
            )?.value || '',
        bio:
            (
                document.querySelector(
                    'textarea[name="bio"]',
                ) as HTMLTextAreaElement
            )?.value || '',
        website_url:
            (
                document.querySelector(
                    'input[name="website_url"]',
                ) as HTMLInputElement
            )?.value || '',
    };

    // Validate form data
    const errors = validateForm(formData);

    if (Object.keys(errors).length > 0) {
        clientErrors.value = errors;
        event.preventDefault(); // Prevent form submission
        return false;
    }

    // If validation passes, allow form submission
    return true;
};

const handleGoogleSignup = () => {
    window.location.href = '/auth/google/register';
};

onMounted(() => {
    // Auto-detect user's timezone
    try {
        const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        detectedTimezone.value = userTimezone;
    } catch {
        detectedTimezone.value = 'UTC';
    }
});
</script>

<template>
    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account"
    >
        <Head title="Register" />

        <Form
            v-bind="RegisteredUserController.store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <!-- Hidden timezone field -->
            <input type="hidden" name="timezone" :value="detectedTimezone" />
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="username">Username</Label>
                    <Input
                        id="username"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="username"
                        name="username"
                        placeholder="Choose a username"
                    />
                    <InputError
                        :message="clientErrors.username || errors.username"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        :tabindex="2"
                        autocomplete="name"
                        name="name"
                        placeholder="Enter your name"
                        maxlength="100"
                    />
                    <InputError
                        :message="clientErrors.name || errors.name"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="3"
                        autocomplete="email"
                        name="email"
                        placeholder="Enter your email"
                    />
                    <InputError :message="clientErrors.email || errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Create a password"
                    />
                    <InputError
                        :message="clientErrors.password || errors.password"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="5"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm your password"
                    />
                    <InputError
                        :message="
                            clientErrors.password_confirmation ||
                            errors.password_confirmation
                        "
                    />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="5"
                    :disabled="processing"
                    data-test="register-user-button"
                    @click="handleSubmit"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Create account
                </Button>
            </div>

            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t" />
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-background px-2 text-muted-foreground">
                        Or continue with
                    </span>
                </div>
            </div>

            <Button
                variant="outline"
                type="button"
                class="w-full"
                @click="handleGoogleSignup"
            >
                <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24">
                    <path
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                        fill="#4285F4"
                    />
                    <path
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                        fill="#34A853"
                    />
                    <path
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                        fill="#FBBC05"
                    />
                    <path
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                        fill="#EA4335"
                    />
                </svg>
                Sign up with Google
            </Button>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="6"
                    >Log in</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
