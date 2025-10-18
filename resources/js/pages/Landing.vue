<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import SEO from '@/components/SEO.vue';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
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
    title: 'Global Fitness Ranking Foundation',
    description:
        'IOMeH (I Owe Me Health) is your personal fitness ranking foundation. Track workouts and nutrition activities while competing on global rankings. Turn fitness into competition.',
    keywords: [
        'fitness tracking',
        'workout ranking',
        'fitness competition',
        'fitness gamification',
        'activity tracking',
        'fitness leaderboard',
        'workout points',
        'nutrition tracking',
        'fitness foundation',
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
    >
        <!-- Navigation -->
        <nav
            class="sticky top-0 z-50 border-b border-border/40 bg-background/95 backdrop-blur-sm"
        >
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <AppLogo />
                        <div>
                            <h1
                                class="text-lg font-bold text-foreground md:text-2xl"
                            >
                                {{ appName }}
                            </h1>
                            <p class="text-xs text-muted-foreground md:text-sm">
                                I Owe Me Health
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <Link href="/login">
                            <Button variant="ghost" size="sm">Sign in</Button>
                        </Link>
                        <Link href="/register">
                            <Button size="sm">Join Now</Button>
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="container mx-auto px-4 py-20">
            <div class="mx-auto max-w-4xl text-center">
                <div class="mb-8">
                    <div
                        class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-2 text-sm font-medium"
                    >
                        <Trophy class="h-4 w-4" />
                        <span
                            >Your Global Health Rank — Shaped by Your Daily
                            Habits</span
                        >
                    </div>
                </div>

                <h1
                    class="mb-6 text-4xl font-bold text-foreground md:text-6xl lg:text-7xl"
                >
                    Track Your Health<br />
                    <span class="text-primary">Earn Your Rank</span>
                </h1>

                <p
                    class="mx-auto mb-10 max-w-3xl text-xl text-muted-foreground md:text-2xl"
                >
                    IOMeH turns fitness tracking into global competition. Log
                    workouts and nutrition activities—earn points and climb the
                    rankings. It's like a sports foundation, but for your
                    fitness journey.
                </p>

                <div
                    class="flex flex-col items-center justify-center gap-4 sm:flex-row"
                >
                    <Link href="/register">
                        <Button size="lg" class="px-8 py-4 text-lg">
                            <Award class="h-5 w-5" />
                            Start Competing
                        </Button>
                    </Link>
                    <Link href="/login">
                        <Button
                            variant="outline"
                            size="lg"
                            class="px-8 py-4 text-lg"
                        >
                            Sign in
                        </Button>
                    </Link>
                </div>

                <!-- Achievement Preview -->
                <div class="mt-16">
                    <p class="mb-4 text-center text-sm text-muted-foreground">
                        Track your progress across seasons
                    </p>
                    <div
                        class="flex flex-wrap items-center justify-center gap-3"
                    >
                        <!-- Season Badge -->
                        <div
                            class="group relative overflow-hidden rounded-xl border border-primary/30 bg-gradient-to-br from-primary/10 to-primary/5 px-6 py-3 transition-all hover:scale-105 hover:border-primary/50 hover:shadow-lg"
                        >
                            <div class="flex items-center gap-2">
                                <Award class="h-5 w-5 text-primary" />
                                <span class="font-bold text-foreground"
                                    >2025 Q4 #7</span
                                >
                            </div>
                        </div>

                        <!-- Year Badge -->
                        <div
                            class="group relative overflow-hidden rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 to-orange-500/5 px-6 py-3 transition-all hover:scale-105 hover:border-amber-500/50 hover:shadow-lg"
                        >
                            <div class="flex items-center gap-2">
                                <Trophy
                                    class="h-5 w-5 text-amber-600 dark:text-amber-500"
                                />
                                <span class="font-bold text-foreground"
                                    >2025 #15</span
                                >
                            </div>
                        </div>

                        <!-- Streak Badge -->
                        <div
                            class="group relative overflow-hidden rounded-xl border border-orange-500/30 bg-gradient-to-br from-orange-500/10 to-red-500/5 px-6 py-3 transition-all hover:scale-105 hover:border-orange-500/50 hover:shadow-lg"
                        >
                            <div class="flex items-center gap-2">
                                <Flame class="h-5 w-5 text-orange-500" />
                                <span class="font-bold text-foreground"
                                    >14 Day Streak</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Two Pillars of Fitness Section -->
        <section class="container mx-auto px-4 py-16">
            <div class="mx-auto max-w-5xl">
                <div class="mb-16 text-center">
                    <h2
                        class="mb-4 text-3xl font-bold text-foreground md:text-4xl"
                    >
                        Track Your Fitness Journey
                    </h2>
                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                        Focus on what matters most for your fitness goals. IOMeH
                        tracks workouts and nutrition with many activities to
                        choose from.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <!-- Workout -->
                    <div
                        class="rounded-2xl border border-primary/20 bg-gradient-to-br from-primary/10 to-primary/5 p-8"
                    >
                        <div class="mb-6 flex items-center gap-4">
                            <div
                                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary"
                            >
                                <Activity
                                    class="h-7 w-7 text-primary-foreground"
                                />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-foreground">
                                    💪 Workout
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    30 activities | 10-60 points
                                </p>
                            </div>
                        </div>
                        <p class="text-muted-foreground">
                            Running, gym, HIIT, swimming, cycling, sports,
                            martial arts, and more. Points based on intensity
                            and duration.
                        </p>
                    </div>

                    <!-- Nutrition -->
                    <div
                        class="rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 p-8"
                    >
                        <div class="mb-6 flex items-center gap-4">
                            <div
                                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-500"
                            >
                                <Apple class="h-7 w-7 text-white" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-foreground">
                                    🥗 Nutrition
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    15 activities | 10-30 points
                                </p>
                            </div>
                        </div>
                        <p class="text-muted-foreground">
                            Healthy meals, vegetables, fruits, hydration,
                            protein goals, supplements, and cooking at home.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Streak Bonus Section -->
        <section class="container mx-auto px-4 py-20">
            <div class="mx-auto max-w-5xl">
                <div class="mb-16 text-center">
                    <h2
                        class="mb-4 text-3xl font-bold text-foreground md:text-4xl"
                    >
                        🔥 Streak Bonus System
                    </h2>
                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                        Consistency is key. Build daily streaks and multiply
                        your points with progressive rewards up to 5×.
                    </p>
                </div>

                <!-- Streak Tiers Grid -->
                <div
                    class="mb-12 grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-8"
                >
                    <!-- Newcomer -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-4 text-center transition-all hover:scale-105 hover:border-primary/50"
                    >
                        <div class="mb-2 text-2xl">🌱</div>
                        <h3 class="mb-1 text-sm font-bold text-foreground">
                            Newcomer
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            Days 1-2
                        </p>
                        <div
                            class="rounded-full bg-primary/10 px-2 py-1 text-xs font-bold text-primary"
                        >
                            1.0×
                        </div>
                    </div>

                    <!-- Beginner -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-4 text-center transition-all hover:scale-105 hover:border-primary/50"
                    >
                        <div class="mb-2 text-2xl">🔥</div>
                        <h3 class="mb-1 text-sm font-bold text-foreground">
                            Beginner
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            Days 3-6
                        </p>
                        <div
                            class="rounded-full bg-primary/10 px-2 py-1 text-xs font-bold text-primary"
                        >
                            1.2×
                        </div>
                    </div>

                    <!-- Regular -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-4 text-center transition-all hover:scale-105 hover:border-primary/50"
                    >
                        <div class="mb-2 text-2xl">⚡</div>
                        <h3 class="mb-1 text-sm font-bold text-foreground">
                            Regular
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            Days 7-13
                        </p>
                        <div
                            class="rounded-full bg-primary/10 px-2 py-1 text-xs font-bold text-primary"
                        >
                            1.5×
                        </div>
                    </div>

                    <!-- Committed -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-4 text-center transition-all hover:scale-105 hover:border-amber-500/50"
                    >
                        <div class="mb-2 text-2xl">💪</div>
                        <h3 class="mb-1 text-sm font-bold text-foreground">
                            Committed
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            Days 14-29
                        </p>
                        <div
                            class="rounded-full bg-amber-500/10 px-2 py-1 text-xs font-bold text-amber-600 dark:text-amber-500"
                        >
                            2.0×
                        </div>
                    </div>

                    <!-- Dedicated -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-4 text-center transition-all hover:scale-105 hover:border-amber-500/50"
                    >
                        <div class="mb-2 text-2xl">🚀</div>
                        <h3 class="mb-1 text-sm font-bold text-foreground">
                            Dedicated
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            Days 30-59
                        </p>
                        <div
                            class="rounded-full bg-amber-500/10 px-2 py-1 text-xs font-bold text-amber-600 dark:text-amber-500"
                        >
                            2.5×
                        </div>
                    </div>

                    <!-- Expert -->
                    <div
                        class="rounded-xl border border-border/50 bg-card/30 p-4 text-center transition-all hover:scale-105 hover:border-amber-500/50"
                    >
                        <div class="mb-2 text-2xl">⭐</div>
                        <h3 class="mb-1 text-sm font-bold text-foreground">
                            Expert
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            Days 60-99
                        </p>
                        <div
                            class="rounded-full bg-amber-500/10 px-2 py-1 text-xs font-bold text-amber-600 dark:text-amber-500"
                        >
                            3.0×
                        </div>
                    </div>

                    <!-- Master -->
                    <div
                        class="rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 to-orange-500/10 p-4 text-center transition-all hover:scale-105 hover:border-amber-500/70"
                    >
                        <div class="mb-2 text-2xl">👑</div>
                        <h3 class="mb-1 text-sm font-bold text-foreground">
                            Master
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            Days 100-199
                        </p>
                        <div
                            class="rounded-full bg-amber-500/20 px-2 py-1 text-xs font-bold text-amber-700 dark:text-amber-400"
                        >
                            4.0×
                        </div>
                    </div>

                    <!-- Legend -->
                    <div
                        class="rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-500/10 to-orange-500/10 p-4 text-center transition-all hover:scale-105 hover:border-amber-500/70"
                    >
                        <div class="mb-2 text-2xl">🏆</div>
                        <h3 class="mb-1 text-sm font-bold text-foreground">
                            Legend
                        </h3>
                        <p class="mb-2 text-xs text-muted-foreground">
                            Days 200+
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
                    class="mb-12 rounded-2xl border border-border/50 bg-card/30 p-8"
                >
                    <h3
                        class="mb-6 text-center text-2xl font-bold text-foreground"
                    >
                        How It Works
                    </h3>
                    <div class="grid gap-6 md:grid-cols-3">
                        <div class="text-center">
                            <div
                                class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10"
                            >
                                <Flame class="h-6 w-6 text-primary" />
                            </div>
                            <h4 class="mb-2 font-bold text-foreground">
                                Build Streaks
                            </h4>
                            <p class="text-sm text-muted-foreground">
                                Log activities daily to maintain your streak.
                                Miss a day and it resets to 1.
                            </p>
                        </div>
                        <div class="text-center">
                            <div
                                class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-500/10"
                            >
                                <TrendingUp
                                    class="h-6 w-6 text-amber-600 dark:text-amber-500"
                                />
                            </div>
                            <h4 class="mb-2 font-bold text-foreground">
                                Multiply Points
                            </h4>
                            <p class="text-sm text-muted-foreground">
                                Your streak tier multiplies all activity points.
                                30-day streak = 2.5× points!
                            </p>
                        </div>
                        <div class="text-center">
                            <div
                                class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-purple-500/10"
                            >
                                <Trophy class="h-6 w-6 text-purple-600" />
                            </div>
                            <h4 class="mb-2 font-bold text-foreground">
                                Milestone Bonuses
                            </h4>
                            <p class="text-sm text-muted-foreground">
                                Hit milestones (7, 30, 100, 365 days) for extra
                                bonus points on top of multipliers!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Example Calculation -->
                <div
                    class="rounded-2xl border border-primary/20 bg-gradient-to-br from-primary/5 to-background p-8"
                >
                    <h3
                        class="mb-6 text-center text-xl font-bold text-foreground"
                    >
                        📊 Example: 30-Day Streak
                    </h3>
                    <div class="space-y-4 text-center">
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Base Activity Points
                            </p>
                            <p class="text-2xl font-bold text-foreground">
                                20 pts
                            </p>
                        </div>
                        <div class="text-2xl text-muted-foreground">×</div>
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Dedicated Tier (30 days)
                            </p>
                            <p
                                class="text-2xl font-bold text-amber-600 dark:text-amber-500"
                            >
                                2.5× Multiplier
                            </p>
                        </div>
                        <div class="text-2xl text-muted-foreground">+</div>
                        <div>
                            <p class="text-sm text-muted-foreground">
                                Milestone Bonus
                            </p>
                            <p class="text-2xl font-bold text-purple-600">
                                +200 pts
                            </p>
                        </div>
                        <div class="border-t border-border pt-4">
                            <p class="text-sm text-muted-foreground">
                                Total Points Earned
                            </p>
                            <p class="text-3xl font-bold text-primary">
                                250 pts 🎉
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rankings Section -->
        <section class="container mx-auto px-4 py-20">
            <div class="mx-auto max-w-5xl">
                <div class="mb-16 text-center">
                    <h2
                        class="mb-4 text-3xl font-bold text-foreground md:text-4xl"
                    >
                        Compete on Global Rankings
                    </h2>
                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                        Like a sports foundation, but for health. See where you
                        rank today, this season, and all-time.
                    </p>
                </div>

                <div
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        class="rounded-xl border border-border/50 bg-card/50 p-6 text-center"
                    >
                        <div class="mb-2 flex justify-center">
                            <Footprints class="h-8 w-8 text-yellow-500" />
                        </div>
                        <h3 class="mb-1 font-bold text-foreground">Today</h3>
                        <p class="text-sm text-muted-foreground">
                            Real-time daily rankings
                        </p>
                    </div>

                    <div
                        class="rounded-xl border border-border/50 bg-card/50 p-6 text-center"
                    >
                        <div class="mb-2 flex justify-center">
                            <CalendarCheck class="h-8 w-8 text-blue-500" />
                        </div>
                        <h3 class="mb-1 font-bold text-foreground">
                            Yesterday
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            Previous day champions
                        </p>
                    </div>

                    <div
                        class="rounded-xl border border-border/50 bg-card/50 p-6 text-center"
                    >
                        <div class="mb-2 flex justify-center">
                            <Award class="h-8 w-8 text-primary" />
                        </div>
                        <h3 class="mb-1 font-bold text-foreground">
                            Season (Q1-Q4)
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            Quarterly ran
                        </p>
                    </div>

                    <div
                        class="rounded-xl border border-border/50 bg-card/50 p-6 text-center"
                    >
                        <div class="mb-2 flex justify-center">
                            <Trophy class="h-8 w-8 text-amber-500" />
                        </div>
                        <h3 class="mb-1 font-bold text-foreground">Year</h3>
                        <p class="text-sm text-muted-foreground">
                            Annual champions
                        </p>
                    </div>
                </div>

                <!-- Example Leaderboard -->
                <div
                    class="mt-12 rounded-2xl border border-border/50 bg-card/30 p-8"
                >
                    <h3
                        class="mb-6 text-center text-xl font-bold text-foreground"
                    >
                        Today's Top 3
                    </h3>
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between rounded-lg bg-gradient-to-r from-yellow-500/10 to-transparent p-4"
                        >
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🥇</span>
                                <div>
                                    <p class="font-bold text-foreground">
                                        Sarah K.
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        HIIT, Healthy meals, Meditation
                                    </p>
                                </div>
                            </div>
                            <span class="text-xl font-bold text-primary"
                                >180 pts</span
                            >
                        </div>

                        <div
                            class="flex items-center justify-between rounded-lg bg-gradient-to-r from-gray-300/10 to-transparent p-4"
                        >
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🥈</span>
                                <div>
                                    <p class="font-bold text-foreground">
                                        Mike R.
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Gym, Running, Meal prep
                                    </p>
                                </div>
                            </div>
                            <span class="text-xl font-bold text-primary"
                                >165 pts</span
                            >
                        </div>

                        <div
                            class="flex items-center justify-between rounded-lg bg-gradient-to-r from-orange-500/10 to-transparent p-4"
                        >
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🥉</span>
                                <div>
                                    <p class="font-bold text-foreground">
                                        Alex T.
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Cycling, Yoga, Journaling
                                    </p>
                                </div>
                            </div>
                            <span class="text-xl font-bold text-primary"
                                >155 pts</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="container mx-auto px-4 py-16">
            <div class="mx-auto max-w-4xl">
                <div class="mb-12 text-center">
                    <h2
                        class="mb-4 text-3xl font-bold text-foreground md:text-4xl"
                    >
                        How IOMeH Works
                    </h2>
                    <p class="text-lg text-muted-foreground">
                        Simple 4-step process to start competing
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="flex gap-4">
                        <div
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary font-bold text-primary-foreground"
                        >
                            1
                        </div>
                        <div>
                            <h3 class="mb-2 font-bold text-foreground">
                                Sign Up & Select Activities
                            </h3>
                            <p class="text-muted-foreground">
                                Choose 5-10 activities you want to track from
                                many options across all health categories.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary font-bold text-primary-foreground"
                        >
                            2
                        </div>
                        <div>
                            <h3 class="mb-2 font-bold text-foreground">
                                Log Today's Activities
                            </h3>
                            <p class="text-muted-foreground">
                                Record today's activities in real-time—workouts
                                and nutrition. Add notes and optional proof.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary font-bold text-primary-foreground"
                        >
                            3
                        </div>
                        <div>
                            <h3 class="mb-2 font-bold text-foreground">
                                Earn Points & Build Streaks
                            </h3>
                            <p class="text-muted-foreground">
                                Get 10-50 points per activity. Build daily
                                streaks. Watch your stats grow.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary font-bold text-primary-foreground"
                        >
                            4
                        </div>
                        <div>
                            <h3 class="mb-2 font-bold text-foreground">
                                Climb the Rankings
                            </h3>
                            <p class="text-muted-foreground">
                                Compete on daily, seasonal, and yearly ran.
                                Track your rank: "2025 Q1 #11".
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="container mx-auto px-4 py-16">
            <div
                class="mx-auto max-w-4xl rounded-2xl bg-gradient-to-r from-primary/10 to-emerald-500/10 p-12 text-center"
            >
                <h2 class="mb-8 text-3xl font-bold text-foreground">
                    Join the Global Health Community
                </h2>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div>
                        <div
                            class="mb-2 flex items-center justify-center gap-2"
                        >
                            <Heart class="h-6 w-6 text-primary" />
                            <span class="text-4xl font-bold text-primary">
                                {{ formatNumber(stats.active_users) }}
                            </span>
                        </div>
                        <div class="text-muted-foreground">Active Athletes</div>
                    </div>
                    <div>
                        <div
                            class="mb-2 flex items-center justify-center gap-2"
                        >
                            <Activity class="h-6 w-6 text-primary" />
                            <span class="text-4xl font-bold text-primary">
                                {{ formatNumber(stats.total_activities) }}
                            </span>
                        </div>
                        <div class="text-muted-foreground">
                            Activities Logged
                        </div>
                    </div>
                    <div>
                        <div
                            class="mb-2 flex items-center justify-center gap-2"
                        >
                            <Flame class="h-6 w-6 text-orange-500" />
                            <span class="text-4xl font-bold text-primary">
                                {{ formatNumber(stats.combined_streak) }}
                            </span>
                        </div>
                        <div class="text-muted-foreground">
                            Combined Streak Days
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="container mx-auto px-4 py-20">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="mb-4 text-3xl font-bold text-foreground md:text-4xl">
                    Ready to Compete in Wellness?
                </h2>
                <p class="mb-8 text-lg text-muted-foreground">
                    Join IOMeH today and turn "I should prioritize health" into
                    "I compete in health." Track, compete, and earn your rank.
                </p>
                <Link href="/register">
                    <Button size="lg" class="px-8 py-4 text-lg">
                        <Trophy class="h-5 w-5" />
                        Start Your Journey
                    </Button>
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer
            class="border-t border-border/40 bg-background/80 backdrop-blur-sm"
        >
            <div class="container mx-auto px-4 py-8">
                <div class="text-center text-muted-foreground">
                    <p class="mb-2 font-medium">
                        {{ appName }} - I Owe Me Health
                    </p>
                    <p class="text-sm">
                        &copy; {{ currentYear }} {{ appName }}. All rights
                        reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
