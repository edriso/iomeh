<script setup lang="ts">
import EmailController from '@/actions/App/Http/Controllers/Settings/EmailController';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/email';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useTranslations } from '@/composables/useTranslations';
import { type BreadcrumbItem } from '@/types';

const { t, isRTL } = useTranslations();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('settings.email'),
        href: edit().url,
    },
];

const emailInput = ref<HTMLInputElement | null>(null);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems" :dir="isRTL ? 'rtl' : 'ltr'">
        <Head :title="t('settings.email')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="t('email.update_email')"
                    :description="t('email.update_description')"
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
                        <Label for="email">{{ t('email.new_email') }}</Label>
                        <Input
                            id="email"
                            ref="emailInput"
                            name="email"
                            type="email"
                            class="mt-1 block w-full"
                            autocomplete="email"
                            :placeholder="t('email.enter_new_email')"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email_confirmation">{{
                            t('email.confirm_email')
                        }}</Label>
                        <Input
                            id="email_confirmation"
                            name="email_confirmation"
                            type="email"
                            class="mt-1 block w-full"
                            autocomplete="email"
                            :placeholder="t('email.confirm_new_email')"
                        />
                        <InputError :message="errors.email_confirmation" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="current_password">{{
                            t('password.current_password')
                        }}</Label>
                        <PasswordInput
                            id="current_password"
                            name="current_password"
                            :placeholder="t('password.enter_current_password')"
                            autocomplete="current-password"
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
                            >{{ t('email.save_email') }}</Button
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
                                {{ t('email.saved') }}
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
