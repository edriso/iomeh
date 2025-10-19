<script setup lang="ts">
import SEO from '@/components/SEO.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useLogActivity } from '@/composables/useLogActivity';
import { useTranslations } from '@/composables/useTranslations';
import { seoConfigs } from '@/config/seo';
import AppLayout from '@/layouts/AppLayout.vue';
import { getNextTierMessage, getTierProgress } from '@/utils/streakTiers';
import { router } from '@inertiajs/vue3';
import {
    Activity,
    Award,
    Calendar,
    CheckCircle,
    Flame,
    Info,
    Plus,
    TrendingUp,
    Trophy,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Types
interface TodayActivity {
    id: number;
    habit_name: string;
    points_earned: number;
    notes?: string;
}

interface RecentActivity {
    id: number;
    date: string;
    habit_name: string;
    icon: string;
    points: number;
    notes?: string;
}

interface Habit {
    id: number;
    name: string;
    icon: string;
    custom_icon?: string | null;
    category: string;
    activity_type_id: number | null;
    base_points: number;
    has_activity_today: boolean;
    notes?: string | null;
}

interface User {
    id: number;
    username: string;
    name: string;
    avatar?: string;
    current_streak: number;
    longest_streak: number;
    streak_tier: {
        name: string;
        multiplier: number;
        icon: string;
    };
    season_rank?: {
        rank: number;
        season: string;
        year: number;
    };
    year_rank?: {
        rank: number;
        year: number;
    };
}

interface Props {
    user: User;
    habits: Habit[];
    today_activities: TodayActivity[];
    recent_activities: RecentActivity[];
}

// Props
const props = defineProps<Props>();

// Use translations with reactive locale
const { t, isRTL } = useTranslations();

// Composables
const { openLogActivityModal } = useLogActivity();

// State
const selectedHabit = ref<Habit | null>(null);
const showHabitModal = ref(false);

// Computed Properties
const todayPoints = computed(() =>
    props.today_activities.reduce(
        (sum, activity) => sum + activity.points_earned,
        0,
    ),
);

// Computed Properties
const streakProgress = computed(() =>
    getTierProgress(props.user.current_streak),
);

// Translate streak tier name
const translatedTierName = computed(() => {
    const tierMap: Record<string, string> = {
        Newcomer: t('streak.newcomer'),
        Beginner: t('streak.beginner'),
        Regular: t('streak.regular'),
        Committed: t('streak.committed'),
        Dedicated: t('streak.dedicated'),
        Expert: t('streak.expert'),
        Master: t('streak.master'),
        Legend: t('streak.legend'),
    };

    return tierMap[props.user.streak_tier.name] || props.user.streak_tier.name;
});
const streakMessage = computed(() => {
    const message = getNextTierMessage(props.user.current_streak);

    // Translate the message
    if (message === 'Legend status achieved!') {
        return t('home.legend_status_achieved');
    }

    // Parse the message format: "X days to TierName (X×)"
    const match = message.match(/(\d+) days to (.+) \((\d+(?:\.\d+)?)×\)/);
    if (match) {
        const [, days, tierName, multiplier] = match;

        // Map tier names to translation keys
        const tierMap: Record<string, string> = {
            Newcomer: t('streak.newcomer'),
            Beginner: t('streak.beginner'),
            Regular: t('streak.regular'),
            Committed: t('streak.committed'),
            Dedicated: t('streak.dedicated'),
            Expert: t('streak.expert'),
            Master: t('streak.master'),
            Legend: t('streak.legend'),
        };

        const translatedTierName = tierMap[tierName] || tierName;
        return `${days} ${t('home.days_to')} ${translatedTierName} (${multiplier}×)`;
    }

    // If no match, return original message (fallback)
    return message;
});

// Navigation Methods
const navigateToRankings = () => router.visit('/rankings');
const navigateToHabits = () => router.visit('/settings/habits');

// Conditional navigation for activities
const handleActivityAction = () => {
    if (props.habits && props.habits.length > 0) {
        // User has habits, open activity modal
        handleLogActivity();
    } else {
        // User has no habits, go to habits settings
        navigateToHabits();
    }
};

// Modal Methods
const openHabitModal = (habit: Habit) => {
    selectedHabit.value = habit;
    showHabitModal.value = true;
};

const closeHabitModal = () => {
    showHabitModal.value = false;
    selectedHabit.value = null;
};

// Activity Methods
const handleLogActivity = () => openLogActivityModal();

// Helper Methods
</script>

<template>
    <AppLayout :dir="isRTL ? 'rtl' : 'ltr'">
        <SEO v-bind="seoConfigs.home" />

        <div class="container mx-auto px-4 py-8">
            <!-- Welcome Header -->
            <div class="mb-8">
                <h1 class="mb-2 text-3xl font-bold text-foreground lg:text-4xl">
                    {{ t('home.welcome') }}, {{ user.name }}! 👋
                </h1>
                <p class="text-muted-foreground lg:text-lg">
                    {{ t('home.description') }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Left Column: Today's Activities -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- My Activities -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div>
                                    <CardTitle>{{
                                        t('settings.habits')
                                    }}</CardTitle>
                                    <CardDescription>
                                        {{ t('home.activities_description') }}
                                    </CardDescription>
                                </div>
                                <div class="flex gap-2">
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        @click="navigateToHabits"
                                    >
                                        {{ t('common.edit') }}
                                    </Button>
                                    <Button
                                        size="sm"
                                        @click="handleLogActivity"
                                    >
                                        <Plus class="h-4 w-4" />
                                        {{ t('home.add_activity') }}
                                    </Button>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div
                                v-if="habits && habits.length > 0"
                                class="flex cursor-default flex-wrap gap-2"
                            >
                                <Badge
                                    v-for="habit in habits"
                                    :key="habit.id"
                                    variant="outline"
                                    class="border-secondary/20 bg-secondary px-3 py-1.5 text-sm text-secondary-foreground transition-colors hover:border-primary/30"
                                    :class="{
                                        'cursor-pointer': habit.notes,
                                        'cursor-default': !habit.notes,
                                    }"
                                    @click="
                                        habit.notes
                                            ? openHabitModal(habit)
                                            : null
                                    "
                                >
                                    <span class="mr-1.5">{{ habit.icon }}</span>
                                    {{ habit.name }}
                                    <CheckCircle
                                        v-if="habit.has_activity_today"
                                        class="ml-1.5 h-3 w-3"
                                    />
                                    <Info
                                        v-if="habit.notes"
                                        class="ml-1.5 h-3 w-3"
                                    />
                                </Badge>
                            </div>
                            <div
                                v-else
                                class="py-8 text-center text-muted-foreground"
                            >
                                <Activity
                                    class="mx-auto mb-3 h-12 w-12 opacity-50"
                                />
                                <p>{{ t('home.no_habits_selected') }}</p>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    class="mt-3"
                                    @click="navigateToHabits"
                                >
                                    {{ t('home.select_activities') }}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Today's Activities -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{
                                t('home.today_activities')
                            }}</CardTitle>
                            <CardDescription>
                                {{ today_activities.length }}
                                {{ t('home.activities_count') }} •
                                {{ todayPoints }} {{ t('home.points_count') }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div
                                v-if="today_activities.length > 0"
                                class="space-y-4"
                            >
                                <div
                                    v-for="activity in today_activities"
                                    :key="activity.id"
                                    class="flex items-start justify-between gap-3 rounded-lg border border-border/50 bg-card/50 p-4 transition-colors hover:border-primary/30"
                                >
                                    <div class="flex-1">
                                        <div
                                            class="mb-1 flex items-center gap-2"
                                        >
                                            <span
                                                class="font-medium text-foreground"
                                            >
                                                {{ activity.habit_name }}
                                            </span>
                                            <Badge
                                                variant="secondary"
                                                class="text-xs"
                                            >
                                                +{{ activity.points_earned }}
                                                {{ t('points.short') }}
                                            </Badge>
                                        </div>
                                        <p
                                            v-if="activity.notes"
                                            class="text-sm text-muted-foreground"
                                        >
                                            {{ activity.notes }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-else
                                class="py-12 text-center text-muted-foreground"
                            >
                                <Calendar
                                    class="mx-auto mb-3 h-12 w-12 opacity-50"
                                />
                                <p class="mb-2">
                                    {{ t('home.no_activities_today') }}
                                </p>
                                <p class="mb-4 text-sm">
                                    {{ t('home.start_tracking') }}
                                </p>
                                <Button
                                    size="sm"
                                    @click="handleActivityAction"
                                    :variant="
                                        props.habits && props.habits.length > 0
                                            ? 'default'
                                            : 'outline'
                                    "
                                >
                                    {{
                                        props.habits && props.habits.length > 0
                                            ? t('home.add_activity')
                                            : t('home.edit_activities')
                                    }}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column: Recent Activities & Quick Actions -->
                <div class="space-y-6">
                    <!-- Streak Display -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Flame class="h-5 w-5 text-orange-500" />
                                {{ t('home.your_streak') }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Current Streak -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <div
                                        class="text-2xl font-bold text-orange-600 dark:text-orange-500"
                                    >
                                        {{ user.current_streak }}
                                    </div>
                                    <div class="text-sm text-muted-foreground">
                                        {{
                                            user.current_streak === 1
                                                ? t('home.day')
                                                : t('home.days')
                                        }}
                                        {{ t('home.in_a_row') }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div
                                        class="text-sm font-medium text-primary"
                                    >
                                        {{ user.streak_tier.icon }}
                                        {{ translatedTierName }}
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        {{ user.streak_tier.multiplier }}×
                                        {{ t('home.multiplier') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="space-y-2">
                                <div
                                    class="h-2 w-full rounded-full bg-orange-200/50 dark:bg-orange-800/30"
                                >
                                    <div
                                        class="h-2 rounded-full bg-gradient-to-r from-orange-500 to-red-500 transition-all duration-500"
                                        :style="{ width: `${streakProgress}%` }"
                                    ></div>
                                </div>
                                <div
                                    class="flex justify-between text-xs text-muted-foreground"
                                >
                                    <span>{{ streakMessage }}</span>
                                    <span
                                        v-if="
                                            user.longest_streak >
                                            user.current_streak
                                        "
                                    >
                                        {{ t('home.best') }}:
                                        {{ user.longest_streak }}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Rankings Quick View -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <TrendingUp class="h-5 w-5 text-primary" />
                                {{ t('home.your_rankings') }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div
                                v-if="user.season_rank"
                                class="flex items-center justify-between rounded-lg border border-border/50 bg-card/50 p-3"
                            >
                                <div class="flex items-center gap-2">
                                    <Award class="h-4 w-4 text-primary" />
                                    <Badge variant="outline" class="text-xs">
                                        {{
                                            isRTL
                                                ? `${t('rankings.q4_rankings')} ${user.season_rank.year}`
                                                : `${user.season_rank.season} ${user.season_rank.year}`
                                        }}
                                    </Badge>
                                </div>
                                <span class="text-2xl font-bold text-primary">
                                    #{{ user.season_rank.rank }}
                                </span>
                            </div>
                            <div
                                v-if="user.year_rank"
                                class="flex items-center justify-between rounded-lg border border-border/50 bg-card/50 p-3"
                            >
                                <div class="flex items-center gap-2">
                                    <Trophy
                                        class="h-4 w-4 text-amber-600 dark:text-amber-500"
                                    />
                                    <Badge variant="outline" class="text-xs">
                                        {{ user.year_rank.year }}
                                        {{ t('home.year') }}
                                    </Badge>
                                </div>
                                <span
                                    class="text-2xl font-bold text-amber-700 dark:text-amber-500"
                                >
                                    #{{ user.year_rank.rank }}
                                </span>
                            </div>
                            <Button
                                class="mt-4 w-full"
                                variant="outline"
                                @click="navigateToRankings"
                            >
                                <TrendingUp class="h-4 w-4" />
                                {{ t('home.view_all_rankings') }}
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Recent Activities -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{
                                t('home.recent_activities')
                            }}</CardTitle>
                            <CardDescription>{{
                                t('home.last_7_days')
                            }}</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div
                                v-if="recent_activities.length > 0"
                                class="space-y-3"
                            >
                                <div
                                    v-for="activity in recent_activities.slice(
                                        0,
                                        5,
                                    )"
                                    :key="activity.id"
                                    class="flex items-center justify-between text-sm"
                                >
                                    <div
                                        class="flex min-w-0 flex-1 items-center gap-2"
                                    >
                                        <span class="flex-shrink-0 text-lg">{{
                                            activity.icon
                                        }}</span>
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="truncate font-medium text-foreground"
                                            >
                                                {{ activity.habit_name }}
                                            </p>
                                            <p
                                                class="text-xs text-muted-foreground"
                                            >
                                                {{ activity.date }}
                                            </p>
                                        </div>
                                    </div>
                                    <Badge
                                        variant="secondary"
                                        class="ml-2 flex-shrink-0"
                                    >
                                        +{{ activity.points }}
                                    </Badge>
                                </div>
                            </div>
                            <div
                                v-else
                                class="py-8 text-center text-muted-foreground"
                            >
                                <p class="text-sm">
                                    {{ t('home.no_recent_activities') }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Habit Notes Modal -->
        <Dialog v-model:open="showHabitModal" @update:open="closeHabitModal">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <span class="text-2xl">{{ selectedHabit?.icon }}</span>
                        {{ selectedHabit?.name }}
                    </DialogTitle>
                    <DialogDescription>
                        {{ t('home.personal_notes') }}
                    </DialogDescription>
                </DialogHeader>
                <div class="mt-4">
                    <div class="rounded-lg bg-muted/50 p-4">
                        <p
                            class="text-sm leading-relaxed text-muted-foreground"
                        >
                            {{ selectedHabit?.notes }}
                        </p>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
