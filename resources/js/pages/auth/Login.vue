<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import ModalAlert from '@/components/ModalAlert.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

// Get flash messages from Inertia page props
const page = usePage();
const flash = computed(() => (page.props.flash as any) || {});

// Client-side validation
const clientErrors = ref<Record<string, string>>({});

const validateForm = (formData: any) => {
    const errors: Record<string, string> = {};

    // Email or username validation
    const emailOrUsername = formData.email?.trim() || '';
    if (!emailOrUsername) {
        errors.email = 'Email address or username is required.';
    }

    // Password validation
    if (!formData.password || formData.password.length < 1) {
        errors.password = 'Password is required.';
    }

    return errors;
};

const handleSubmit = (event: Event) => {
    // Clear previous errors
    clientErrors.value = {};

    // Get form data from the form state
    const formData = {
        email:
            (document.querySelector('input[name="email"]') as HTMLInputElement)
                ?.value || '',
        password:
            (
                document.querySelector(
                    'input[name="password"]',
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

const handleGoogleLogin = () => {
    window.location.href = '/auth/google/login';
};
</script>

<template>
    <AuthBase
        title="Log in to your account"
        description="Enter your credentials below to log in"
    >
        <Head title="Log in" />

        <ModalAlert v-if="status" type="success" :message="status" />

        <!-- Error Messages -->
        <ModalAlert v-if="flash.error" type="error" :message="flash.error" />

        <Form
            v-bind="AuthenticatedSessionController.store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email address or username</Label>
                    <Input
                        id="email"
                        type="text"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="Enter your email or username"
                    />
                    <InputError :message="clientErrors.email || errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm"
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    />
                    <InputError
                        :message="clientErrors.password || errors.password"
                    />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                    @click="handleSubmit"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Log in
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
                @click="handleGoogleLogin"
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
                Continue with Google
            </Button>

            <div class="text-center text-sm text-muted-foreground">
                Don't have an account?
                <TextLink :href="register()" :tabindex="5">Sign up</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
