<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import SEO from '@/components/SEO.vue';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import {
    Activity,
    Apple,
    Award,
    Brain,
    Flame,
    Heart,
    Moon,
    TrendingUp,
    Trophy,
    Zap,
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
const appName = import.meta.env.VITE_APP_NAME || 'IOMeW';

// Get current year dynamically
const currentYear = new Date().getFullYear();

// Simple number formatting function
const formatNumber = (num: number) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
};

// SEO Data
// Note: The SEO component automatically appends " | IOMeW" to the title
const seoData = {
    title: 'Global Wellness Ranking Organization',
    description:
        'IOMeW (I Owe Me Wellness) is your personal wellness ranking organization. Track workouts, nutrition, sleep, and mindfulness activities while competing on global leaderboards. Turn wellness into competition.',
    keywords: [
        'wellness tracking',
        'fitness ranking',
        'wellness competition',
        'wellness gamification',
        'activity tracking',
        'wellness leaderboard',
        'workout points',
        'nutrition tracking',
        'sleep monitoring',
        'mindfulness app',
        'wellness organization',
        'fitness motivation',
    ],
    url: '/',
    image: '/iomew.png',
    imageAlt: 'IOMeW - I Owe Me Wellness',
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
                                I Owe Me Wellness
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <Link href="/login">
                            <Button variant="ghost" size="sm">Log in</Button>
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
                            >Your Global Wellness Rank — Shaped by Your Daily
                            Habits</span
                        >
                    </div>
                </div>

                <h1
                    class="mb-6 text-4xl font-bold text-foreground md:text-6xl lg:text-7xl"
                >
                    Track Your Wellness<br />
                    <span class="text-primary">Earn Your Rank</span>
                </h1>

                <p
                    class="mx-auto mb-10 max-w-3xl text-xl text-muted-foreground md:text-2xl"
                >
                    IOMeW turns wellness tracking into global competition. Log
                    workouts, nutrition, sleep, and mindfulness activities—earn
                    points and climb the rankings. It's like a sports
                    organization, but for your wellness.
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
                            Log In
                        </Button>
                    </Link>
                </div>

                <!-- Example Rank Display -->
                <div
                    class="mt-12 flex justify-center gap-6 text-sm text-muted-foreground"
                >
                    <div
                        class="rounded-lg border border-border/50 bg-card/50 px-4 py-2"
                    >
                        <span class="font-medium">2025 Q1 #11</span>
                    </div>
                    <div
                        class="rounded-lg border border-border/50 bg-card/50 px-4 py-2"
                    >
                        <span class="font-medium">2024 #156</span>
                    </div>
                    <div
                        class="flex items-center gap-2 rounded-lg border border-border/50 bg-card/50 px-4 py-2"
                    >
                        <Flame class="h-4 w-4 text-orange-500" />
                        <span class="font-medium">14 day streak</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Four Pillars of Health Section -->
        <section class="container mx-auto px-4 py-16">
            <div class="mx-auto max-w-5xl">
                <div class="mb-16 text-center">
                    <h2
                        class="mb-4 text-3xl font-bold text-foreground md:text-4xl"
                    >
                        Track All Dimensions of Wellness
                    </h2>
                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                        True wellness is multi-dimensional. IOMeW tracks all
                        four pillars with 45+ activities.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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
                                    20 activities | 10-50 points
                                </p>
                            </div>
                        </div>
                        <p class="text-muted-foreground">
                            Running, gym, HIIT, yoga, swimming, cycling, sports,
                            martial arts, and more. Points based on MET
                            (Metabolic Equivalent) values.
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
                                    9 activities | 10-25 points
                                </p>
                            </div>
                        </div>
                        <p class="text-muted-foreground">
                            Healthy meals, meal prep, fruits & vegetables,
                            hydration, protein goals, avoiding junk food, and
                            supplements.
                        </p>
                    </div>

                    <!-- Wellness -->
                    <div
                        class="rounded-2xl border border-blue-500/20 bg-gradient-to-br from-blue-500/10 to-blue-500/5 p-8"
                    >
                        <div class="mb-6 flex items-center gap-4">
                            <div
                                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-500"
                            >
                                <Moon class="h-7 w-7 text-white" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-foreground">
                                    🌟 Wellness
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    8 activities | 15-30 points
                                </p>
                            </div>
                        </div>
                        <p class="text-muted-foreground">
                            Quality sleep, early wake-ups, sunlight exposure,
                            nature time, cold showers, sauna, and social
                            connections.
                        </p>
                    </div>

                    <!-- Mindfulness -->
                    <div
                        class="rounded-2xl border border-purple-500/20 bg-gradient-to-br from-purple-500/10 to-purple-500/5 p-8"
                    >
                        <div class="mb-6 flex items-center gap-4">
                            <div
                                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-500"
                            >
                                <Brain class="h-7 w-7 text-white" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-foreground">
                                    🧘 Mindfulness
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    8 activities | 15-25 points
                                </p>
                            </div>
                        </div>
                        <p class="text-muted-foreground">
                            Meditation, breathing exercises, journaling,
                            reading, prayer, digital detox, learning, and
                            creative activities.
                        </p>
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
                        Compete on Global Leaderboards
                    </h2>
                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                        Like a sports organization, but for health. See where
                        you rank today, this season, and all-time.
                    </p>
                </div>

                <div
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        class="rounded-xl border border-border/50 bg-card/50 p-6 text-center"
                    >
                        <div class="mb-2 flex justify-center">
                            <Zap class="h-8 w-8 text-yellow-500" />
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
                            <TrendingUp class="h-8 w-8 text-blue-500" />
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
                            <Trophy class="h-8 w-8 text-primary" />
                        </div>
                        <h3 class="mb-1 font-bold text-foreground">
                            Season (Q1-Q4)
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            Quarterly leaderboards
                        </p>
                    </div>

                    <div
                        class="rounded-xl border border-border/50 bg-card/50 p-6 text-center"
                    >
                        <div class="mb-2 flex justify-center">
                            <Award class="h-8 w-8 text-purple-500" />
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
                        How IOMeW Works
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
                                45+ options across all health categories.
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
                                Log Your Activities
                            </h3>
                            <p class="text-muted-foreground">
                                Record daily activities—workouts, meals, sleep,
                                mindfulness. Add notes and optional proof.
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
                                Compete on daily, seasonal, and yearly
                                leaderboards. Track your rank: "2025 Q1 #11".
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
                    Join IOMeW today and turn "I should prioritize wellness"
                    into "I compete in wellness." Track, compete, and earn your
                    rank.
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
                        {{ appName }} - I Owe Me Wellness
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
