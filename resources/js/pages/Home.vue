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
import { computed, onUnmounted, ref, shallowRef } from 'vue';

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
    is_inactive?: boolean;
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

// State (using shallowRef for better performance)
const selectedHabit = shallowRef<Habit | null>(null);
const showHabitModal = ref(false);

// Computed Properties
const todayPoints = computed(() =>
    props.today_activities.reduce(
        (sum, activity) => sum + activity.points_earned,
        0,
    ),
);

const streakProgress = computed(() =>
    getTierProgress(props.user.current_streak),
);

// Tier name mapping (memoized)
const tierNameMap = computed(
    () =>
        ({
            Newcomer: t('streak.newcomer'),
            Beginner: t('streak.beginner'),
            Regular: t('streak.regular'),
            Committed: t('streak.committed'),
            Dedicated: t('streak.dedicated'),
            Expert: t('streak.expert'),
            Master: t('streak.master'),
            Legend: t('streak.legend'),
        }) as Record<string, string>,
);

const translatedTierName = computed(
    () =>
        tierNameMap.value[props.user.streak_tier.name] ||
        props.user.streak_tier.name,
);

const streakMessage = computed(() => {
    const message = getNextTierMessage(props.user.current_streak);

    if (message === 'Legend status achieved!') {
        return t('home.legend_status_achieved');
    }

    const match = message.match(/(\d+) days to (.+) \((\d+(?:\.\d+)?)×\)/);
    if (match) {
        const [, days, tierName, multiplier] = match;
        const translatedTierName = tierNameMap.value[tierName] || tierName;
        return `${days} ${t('home.days_to')} ${translatedTierName} (${multiplier}×)`;
    }

    return message;
});

// Navigation Methods
const navigateToRankings = () => router.visit('/rankings');
const navigateToHabits = () => router.visit('/settings/habits');

// Computed properties for better performance
const hasHabits = computed(() => props.habits && props.habits.length > 0);
const recentActivitiesSlice = computed(() =>
    props.recent_activities.slice(0, 5),
);

