<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useTranslations } from '@/composables/useTranslations';
import type { User } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ChevronDown, Crown, Home, LogOut, Settings } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user as User | undefined);

// Get current locale from shared Inertia data and initialize
const initialLocale = (page.props.currentLocale as string) || 'en';
const { t } = useTranslations(initialLocale);

const appName = import.meta.env.VITE_APP_NAME || 'IOMeH';

const navLinks = computed(() => [
    {
        name: t('nav.home'),
        href: '/',
        icon: Home,
    },
    {
        name: t('nav.rankings'),
        href: '/rankings',
        icon: Crown,
    },
]);

// Handle scroll to top
const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Handle logout
const handleLogout = () => {
    router.post('/logout');
};

// Check if we should show scroll button instead of link for nav items
const shouldShowNavButton = (href: string) => {
    const currentPath = page.url;
    let targetPath = href;

    if (href === '/') {
        targetPath = '/';
    }

    return currentPath === targetPath;
};
</script>

<template>
    <header
        class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
    >
        <div
            class="container mx-auto flex h-16 items-center justify-between gap-x-2 px-4"
        >
            <!-- Left: Logo and App Name -->
            <div class="flex items-center gap-2">
                <template v-if="shouldShowNavButton('/')">
                    <!-- Show button when on home page -->
                    <Button
                        variant="ghost"
                        @click="scrollToTop"
                        class="flex h-auto items-center gap-2 p-2 font-heading hover:bg-transparent"
                    >
                        <AppLogo />
                        <span class="text-xl font-medium text-foreground">{{
                            appName
                        }}</span>
                    </Button>
                </template>
                <template v-else>
                    <!-- Show link when not on home page -->
                    <Link
                        href="/"
                        class="flex h-auto items-center gap-2 p-0 font-heading"
                        title="Go to home"
                    >
                        <AppLogo />
                        <span class="text-xl font-medium text-foreground">{{
                            appName
                        }}</span>
                    </Link>
                </template>
            </div>

            <!-- Middle: Navigation Links -->
            <nav class="hidden items-center gap-1 md:flex">
                <template v-for="link in navLinks" :key="link.href">
                    <!-- Show button when on same page -->
                    <Button
                        v-if="shouldShowNavButton(link.href)"
                        variant="ghost"
                        @click="scrollToTop"
                        class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors"
                    >
                        <component :is="link.icon" class="h-4 w-4" />
                        {{ link.name }}
                    </Button>
                    <!-- Show link when on different page -->
                    <Link
                        v-else
                        :href="link.href"
                        class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-primary hover:text-primary-foreground"
                        :title="`Go to ${link.name}`"
                    >
                        <component :is="link.icon" class="h-4 w-4" />
                        {{ link.name }}
                    </Link>
                </template>
            </nav>

            <!-- Right: User Avatar Dropdown -->
            <div v-if="user" class="flex items-center gap-2">
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button
                            variant="ghost"
                            class="relative flex h-10 items-center gap-2 rounded-full px-2 hover:bg-primary/10 dark:hover:bg-primary/10"
                        >
                            <Avatar class="h-8 w-8 border-2 border-primary/20">
                                <AvatarImage
                                    v-if="user.avatar"
                                    :src="user.avatar"
                                    :alt="user.name || user.username"
                                />
                                <AvatarFallback
                                    class="bg-primary text-primary-foreground"
                                >
                                    {{
                                        (user.name || user.username || 'U')
                                            .charAt(0)
                                            .toUpperCase()
                                    }}
                                </AvatarFallback>
                            </Avatar>
                            <ChevronDown
                                class="h-4 w-4 text-muted-foreground"
                            />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <DropdownMenuItem as-child>
                            <Link
                                :href="`/profile/${user.username}`"
                                class="flex w-full cursor-pointer items-center gap-2 px-2 py-1.5 hover:[&_span]:text-primary-foreground"
                            >
                                <Avatar class="h-6 w-6">
                                    <AvatarImage
                                        v-if="user.avatar"
                                        :src="user.avatar"
                                        :alt="user.name || user.username"
                                    />
                                    <AvatarFallback
                                        class="border border-primary-foreground bg-background text-xs dark:border-border dark:bg-foreground dark:text-primary-foreground"
                                    >
                                        {{
                                            (user.name || user.username || 'U')
                                                .charAt(0)
                                                .toUpperCase()
                                        }}
                                    </AvatarFallback>
                                </Avatar>
                                <div class="flex max-w-full min-w-0 flex-col">
                                    <span
                                        class="truncate text-sm font-medium text-ellipsis"
                                        >{{ user.name || user.username }}</span
                                    >
                                    <span
                                        class="text-xs text-muted-foreground"
                                        >{{ t('nav.view_profile') }}</span
                                    >
                                </div>
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem as-child>
                            <Link
                                href="/settings/profile"
                                class="flex w-full cursor-pointer items-center gap-2 px-2 py-1.5 hover:[&_svg]:!text-primary-foreground"
                            >
                                <Settings class="h-4 w-4" />
                                <span>{{ t('nav.settings') }}</span>
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            @click="handleLogout"
                            class="flex w-full cursor-pointer items-center gap-2 px-2 py-1.5 hover:[&_svg]:!text-primary-foreground"
                        >
                            <LogOut class="h-4 w-4" />
                            <span>{{ t('nav.logout') }}</span>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <!-- Right: Auth Links (for guests) -->
            <div v-else class="flex items-center gap-2">
                <Link href="/login">
                    <Button variant="ghost">{{ t('nav.login') }}</Button>
                </Link>
                <Link href="/register">
                    <Button variant="default">{{ t('nav.register') }}</Button>
                </Link>
            </div>
        </div>

        <!-- Mobile Navigation (Bottom) -->
        <div class="border-t bg-background md:hidden">
            <nav class="flex items-center justify-around px-2 py-2">
                <template v-for="link in navLinks" :key="link.href">
                    <!-- Show button when on same page -->
                    <Button
                        v-if="shouldShowNavButton(link.href)"
                        variant="ghost"
                        @click="scrollToTop"
                        class="flex h-auto min-w-[60px] flex-col items-center gap-1 rounded-md bg-primary px-3 py-2 text-xs font-medium text-primary-foreground transition-colors [&_svg]:!size-5"
                    >
                        <component
                            :is="link.icon"
                            class="size-5 flex-shrink-0"
                        />
                        <span>{{ link.name }}</span>
                    </Button>
                    <!-- Show link when on different page -->
                    <Link
                        v-else
                        :href="link.href"
                        class="flex min-w-[60px] flex-col items-center gap-1 rounded-md px-3 py-2 text-xs font-medium text-muted-foreground transition-colors hover:bg-primary hover:text-primary-foreground"
                        :title="`Go to ${link.name}`"
                    >
                        <component
                            :is="link.icon"
                            class="size-5 flex-shrink-0"
                        />
                        <span>{{ link.name }}</span>
                    </Link>
                </template>
            </nav>
        </div>
    </header>
</template>
