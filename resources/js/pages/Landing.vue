<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import SEO from '@/components/SEO.vue';
import { Button } from '@/components/ui/button';
import { useTranslations } from '@/composables/useTranslations';
import { STREAK_TIERS } from '@/utils/streakTiers';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Activity,
    Apple,
    Award,
    CalendarCheck,
    Flame,
    Footprints,
    Heart,
    TrendingUp,
    Trophy,
} from 'lucide-vue-next';

interface Props {
    stats?: {
        active_users: number;
        total_activities: number;
        combined_streak: number;
    };
}

withDefaults(defineProps<Props>(), {
    stats: () => ({
        active_users: 0,
        total_activities: 0,
        combined_streak: 0,
    }),
});

// Get current locale from shared Inertia data and initialize
const page = usePage();
const initialLocale = (page.props.currentLocale as string) || 'en';

// Use translations with reactive locale
const { t, isRTL } = useTranslations(initialLocale);

// Get app name from environment variable
const appName = import.meta.env.VITE_APP_NAME || 'IOMeH';

// Get current year dynamically
const currentYear = new Date().getFullYear();

// Simple number formatting function
const formatNumber = (num: number) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
};

// SEO Data
// Note: The SEO component automatically appends " | IOMeH" to the title
const seoData = {
    title: 'Global Fitness Ranking Platform',
    description:
        'IOMeH (I Owe Me Health) is your personal fitness ranking platform. Track workouts and nutrition activities while competing on global rankings. Turn fitness into competition.',
    keywords: [
        'fitness tracking',
        'workout ranking',
        'fitness competition',
        'fitness gamification',
        'activity tracking',
        'fitness leaderboard',
        'workout points',
        'nutrition tracking',
        'fitness platform',
        'fitness motivation',
        'gym tracking',
        'exercise tracking',
    ],
    url: '/',
    image: '/iomeh.png',
    imageAlt: 'IOMeH - I Owe Me Health',
    type: 'website' as const,
};
</script>

