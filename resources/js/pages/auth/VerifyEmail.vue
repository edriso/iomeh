<script setup lang="ts">
import EmailVerificationNotificationController from '@/actions/App/Http/Controllers/Auth/EmailVerificationNotificationController';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes/index';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        title="Verify Your Email Address"
        description="We've sent a verification link to your email address. Please check your inbox and click the link to complete your account setup. This might take a minute."
    >
        <Head title="Email verification" />

        <div
            v-if="status === 'verification-link-sent'"
            class="text-primary-text mb-4 text-center text-sm"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <Form
            v-bind="EmailVerificationNotificationController.store.form()"
            class="space-y-6 text-center"
            v-slot="{ processing }"
        >
            <Button :disabled="processing" variant="secondary" class="w-full">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                Resend verification email
            </Button>

            <TextLink
                :href="logout()"
                as="button"
                class="mx-auto block cursor-pointer text-sm"
            >
                Log out
            </TextLink>
        </Form>
    </AuthLayout>
</template>
