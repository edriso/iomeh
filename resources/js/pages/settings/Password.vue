<script setup lang="ts">
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useTranslations } from '@/composables/useTranslations';
import { type BreadcrumbItem } from '@/types';

const { t, isRTL } = useTranslations();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('settings.password'),
        href: edit().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems" :dir="isRTL ? 'rtl' : 'ltr'">
        <Head :title="t('settings.password')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="t('password.update_password')"
                    :description="t('password.update_description')"
                />

                <Form
                    v-bind="PasswordController.update.form()"
                    :options="{
                        preserveScroll: true,
                    }"
                    reset-on-success
                    :reset-on-error="[
                        'password',
                        'password_confirmation',
                        'current_password',
                    ]"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <!-- Hidden username field for accessibility -->
                    <input
                        type="text"
                        name="username"
                        autocomplete="username"
                        class="hidden"
                        tabindex="-1"
                    />

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

                    <div class="grid gap-2">
                        <Label for="password">{{
                            t('password.new_password')
                        }}</Label>
                        <PasswordInput
                            id="password"
                            name="password"
                            :placeholder="t('password.create_new_password')"
                            autocomplete="new-password"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">{{
                            t('password.confirm_password')
                        }}</Label>
                        <PasswordInput
                            id="password_confirmation"
                            name="password_confirmation"
                            :placeholder="t('password.confirm_new_password')"
                            autocomplete="new-password"
                        />
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-password-button"
                            >{{ t('password.save_password') }}</Button
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
                                {{ t('password.saved') }}
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
