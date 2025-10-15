<script setup lang="ts">
import EmailController from '@/actions/App/Http/Controllers/Settings/EmailController';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/email';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Email settings',
        href: edit().url,
    },
];

const emailInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Email settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Update email address"
                    description="Change your email address. You'll need to verify the new email address."
                />

                <Form
                    v-bind="EmailController.update.form()"
                    :options="{
                        preserveScroll: true,
                    }"
                    reset-on-success
                    :reset-on-error="[
                        'email',
                        'email_confirmation',
                        'current_password',
                    ]"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="email">New email address</Label>
                        <Input
                            id="email"
                            ref="emailInput"
                            name="email"
                            type="email"
                            class="mt-1 block w-full"
                            autocomplete="email"
                            placeholder="Enter new email address"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email_confirmation"
                            >Confirm email address</Label
                        >
                        <Input
                            id="email_confirmation"
                            name="email_confirmation"
                            type="email"
                            class="mt-1 block w-full"
                            autocomplete="email"
                            placeholder="Confirm new email address"
                        />
                        <InputError :message="errors.email_confirmation" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="current_password">Current password</Label>
                        <Input
                            id="current_password"
                            ref="currentPasswordInput"
                            name="current_password"
                            type="password"
                            class="mt-1 block w-full"
                            autocomplete="current-password"
                            placeholder="Enter current password"
                        />
                        <InputError :message="errors.current_password" />
                    </div>

                    <!-- Hidden username field for accessibility -->
                    <input
                        type="text"
                        name="username"
                        autocomplete="username"
                        class="hidden"
                        tabindex="-1"
                    />

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-email-button"
                            >Save email</Button
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
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