// Conditional navigation for activities
const handleActivityAction = () => {
    if (hasHabits.value) {
        handleLogActivity();
    } else {
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

// State for enhanced click handling
const clickTimeout = ref<number | null>(null);
const isDoubleClick = ref(false);
const hoveredHabit = ref<number | null>(null);

// Constants
const CLICK_DELAY = 200;

// Habit interaction handlers
const habitHandlers = {
    // Handle single click on a habit
    click: (habit: Habit) => {
        if (clickTimeout.value) {
            clearTimeout(clickTimeout.value);
            clickTimeout.value = null;
        }

        isDoubleClick.value = false;

        clickTimeout.value = setTimeout(() => {
            if (!isDoubleClick.value) {
                if (habit.notes) {
                    openHabitModal(habit);
                } else if (!habit.has_activity_today) {
                    openLogActivityModal(habit);
                }
            }
        }, CLICK_DELAY);
    },

    // Handle double click on a habit
    doubleClick: (habit: Habit, event: MouseEvent) => {
        if (clickTimeout.value) {
            clearTimeout(clickTimeout.value);
            clickTimeout.value = null;
        }

        isDoubleClick.value = true;
        event.preventDefault();
        event.stopPropagation();

        // Prevent text selection
        window.getSelection()?.removeAllRanges();

        if (!habit.has_activity_today) {
            openLogActivityModal(habit);
        }
    },

    // Handle hover states
    hover: (habitId: number | null) => {
        hoveredHabit.value = habitId;
    },

    // Handle mousedown to prevent text selection
    mouseDown: (event: MouseEvent) => {
        if (event.button === 0) {
            event.preventDefault();
        }
    },
};

// Habit utility functions
const habitUtils = {
    // Get tooltip text for habit badges
    getTooltip: (habit: Habit): string => {
        const tooltipMap = {
            completedWithNotes: t('home.habit_completed_with_notes'),
            completed: t('home.habit_completed'),
            clickToViewNotes: t('home.habit_click_to_view_notes'),
            clickToLog: t('home.habit_click_to_log'),
        };

        // If habit is completed today and has notes, show completed with notes
        if (habit.has_activity_today && habit.notes) {
            return tooltipMap.completedWithNotes;
        }
        // If habit is completed today but no notes, just show completed
        if (habit.has_activity_today) {
            return tooltipMap.completed;
        }
        // If habit has notes but not completed today, clicking will open notes modal
        if (habit.notes) {
            return tooltipMap.clickToViewNotes;
        }
        // If habit not completed today and no notes, clicking will open log modal
        return tooltipMap.clickToLog;
    },

    // Get dynamic classes for habit badges
    getClasses: (habit: Habit) => {
        const baseClasses =
            'border-border/20 bg-card px-2 py-1 text-xs text-card-foreground transition-all duration-200 hover:border-primary/30 select-none sm:px-3 sm:py-1.5 sm:text-sm';

        const isCompleted = habit.has_activity_today;
        const isInteractive = habit.notes || !isCompleted;
        const isHovered = hoveredHabit.value === habit.id && !isCompleted;

        return [
            baseClasses,
            {
                'cursor-pointer': isInteractive,
                'cursor-default': !isInteractive,
                'opacity-60': isCompleted && !habit.notes,
                'hover:bg-primary/5': !isCompleted,
                'hover:scale-105': !isCompleted,
                'hover:shadow-md': !isCompleted,
                'ring-2 ring-primary/20': isHovered,
                'bg-muted/50 border-muted text-muted-foreground': isCompleted,
                'hover:bg-muted/70': isCompleted,
            },
        ];
    },
};

// Cleanup function
const cleanup = () => {
    if (clickTimeout.value) {
        clearTimeout(clickTimeout.value);
        clickTimeout.value = null;
    }
};

// Cleanup on component unmount
onUnmounted(cleanup);

// Helper Methods
</script>

<template>
    <AppLayout :dir="isRTL ? 'rtl' : 'ltr'">
        <SEO v-bind="seoConfigs.home" />

        <div class="container mx-auto px-4 py-4 sm:py-6 lg:py-8">
            <!-- Welcome Header -->
            <div class="mb-6 sm:mb-8">
                <h1
                    class="mb-2 text-2xl font-bold text-foreground sm:text-3xl lg:text-4xl"
                >
                    {{ t('home.welcome') }}, {{ user.name }}! 👋
                </h1>
                <p
                    class="text-sm text-muted-foreground sm:text-base lg:text-lg"
                >
                    {{ t('home.description') }}
                </p>
            </div>

            <div
                class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-3 lg:gap-8"
            >
                <!-- Left Column: Today's Activities -->
                <div class="space-y-4 sm:space-y-6 lg:col-span-2">
                    <!-- My Activities -->
                    <Card>
                        <CardHeader>
                            <div
                                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div>
                                    <CardTitle class="text-lg sm:text-xl">{{
                                        t('settings.habits')
                                    }}</CardTitle>
                                    <CardDescription class="text-sm">
                                        {{ t('home.activities_description') }}
                                    </CardDescription>
                                </div>
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        class="w-full max-sm:order-2 sm:w-auto"
                                        @click="navigateToHabits"
                                    >
                                        {{ t('common.edit') }}
                                    </Button>
                                    <Button
                                        size="sm"
                                        class="w-full sm:w-auto"
                                        @click="handleLogActivity"
                                    >
                                        <Plus class="h-4 w-4" />
                                        {{ t('home.log_activity') }}
                                    </Button>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div
                                v-if="hasHabits"
                                class="flex cursor-default flex-wrap gap-2"
                            >
                                <Badge
                                    v-for="habit in habits"
                                    :key="habit.id"
                                    variant="outline"
                                    :class="habitUtils.getClasses(habit)"
                                    :title="habitUtils.getTooltip(habit)"
                                    @click="habitHandlers.click(habit)"
                                    @dblclick="
                                        habitHandlers.doubleClick(habit, $event)
                                    "
                                    @mousedown="habitHandlers.mouseDown"
                                    @mouseenter="habitHandlers.hover(habit.id)"
                                    @mouseleave="habitHandlers.hover(null)"
                                >
                                    <span class="mr-1 sm:mr-1.5">{{
                                        habit.icon
                                    }}</span>
                                    <span class="truncate">{{
                                        habit.name
                                    }}</span>
                                    <CheckCircle
                                        v-if="habit.has_activity_today"
                                        class="ml-1 h-3 w-3 text-green-500 sm:ml-1.5 dark:text-green-400"
                                    />
                                    <Info
                                        v-if="habit.notes"
                                        @click.stop="openHabitModal(habit)"
                                        class="ml-1 h-3 w-3 cursor-pointer text-primary transition-colors hover:text-primary/80 sm:ml-1.5"
                                    />
                                </Badge>
                            </div>
                            <div
                                v-else
                                class="py-6 text-center text-muted-foreground sm:py-8"
                            >
                                <Activity
                                    class="mx-auto mb-3 h-10 w-10 opacity-50 sm:h-12 sm:w-12"
                                />
                                <p class="text-sm sm:text-base">
                                    {{ t('home.no_habits_selected') }}
                                </p>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    class="mt-3 w-full sm:w-auto"
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
                            <CardTitle class="text-lg sm:text-xl">{{
                                t('home.today_activities')
                            }}</CardTitle>
                            <CardDescription class="text-sm">
                                {{ today_activities.length }}
                                {{ t('home.activities_count') }} •
                                {{ todayPoints }} {{ t('home.points_count') }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div
                                v-if="today_activities.length > 0"
                                class="space-y-3 sm:space-y-4"
                            >
                                <div
                                    v-for="activity in today_activities"
                                    :key="activity.id"
                                    class="flex items-start justify-between gap-2 rounded-lg border border-border/50 bg-card/50 p-3 transition-colors hover:border-primary/30 sm:gap-3 sm:p-4"
                                >
                                    <div class="min-w-0 flex-1">
                                        <div
                                            class="mb-1 flex flex-col gap-2 sm:flex-row sm:items-center"
                                        >
                                            <span
                                                class="truncate font-medium text-foreground"
                                            >
                                                {{ activity.habit_name }}
                                            </span>
                                            <Badge
                                                variant="secondary"
                                                class="w-fit text-xs"
                                            >
                                                +{{ activity.points_earned }}
                                                {{ t('points.short') }}
                                            </Badge>
                                        </div>
                                        <p
                                            v-if="activity.notes"
                                            class="text-xs text-muted-foreground sm:text-sm"
                                        >
                                            {{ activity.notes }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-else
                                class="py-8 text-center text-muted-foreground sm:py-12"
                            >
                                <Calendar
                                    class="mx-auto mb-3 h-10 w-10 opacity-50 sm:h-12 sm:w-12"
                                />
                                <p class="mb-2 text-sm sm:text-base">
                                    {{ t('home.no_activities_today') }}
                                </p>
                                <p class="mb-4 text-xs sm:text-sm">
                                    {{ t('home.start_tracking') }}
                                </p>
                                <Button
                                    size="sm"
                                    class="w-full sm:w-auto"
                                    @click="handleActivityAction"
                                    :variant="hasHabits ? 'default' : 'outline'"
                                >
                                    {{
                                        hasHabits
                                            ? t('home.log_activity')
                                            : t('common.edit')
                                    }}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column: Recent Activities & Quick Actions -->
                <div class="space-y-4 sm:space-y-6">
                    <!-- Streak Display -->
                    <Card>
                        <CardHeader>
                            <CardTitle
                                class="flex items-center gap-2 text-lg sm:text-xl"
                            >
                                <Flame
                                    class="h-4 w-4 text-orange-500 sm:h-5 sm:w-5"
                                />
                                {{ t('home.your_streak') }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3 sm:space-y-4">
                            <!-- Current Streak -->
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div>
                                    <div
                                        class="text-xl font-bold text-orange-600 sm:text-2xl dark:text-orange-500"
                                    >
                                        {{ user.current_streak }}
                                    </div>
                                    <div
                                        class="text-xs text-muted-foreground sm:text-sm"
                                    >
                                        {{
                                            user.current_streak === 1
                                                ? t('home.day')
                                                : t('home.days')
                                        }}
                                        {{ t('home.in_a_row') }}
                                    </div>
                                </div>
                                <div class="text-left sm:text-right">
                                    <div
                                        class="text-sm font-medium text-primary"
                                    >
                                        {{ user.streak_tier.icon }}
                                        <span class="hidden sm:inline">{{
                                            translatedTierName
                                        }}</span>
                                        <span class="sm:hidden">{{
                                            translatedTierName
                                        }}</span>
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
                                    class="flex flex-col gap-1 text-xs text-muted-foreground sm:flex-row sm:justify-between"
                                >
                                    <span class="truncate">{{
                                        streakMessage
                                    }}</span>
                                    <span
                                        v-if="
                                            user.longest_streak >
                                            user.current_streak
                                        "
                                        class="text-xs"
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
                            <CardTitle
                                class="flex items-center gap-2 text-lg sm:text-xl"
                            >
                                <TrendingUp
                                    class="h-4 w-4 text-primary sm:h-5 sm:w-5"
                                />
                                {{ t('home.your_rankings') }}
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-2 sm:space-y-3">
                            <div
                                v-if="user.season_rank"
                                class="flex items-center justify-between rounded-lg border border-border/50 bg-card/50 p-2 sm:p-3"
                            >
                                <div class="flex min-w-0 items-center gap-2">
                                    <Award
                                        class="h-3 w-3 text-primary sm:h-4 sm:w-4"
                                    />
                                    <Badge
                                        variant="outline"
                                        class="truncate text-xs"
                                    >
                                        {{
                                            isRTL
                                                ? `${t('rankings.q4_rankings')} ${user.season_rank.year}`
                                                : `${user.season_rank.season} ${user.season_rank.year}`
                                        }}
                                    </Badge>
                                </div>
                                <span
                                    class="text-lg font-bold text-primary sm:text-2xl"
                                >
                                    #{{ user.season_rank.rank }}
                                </span>
                            </div>
                            <div
                                v-if="user.year_rank"
                                class="flex items-center justify-between rounded-lg border border-border/50 bg-card/50 p-2 sm:p-3"
                            >
                                <div class="flex min-w-0 items-center gap-2">
                                    <Trophy
                                        class="h-3 w-3 text-amber-600 sm:h-4 sm:w-4 dark:text-amber-500"
                                    />
                                    <Badge
                                        variant="outline"
                                        class="truncate text-xs"
                                    >
                                        {{ user.year_rank.year }}
                                        {{ t('home.year') }}
                                    </Badge>
                                </div>
                                <span
                                    class="text-lg font-bold text-amber-700 sm:text-2xl dark:text-amber-500"
                                >
                                    #{{ user.year_rank.rank }}
                                </span>
                            </div>
                            <Button
                                class="mt-3 w-full sm:mt-4"
                                variant="outline"
                                size="sm"
                                @click="navigateToRankings"
                            >
                                <TrendingUp class="h-3 w-3 sm:h-4 sm:w-4" />
                                {{ t('home.view_all_rankings') }}
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Recent Activities -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg sm:text-xl">{{
                                t('home.recent_activities')
                            }}</CardTitle>
                            <CardDescription class="text-sm">{{
                                t('home.last_7_days')
                            }}</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div
                                v-if="recent_activities.length > 0"
                                class="space-y-2 sm:space-y-3"
                            >
                                <div
                                    v-for="activity in recentActivitiesSlice"
                                    :key="activity.id"
                                    :class="[
                                        'flex items-center justify-between text-xs sm:text-sm',
                                        activity.is_inactive
                                            ? 'opacity-75'
                                            : '',
                                    ]"
                                >
                                    <div
                                        class="flex min-w-0 flex-1 items-center gap-2"
                                    >
                                        <span
                                            class="flex-shrink-0 text-sm sm:text-lg"
                                            >{{ activity.icon }}</span
                                        >
                                        <div class="min-w-0 flex-1">
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <p
                                                    class="truncate font-medium text-foreground"
                                                >
                                                    {{ activity.habit_name }}
                                                </p>
                                                <span
                                                    v-if="activity.is_inactive"
                                                    class="inline-flex items-center rounded-full bg-orange-100 px-1.5 py-0.5 text-xs font-medium text-orange-800 dark:bg-orange-900/30 dark:text-orange-300"
                                                >
                                                    {{
                                                        t(
                                                            'activity.inactive_habit',
                                                        )
                                                    }}
                                                </span>
                                            </div>
                                            <p
                                                class="text-xs text-muted-foreground"
                                            >
                                                {{ activity.date }}
                                            </p>
                                        </div>
                                    </div>
                                    <Badge
                                        variant="secondary"
                                        class="ml-1 flex-shrink-0 text-xs sm:ml-2"
                                    >
                                        +{{ activity.points }}
                                    </Badge>
                                </div>
                            </div>
                            <div
                                v-else
                                class="py-6 text-center text-muted-foreground sm:py-8"
                            >
                                <p class="text-xs sm:text-sm">
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
            <DialogContent
                class="w-[calc(100%-2rem)] max-w-sm sm:mx-0 sm:max-w-md lg:max-w-lg"
            >
                <div :dir="isRTL ? 'rtl' : 'ltr'">
                    <DialogHeader class="space-y-2 sm:space-y-3">
                        <DialogTitle
                            class="flex items-center gap-2 text-base sm:text-lg lg:text-xl"
                            :class="isRTL ? 'flex-row-reverse' : ''"
                        >
                            <span
                                class="flex-shrink-0 text-lg sm:text-xl lg:text-2xl"
                                >{{ selectedHabit?.icon }}</span
                            >
                            <span class="min-w-0 truncate">{{
                                selectedHabit?.name
                            }}</span>
                        </DialogTitle>
                        <DialogDescription class="text-xs sm:text-sm">
                            {{ t('home.personal_notes') }}
                        </DialogDescription>
                    </DialogHeader>
                    <div class="mt-2 sm:mt-3 lg:mt-4">
                        <div class="rounded-lg bg-muted/50 p-3 sm:p-4 lg:p-5">
                            <p
                                class="text-xs leading-relaxed text-muted-foreground sm:text-sm lg:text-base"
                            >
                                {{ selectedHabit?.notes }}
                            </p>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
