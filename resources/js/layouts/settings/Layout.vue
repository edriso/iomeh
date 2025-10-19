<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useTranslations } from '@/composables/useTranslations';
import { toUrl, urlIsActive } from '@/lib/utils';
import { edit as editAccount } from '@/routes/account';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editEmail } from '@/routes/email';
import { edit as editHabits } from '@/routes/habits';
import { edit as editPassword } from '@/routes/password';
import { edit as editProfile } from '@/routes/profile';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t, isRTL } = useTranslations();

const sidebarNavItems = computed(() => [
    {
        title: t('settings.profile'),
        href: editProfile(),
    },
    {
        title: t('settings.habits'),
        href: editHabits(),
    },
    {
        title: t('settings.password'),
        href: editPassword(),
    },
    {
        title: t('settings.email'),
        href: editEmail(),
    },
    {
        title: t('settings.appearance'),
        href: editAppearance(),
    },
    {
        title: t('settings.account'),
        href: editAccount(),
    },
]);

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6" :dir="isRTL ? 'rtl' : 'ltr'">
        <Heading
            :title="t('settings.title')"
            :description="t('settings.description')"
        />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="[
                            'w-full justify-start',
                            { 'bg-muted': urlIsActive(item.href, currentPath) },
                        ]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