<template>
    <SEO
        :title="seoData.title"
        :description="seoData.description"
        :keywords="seoData.keywords"
        :url="seoData.url"
        :image="seoData.image"
        :image-alt="seoData.imageAlt"
        :type="seoData.type"
    />

    <div
        class="min-h-screen bg-gradient-to-br from-primary/5 via-background to-emerald-500/5"
        :dir="isRTL ? 'rtl' : 'ltr'"
    >
        <!-- Navigation -->
        <nav
            class="sticky top-0 z-50 border-b border-border/40 bg-background/95 backdrop-blur-sm"
        >
            <div class="container mx-auto px-2 py-2 sm:px-4 sm:py-3 md:py-4">
                <div class="flex items-center justify-between gap-1 sm:gap-2">
                    <!-- Logo and App Name -->
                    <div
                        class="flex min-w-0 flex-shrink-0 items-center gap-1 sm:gap-2 md:gap-3"
                    >
                        <div class="flex-shrink-0">
                            <AppLogo />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1
                                class="truncate text-xs font-bold text-foreground sm:text-sm md:text-base lg:text-lg xl:text-2xl"
                            >
                                {{ appName }}
                            </h1>
                            <p
                                class="hidden text-xs text-muted-foreground sm:block sm:text-sm"
                            >
                                I Owe Me Health
                            </p>
                        </div>
                    </div>

                    <!-- Right Side Actions -->
                    <div
                        class="flex flex-shrink-0 items-center gap-0.5 sm:gap-1 md:gap-2 lg:gap-3"
                    >
                        <!-- Language Switcher - Compact on mobile -->
                        <div class="flex-shrink-0 w-auto">
                            <div class="scale-70 sm:scale-80 md:scale-90 lg:scale-100">
                                <LanguageSwitcher
                                    :show-label="false"
                                    variant="single"
                                    class="[&_button]:px-1 [&_button]:py-1 sm:[&_button]:px-2 sm:[&_button]:py-2 md:[&_button]:px-3 md:[&_button]:py-2"
                                />
                            </div>
                        </div>

                        <!-- Login Button -->
                        <Link href="/login">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="h-7 px-1.5 text-xs sm:h-8 sm:px-2 sm:text-sm md:h-9 md:px-3"
                            >
                                <span class="hidden sm:inline">{{
                                    t('nav.login')
                                }}</span>
                                <span class="sm:hidden">{{ t('nav.login') }}</span>
                            </Button>
                        </Link>

                        <!-- Register Button -->
                        <Link href="/register">
                            <Button
                                size="sm"
                                class="h-7 px-1.5 text-xs sm:h-8 sm:px-2 sm:text-sm md:h-9 md:px-3"
                            >
                                <span class="hidden sm:inline">{{
                                    t('nav.register')
                                }}</span>
                                <span class="sm:hidden">{{ t('nav.register') }}</span>
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="container mx-auto px-4 py-12 sm:py-16 md:py-20">
            <div class="mx-auto max-w-4xl text-center">
                <div class="mb-6 sm:mb-8">
                    <div
                        class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-2 text-xs font-medium sm:px-4 sm:text-sm"
                    >
                        <Trophy class="h-3 w-3 sm:h-4 sm:w-4" />
                        <span>{{ t('landing.badge') }}</span>
                    </div>
                </div>

                <h1
                    class="mb-4 text-3xl font-bold text-foreground sm:mb-6 sm:text-4xl md:text-6xl lg:text-7xl"
                >
                    {{ t('landing.hero_title') }}
                </h1>

                <p
                    class="mx-auto mb-8 max-w-3xl text-lg text-muted-foreground sm:mb-10 sm:text-xl md:text-2xl"
                >
                    {{ t('landing.subtitle') }}
                </p>

                <div
                    class="flex flex-col items-center justify-center gap-3 sm:flex-row sm:gap-4"
                >
                    <Link href="/register">
                        <Button
                            size="lg"
                            class="w-full px-6 py-3 text-base sm:w-auto sm:px-8 sm:py-4 sm:text-lg"
                        >
                            <Award class="h-4 w-4 sm:h-5 sm:w-5" />
                            {{ t('landing.get_started') }}
                        </Button>
                    </Link>
                    <Link href="/login">
                        <Button
                            variant="outline"
                            size="lg"
                            class="w-full px-6 py-3 text-base sm:w-auto sm:px-8 sm:py-4 sm:text-lg"
                        >
                            {{ t('nav.login') }}
                        </Button>
                    </Link>
                </div>

                <!-- Achievement Preview -->
                <div class="mt-12 sm:mt-16">
                    <p class="mb-4 text-center text-sm text-muted-foreground">
                        {{ t('landing.progress_text') }}
                    </p>
                    <div
                        class="flex flex-wrap items-center justify-center gap-2 sm:gap-3"
                    >
                        <!-- Season Badge -->
                        <div
                            class="group relative overflow-hidden rounded-xl border border-primary/30 bg-gradient-to-br from-primary/10 to-primary/5 px-4 py-2 transition-all hover:scale-105 hover:border-primary/50 hover:shadow-lg sm:px-6 sm:py-3"
                        >
                            <div class="flex items-center gap-1 sm:gap-2">
                                <Award
                                    class="h-4 w-4 text-primary sm:h-5 sm:w-5"
                                />
                                <span
                                    class="text-sm font-bold text-foreground sm:text-base"
                                    >{{ t('badge.quarter_rank') }}</span
                                >
                            </div>
                        </div>

                        <!-- Year Badge -->
                        <div
                            class="group relative overflow-hidden rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 to-orange-500/5 px-4 py-2 transition-all hover:scale-105 hover:border-amber-500/50 hover:shadow-lg sm:px-6 sm:py-3"
                        >
                            <div class="flex items-center gap-1 sm:gap-2">
                                <Trophy
                                    class="h-4 w-4 text-amber-600 sm:h-5 sm:w-5 dark:text-amber-500"
                                />
                                <span
                                    class="text-sm font-bold text-foreground sm:text-base"
                                    >{{ t('badge.year_rank') }}</span
                                >
                            </div>
                        </div>

                        <!-- Streak Badge -->
                        <div
                            class="group relative overflow-hidden rounded-xl border border-orange-500/30 bg-gradient-to-br from-orange-500/10 to-red-500/5 px-4 py-2 transition-all hover:scale-105 hover:border-orange-500/50 hover:shadow-lg sm:px-6 sm:py-3"
                        >
                            <div class="flex items-center gap-1 sm:gap-2">
                                <Flame
                                    class="h-4 w-4 text-orange-500 sm:h-5 sm:w-5"
                                />
                                <span
                                    class="text-sm font-bold text-foreground sm:text-base"
                                    >{{ t('badge.streak') }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Two Pillars of Fitness Section -->
        <section class="container mx-auto px-4 py-12 sm:py-16">
            <div class="mx-auto max-w-5xl">
                <div class="mb-12 text-center sm:mb-16">
                    <h2
                        class="mb-4 text-2xl font-bold text-foreground sm:text-3xl md:text-4xl"
                    >
                        {{ t('landing.fitness_journey_title') }}
                    </h2>
                    <p
                        class="mx-auto max-w-2xl text-base text-muted-foreground sm:text-lg"
                    >
                        {{ t('landing.fitness_journey_description') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:gap-8 md:grid-cols-2">
                    <!-- Workout -->
                    <div
                        class="rounded-2xl border border-primary/20 bg-gradient-to-br from-primary/10 to-primary/5 p-6 sm:p-8"
                    >
                        <div
                            class="mb-4 flex items-center gap-3 sm:mb-6 sm:gap-4"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary sm:h-14 sm:w-14"
                            >
                                <Activity
                                    class="h-6 w-6 text-primary-foreground sm:h-7 sm:w-7"
                                />
                            </div>
                            <div>
                                <h3
                                    class="text-lg font-bold text-foreground sm:text-xl"
                                >
                                    {{ t('landing.workout_title') }}
                                </h3>
                                <p
                                    class="text-xs text-muted-foreground sm:text-sm"
                                >
                                    {{ t('landing.workout_stats') }}
                                </p>
                            </div>
                        </div>
                        <p class="text-sm text-muted-foreground sm:text-base">
                            {{ t('landing.workout_description') }}
                        </p>
                    </div>

                    <!-- Nutrition -->
                    <div
                        class="rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 p-6 sm:p-8"
                    >
                        <div
                            class="mb-4 flex items-center gap-3 sm:mb-6 sm:gap-4"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500 sm:h-14 sm:w-14"
                            >
                                <Apple
                                    class="h-6 w-6 text-white sm:h-7 sm:w-7"
                                />
                            </div>
                            <div>
                                <h3
                                    class="text-lg font-bold text-foreground sm:text-xl"
                                >
                                    {{ t('landing.nutrition_title') }}
                                </h3>
                                <p
                                    class="text-xs text-muted-foreground sm:text-sm"
                                >
                                    {{ t('landing.nutrition_stats') }}
                                </p>
                            </div>
                        </div>
                        <p class="text-sm text-muted-foreground sm:text-base">
                            {{ t('landing.nutrition_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Streak Bonus Section -->
        <section class="container mx-auto px-4 py-12 sm:py-16 md:py-20">
            <div class="mx-auto max-w-5xl">
                <div class="mb-12 text-center sm:mb-16">
                    <h2
                        class="mb-4 text-2xl font-bold text-foreground sm:text-3xl md:text-4xl"
                    >
                        {{ t('landing.streak_bonus_title') }}
                    </h2>
                    <p
                        class="mx-auto max-w-2xl text-base text-muted-foreground sm:text-lg"
                    >
                        {{ t('landing.streak_bonus_description') }}
                        {{
                            STREAK_TIERS[
                                STREAK_TIERS.length - 1
                            ].multiplier.toFixed(1)
                        }}×.
                    </p>
                </div>

                <!-- Streak Tiers Grid -->
                <div
                    class="mb-8 grid grid-cols-2 gap-3 sm:mb-12 sm:gap-4 md:grid-cols-4 lg:grid-cols-8"
                >
                    <!-- Newcomer -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-3 text-center transition-all hover:scale-105 hover:border-primary/50 sm:p-4"
                    >
                        <div class="mb-1 text-xl sm:mb-2 sm:text-2xl">🌱</div>
                        <h3
                            class="mb-1 text-xs font-bold text-foreground sm:text-sm"
                        >
                            {{ t('landing.tier_newcomer') }}
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            {{ t('landing.days_1_2') }}
                        </p>
                        <div
                            class="rounded-full bg-primary/10 px-2 py-1 text-xs font-bold text-primary"
                        >
                            1.0×
                        </div>
                    </div>

                    <!-- Beginner -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-3 text-center transition-all hover:scale-105 hover:border-primary/50 sm:p-4"
                    >
                        <div class="mb-1 text-xl sm:mb-2 sm:text-2xl">🔥</div>
                        <h3
                            class="mb-1 text-xs font-bold text-foreground sm:text-sm"
                        >
                            {{ t('landing.tier_beginner') }}
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            {{ t('landing.days_3_6') }}
                        </p>
                        <div
                            class="rounded-full bg-primary/10 px-2 py-1 text-xs font-bold text-primary"
                        >
                            1.2×
                        </div>
                    </div>

                    <!-- Regular -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-3 text-center transition-all hover:scale-105 hover:border-primary/50 sm:p-4"
                    >
                        <div class="mb-1 text-xl sm:mb-2 sm:text-2xl">⚡</div>
                        <h3
                            class="mb-1 text-xs font-bold text-foreground sm:text-sm"
                        >
                            {{ t('landing.tier_regular') }}
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            {{ t('landing.days_7_13') }}
                        </p>
                        <div
                            class="rounded-full bg-primary/10 px-2 py-1 text-xs font-bold text-primary"
                        >
                            1.5×
                        </div>
                    </div>

                    <!-- Committed -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-3 text-center transition-all hover:scale-105 hover:border-amber-500/50 sm:p-4"
                    >
                        <div class="mb-1 text-xl sm:mb-2 sm:text-2xl">💪</div>
                        <h3
                            class="mb-1 text-xs font-bold text-foreground sm:text-sm"
                        >
                            {{ t('landing.tier_committed') }}
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            {{ t('landing.days_14_29') }}
                        </p>
                        <div
                            class="rounded-full bg-amber-500/10 px-2 py-1 text-xs font-bold text-amber-600 dark:text-amber-500"
                        >
                            2.0×
                        </div>
                    </div>

                    <!-- Dedicated -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-3 text-center transition-all hover:scale-105 hover:border-amber-500/50 sm:p-4"
                    >
                        <div class="mb-1 text-xl sm:mb-2 sm:text-2xl">🚀</div>
                        <h3
                            class="mb-1 text-xs font-bold text-foreground sm:text-sm"
                        >
                            {{ t('landing.tier_dedicated') }}
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            {{ t('landing.days_30_59') }}
                        </p>
                        <div
                            class="rounded-full bg-amber-500/10 px-2 py-1 text-xs font-bold text-amber-600 dark:text-amber-500"
                        >
                            {{
                                STREAK_TIERS.find(
                                    (tier) => tier.name === 'Dedicated',
                                )?.multiplier
                            }}×
                        </div>
                    </div>

                    <!-- Expert -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-3 text-center transition-all hover:scale-105 hover:border-amber-500/50 sm:p-4"
                    >
                        <div class="mb-1 text-xl sm:mb-2 sm:text-2xl">⭐</div>
                        <h3
                            class="mb-1 text-xs font-bold text-foreground sm:text-sm"
                        >
                            {{ t('landing.tier_expert') }}
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            {{ t('landing.days_60_99') }}
                        </p>
                        <div
                            class="rounded-full bg-amber-500/10 px-2 py-1 text-xs font-bold text-amber-600 dark:text-amber-500"
                        >
                            3.0×
                        </div>
                    </div>

                    <!-- Master -->
                    <div
                        class="rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 to-orange-500/10 p-3 text-center transition-all hover:scale-105 hover:border-amber-500/70 sm:p-4"
                    >
                        <div class="mb-1 text-xl sm:mb-2 sm:text-2xl">👑</div>
                        <h3
                            class="mb-1 text-xs font-bold text-foreground sm:text-sm"
                        >
                            {{ t('landing.tier_master') }}
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            {{ t('landing.days_100_199') }}
                        </p>
                        <div
                            class="rounded-full bg-amber-500/20 px-2 py-1 text-xs font-bold text-amber-700 dark:text-amber-400"
                        >
                            4.0×
                        </div>
                    </div>

                    <!-- Legend -->
                    <div
                        class="rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 to-orange-500/10 p-3 text-center transition-all hover:scale-105 hover:border-amber-500/70 sm:p-4"
                    >
                        <div class="mb-1 text-xl sm:mb-2 sm:text-2xl">🏆</div>
                        <h3
                            class="mb-1 text-xs font-bold text-foreground sm:text-sm"
                        >
                            {{ t('landing.tier_legend') }}
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            {{ t('landing.days_200_plus') }}
                        </p>
                        <div
                            class="rounded-full bg-amber-500/20 px-2 py-1 text-xs font-bold text-amber-700 dark:text-amber-400"
                        >
                            5.0×
                        </div>
                    </div>
                </div>

                <!-- How it Works -->
                <div
                    class="mb-8 rounded-2xl border border-border/50 bg-card/30 p-6 sm:mb-12 sm:p-8"
                >
                    <h3
                        class="mb-6 text-center text-xl font-bold text-foreground sm:text-2xl"
                    >
                        {{ t('landing.how_it_works_title') }}
                    </h3>
                    <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3">
                        <div class="text-center">
                            <div
                                class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 sm:mb-4 sm:h-12 sm:w-12"
                            >
                                <Flame
                                    class="h-5 w-5 text-primary sm:h-6 sm:w-6"
                                />
                            </div>
                            <h4
                                class="mb-2 text-sm font-bold text-foreground sm:text-base"
                            >
                                {{ t('landing.build_streaks_title') }}
                            </h4>
                            <p class="text-xs text-muted-foreground sm:text-sm">
                                {{ t('landing.build_streaks_description') }}
                            </p>
                        </div>
                        <div class="text-center">
                            <div
                                class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-amber-500/10 sm:mb-4 sm:h-12 sm:w-12"
                            >
                                <TrendingUp
                                    class="h-5 w-5 text-amber-600 sm:h-6 sm:w-6 dark:text-amber-500"
                                />
                            </div>
                            <h4
                                class="mb-2 text-sm font-bold text-foreground sm:text-base"
                            >
                                {{ t('landing.multiply_points_title') }}
                            </h4>
                            <p class="text-xs text-muted-foreground sm:text-sm">
                                {{ t('landing.multiply_points_description') }}
                                {{
                                    STREAK_TIERS.find(
                                        (tier) => tier.name === 'Dedicated',
                                    )?.min
                                }}-day streak =
                                {{
                                    STREAK_TIERS.find(
                                        (tier) => tier.name === 'Dedicated',
                                    )?.multiplier
                                }}× points!
                            </p>
                        </div>
                        <div class="text-center sm:col-span-2 md:col-span-1">
                            <div
                                class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-purple-500/10 sm:mb-4 sm:h-12 sm:w-12"
                            >
                                <Trophy
                                    class="h-5 w-5 text-purple-600 sm:h-6 sm:w-6"
                                />
                            </div>
                            <h4
                                class="mb-2 text-sm font-bold text-foreground sm:text-base"
                            >
                                {{ t('landing.milestone_bonuses_title') }}
                            </h4>
                            <p class="text-xs text-muted-foreground sm:text-sm">
                                {{ t('landing.milestone_bonuses_description') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Example Calculation -->
                <div
                    class="rounded-2xl border border-primary/20 bg-gradient-to-br from-primary/5 to-background p-6 sm:p-8"
                >
                    <h3
                        class="mb-4 text-center text-lg font-bold text-foreground sm:mb-6 sm:text-xl"
                    >
                        {{ t('landing.example_calculation_title') }}
                    </h3>
                    <div class="space-y-3 text-center sm:space-y-4">
                        <div>
                            <p class="text-xs text-muted-foreground sm:text-sm">
                                {{ t('landing.base_activity_points') }}
                            </p>
                            <p
                                class="text-xl font-bold text-foreground sm:text-2xl"
                            >
                                20 {{ t('points.short') }}
                            </p>
                        </div>
                        <div class="text-xl text-muted-foreground sm:text-2xl">
                            ×
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground sm:text-sm">
                                {{ t('landing.dedicated_tier') }}
                            </p>
                            <p
                                class="text-xl font-bold text-amber-600 sm:text-2xl dark:text-amber-500"
                            >
                                {{
                                    STREAK_TIERS.find(
                                        (tier) => tier.name === 'Dedicated',
                                    )?.multiplier
                                }}× {{ t('landing.multiplier') }}
                            </p>
                        </div>
                        <div class="text-xl text-muted-foreground sm:text-2xl">
                            +
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground sm:text-sm">
                                {{ t('landing.milestone_bonus') }}
                            </p>
                            <p
                                class="text-xl font-bold text-purple-600 sm:text-2xl"
                            >
                                +200 {{ t('points.short') }}
                            </p>
                        </div>
                        <div class="border-t border-border pt-3 sm:pt-4">
                            <p class="text-xs text-muted-foreground sm:text-sm">
                                {{ t('landing.total_points_earned') }}
                            </p>
                            <p
                                class="text-2xl font-bold text-primary sm:text-3xl"
                            >
                                250 {{ t('points.short') }} 🎉
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rankings Section -->
        <section class="container mx-auto px-4 py-12 sm:py-16 md:py-20">
            <div class="mx-auto max-w-5xl">
                <div class="mb-12 text-center sm:mb-16">
                    <h2
                        class="mb-4 text-2xl font-bold text-foreground sm:text-3xl md:text-4xl"
                    >
                        {{ t('landing.global_rankings_title') }}
                    </h2>
                    <p
                        class="mx-auto max-w-2xl text-base text-muted-foreground sm:text-lg"
                    >
                        {{ t('landing.global_rankings_description') }}
                    </p>
                </div>

                <div
                    class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        class="rounded-xl border border-border/50 bg-card/50 p-4 text-center sm:p-6"
                    >
                        <div class="mb-2 flex justify-center">
                            <Footprints
                                class="h-6 w-6 text-primary sm:h-8 sm:w-8"
                            />
                        </div>
                        <h3
                            class="mb-1 text-sm font-bold text-foreground sm:text-base"
                        >
                            {{ t('landing.today_title') }}
                        </h3>
                        <p class="text-xs text-muted-foreground sm:text-sm">
                            {{ t('landing.today_description') }}
                        </p>
                    </div>

                    <div
                        class="rounded-xl border border-border/50 bg-card/50 p-4 text-center sm:p-6"
                    >
                        <div class="mb-2 flex justify-center">
                            <CalendarCheck
                                class="h-6 w-6 text-muted-foreground sm:h-8 sm:w-8"
                            />
                        </div>
                        <h3
                            class="mb-1 text-sm font-bold text-foreground sm:text-base"
                        >
                            {{ t('landing.yesterday_title') }}
                        </h3>
                        <p class="text-xs text-muted-foreground sm:text-sm">
                            {{ t('landing.yesterday_description') }}
                        </p>
                    </div>

                    <div
                        class="rounded-xl border border-border/50 bg-card/50 p-4 text-center sm:p-6"
                    >
                        <div class="mb-2 flex justify-center">
                            <Award
                                class="h-6 w-6 text-amber-500 sm:h-8 sm:w-8"
                            />
                        </div>
                        <h3
                            class="mb-1 text-sm font-bold text-foreground sm:text-base"
                        >
                            {{ t('landing.season_label') }}
                        </h3>
                        <p class="text-xs text-muted-foreground sm:text-sm">
                            {{ t('landing.quarterly_rank') }}
                        </p>
                    </div>

                    <div
                        class="rounded-xl border border-border/50 bg-card/50 p-4 text-center sm:p-6"
                    >
                        <div class="mb-2 flex justify-center">
                            <Trophy
                                class="h-6 w-6 text-amber-500 sm:h-8 sm:w-8"
                            />
                        </div>
                        <h3
                            class="mb-1 text-sm font-bold text-foreground sm:text-base"
                        >
                            {{ t('landing.year_title') }}
                        </h3>
                        <p class="text-xs text-muted-foreground sm:text-sm">
                            {{ t('landing.year_description') }}
                        </p>
                    </div>
                </div>

                <!-- Example Leaderboard -->
                <div
                    class="mt-8 rounded-2xl border border-border/50 bg-card/30 p-6 sm:mt-12 sm:p-8"
                >
                    <h3
                        class="mb-4 text-center text-lg font-bold text-foreground sm:mb-6 sm:text-xl"
                    >
                        {{ t('landing.todays_top3_title') }}
                    </h3>
                    <div class="space-y-3 sm:space-y-4">
                        <div
                            class="flex items-center justify-between rounded-lg bg-gradient-to-r from-yellow-500/10 to-transparent p-3 sm:p-4"
                        >
                            <div class="flex items-center gap-2 sm:gap-3">
                                <span class="text-xl sm:text-2xl">🥇</span>
                                <div>
                                    <p
                                        class="text-sm font-bold text-foreground sm:text-base"
                                    >
                                        Sarah K.
                                    </p>
                                    <p
                                        class="text-xs text-muted-foreground sm:text-sm"
                                    >
                                        HIIT, Healthy meals, Meditation
                                    </p>
                                </div>
                            </div>
                            <span
                                class="text-lg font-bold text-primary sm:text-xl"
                                >180 {{ t('points.short') }}</span
                            >
                        </div>

                        <div
                            class="flex items-center justify-between rounded-lg bg-gradient-to-r from-gray-300/10 to-transparent p-3 sm:p-4"
                        >
                            <div class="flex items-center gap-2 sm:gap-3">
                                <span class="text-xl sm:text-2xl">🥈</span>
                                <div>
                                    <p
                                        class="text-sm font-bold text-foreground sm:text-base"
                                    >
                                        Mike R.
                                    </p>
                                    <p
                                        class="text-xs text-muted-foreground sm:text-sm"
                                    >
                                        Gym, Running, Meal prep
                                    </p>
                                </div>
                            </div>
                            <span
                                class="text-lg font-bold text-primary sm:text-xl"
                                >165 {{ t('points.short') }}</span
                            >
                        </div>

                        <div
                            class="flex items-center justify-between rounded-lg bg-gradient-to-r from-orange-500/10 to-transparent p-3 sm:p-4"
                        >
                            <div class="flex items-center gap-2 sm:gap-3">
                                <span class="text-xl sm:text-2xl">🥉</span>
                                <div>
                                    <p
                                        class="text-sm font-bold text-foreground sm:text-base"
                                    >
                                        Alex T.
                                    </p>
                                    <p
                                        class="text-xs text-muted-foreground sm:text-sm"
                                    >
                                        Cycling, Yoga, Journaling
                                    </p>
                                </div>
                            </div>
                            <span
                                class="text-lg font-bold text-primary sm:text-xl"
                                >155 {{ t('points.short') }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="container mx-auto px-4 py-12 sm:py-16">
            <div class="mx-auto max-w-4xl">
                <div class="mb-8 text-center sm:mb-12">
                    <h2
                        class="mb-4 text-2xl font-bold text-foreground sm:text-3xl md:text-4xl"
                    >
                        {{ t('landing.how_iomeh_works_title') }}
                    </h2>
                    <p class="text-base text-muted-foreground sm:text-lg">
                        {{ t('landing.how_iomeh_works_description') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2">
                    <div class="flex gap-3 sm:gap-4">
                        <div
                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground sm:h-10 sm:w-10 sm:text-base"
                        >
                            1
                        </div>
                        <div>
                            <h3
                                class="mb-2 text-sm font-bold text-foreground sm:text-base"
                            >
                                {{ t('landing.step1_title') }}
                            </h3>
                            <p class="text-xs text-muted-foreground sm:text-sm">
                                {{ t('landing.step1_description') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 sm:gap-4">
                        <div
                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground sm:h-10 sm:w-10 sm:text-base"
                        >
                            2
                        </div>
                        <div>
                            <h3
                                class="mb-2 text-sm font-bold text-foreground sm:text-base"
                            >
                                {{ t('landing.step2_title') }}
                            </h3>
                            <p class="text-xs text-muted-foreground sm:text-sm">
                                {{ t('landing.step2_description') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 sm:gap-4">
                        <div
                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground sm:h-10 sm:w-10 sm:text-base"
                        >
                            3
                        </div>
                        <div>
                            <h3
                                class="mb-2 text-sm font-bold text-foreground sm:text-base"
                            >
                                {{ t('landing.step3_title') }}
                            </h3>
                            <p class="text-xs text-muted-foreground sm:text-sm">
                                {{ t('landing.step3_description') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 sm:gap-4">
                        <div
                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground sm:h-10 sm:w-10 sm:text-base"
                        >
                            4
                        </div>
                        <div>
                            <h3
                                class="mb-2 text-sm font-bold text-foreground sm:text-base"
                            >
                                {{ t('landing.climb_rankings') }}
                            </h3>
                            <p class="text-xs text-muted-foreground sm:text-sm">
                                {{ t('landing.step4_description') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="container mx-auto px-4 py-12 sm:py-16">
            <div
                class="mx-auto max-w-4xl rounded-2xl bg-gradient-to-r from-primary/10 to-emerald-500/10 p-6 text-center sm:p-8 md:p-12"
            >
                <h2
                    class="mb-6 text-2xl font-bold text-foreground sm:mb-8 sm:text-3xl"
                >
                    {{ t('landing.community_title') }}
                </h2>
                <div class="grid grid-cols-1 gap-6 sm:gap-8 md:grid-cols-3">
                    <div>
                        <div
                            class="mb-2 flex items-center justify-center gap-2"
                        >
                            <Heart class="h-5 w-5 text-primary sm:h-6 sm:w-6" />
                            <span
                                class="text-3xl font-bold text-primary sm:text-4xl"
                            >
                                {{ formatNumber(stats.active_users) }}
                            </span>
                        </div>
                        <div class="text-sm text-muted-foreground sm:text-base">
                            {{ t('landing.stats.active_users') }}
                        </div>
                    </div>
                    <div>
                        <div
                            class="mb-2 flex items-center justify-center gap-2"
                        >
                            <Activity
                                class="h-5 w-5 text-primary sm:h-6 sm:w-6"
                            />
                            <span
                                class="text-3xl font-bold text-primary sm:text-4xl"
                            >
                                {{ formatNumber(stats.total_activities) }}
                            </span>
                        </div>
                        <div class="text-sm text-muted-foreground sm:text-base">
                            {{ t('landing.stats.total_activities') }}
                        </div>
                    </div>
                    <div>
                        <div
                            class="mb-2 flex items-center justify-center gap-2"
                        >
                            <Flame
                                class="h-5 w-5 text-orange-500 sm:h-6 sm:w-6"
                            />
                            <span
                                class="text-3xl font-bold text-primary sm:text-4xl"
                            >
                                {{ formatNumber(stats.combined_streak) }}
                            </span>
                        </div>
                        <div class="text-sm text-muted-foreground sm:text-base">
                            {{ t('landing.stats.combined_streak') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="container mx-auto px-4 py-12 sm:py-16 md:py-20">
            <div class="mx-auto max-w-3xl text-center">
                <h2
                    class="mb-4 text-2xl font-bold text-foreground sm:text-3xl md:text-4xl"
                >
                    {{ t('landing.ready_to_compete_title') }}
                </h2>
                <p
                    class="mb-6 text-base text-muted-foreground sm:mb-8 sm:text-lg"
                >
                    {{ t('landing.ready_to_compete_description') }}
                </p>
                <Link href="/register">
                    <Button
                        size="lg"
                        class="w-full px-6 py-3 text-base sm:w-auto sm:px-8 sm:py-4 sm:text-lg"
                    >
                        <Trophy class="h-4 w-4 sm:h-5 sm:w-5" />
                        {{ t('landing.start_journey') }}
                    </Button>
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer
            class="border-t border-border/40 bg-background/80 backdrop-blur-sm"
        >
            <div class="container mx-auto px-4 py-6 sm:py-8">
                <div class="text-center text-muted-foreground">
                    <p class="mb-2 text-sm font-medium sm:text-base">
                        {{ appName }} - I Owe Me Health
                    </p>
                    <p class="text-xs sm:text-sm">
                        &copy; {{ currentYear }} {{ appName }}. All rights
                        reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
