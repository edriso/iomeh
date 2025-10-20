<script setup lang="ts">
import ActivityCalendarHeatmap from '@/components/ActivityCalendarHeatmap.vue';
import EditActivityModal from '@/components/EditActivityModal.vue';
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
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { useNumberFormat } from '@/composables/useNumberFormat';
import { useTranslations } from '@/composables/useTranslations';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    Activity,
    Award,
    Edit,
    Flame,
    Link as LinkIcon,
    Lock,
    Trophy,
} from 'lucide-vue-next';
import { computed, nextTick, ref } from 'vue';

interface ProfileUser {
    id: number;
    name: string;
    username: string;
    bio?: string;
    website_url?: string;
    avatar?: string;
    lifetime_points: number;
    current_season_points: number;
    current_year_points: number;
    current_streak: number;
    longest_streak: number;
    last_activity_date?: string;
    created_at: string;
    week_starts_on?: number;
}

interface RecentActivity {
    id: number;
    activity_type_name: string;
    activity_type_icon: string;
    custom_name: string;
    date: string;
    points_earned: number;
    notes?: string;
    memory_url?: string;
    is_inactive?: boolean;
}

interface CalendarDay {
    date: string;
    activities_count: number;
    points: number;
}

interface Habit {
    id: number;
    name: string;
    icon: string;
    category: string;
    activity_type_id: number;
    base_points: number;
    has_activity_today: boolean;
}

interface RankingHistory {
    id: number;
    season: number | null;
    year: number;
    points: number;
    rank: number;
    season_name: string;
    display_name: string;
}

interface Props {
    user?: ProfileUser;
    recent_activities?: RecentActivity[];
    calendar_data?: CalendarDay[];
    stats?: {
        lifetime_points: number;
        current_season_points: number;
        current_year_points: number;
        current_streak: number;
        longest_streak: number;
        total_activities: number;
        active_days: number;
        habits_count: number;
    };
    is_own_profile?: boolean;
    habits?: Habit[];
    ranking_histories?: RankingHistory[];
}

const props = withDefaults(defineProps<Props>(), {
    user: undefined,
    recent_activities: () => [],
    calendar_data: () => [],
    stats: () => ({
        lifetime_points: 0,
        current_season_points: 0,
        current_year_points: 0,
        current_streak: 0,
        longest_streak: 0,
        total_activities: 0,
        active_days: 0,
        habits_count: 0,
    }),
    is_own_profile: false,
    habits: () => [],
    ranking_histories: () => [],
});

const { formatNumber } = useNumberFormat();
const { t, isRTL } = useTranslations();

// Arabic pluralization for days
const getDaysText = (count: number) => {
    if (isRTL.value) {
        // Arabic pluralization rules
        if (count === 1) {
            return t('days.single'); // يوم
        } else if (count >= 3 && count <= 9) {
            return t('days.plural'); // أيام
        } else {
            return t('days.plural'); // أيام (for 0, 2, 10+)
        }
    } else {
        // English pluralization
        return count === 1 ? t('days.single') : t('days.plural');
    }
};

// Transform calendar data for heatmap
const heatmapDays = computed(() => {
    return (
        props.calendar_data?.map((day) => ({
            date: day.date,
            count: day.activities_count,
        })) || []
    );
});

// Selected date activities (only for own profile)
const selectedDate = ref<string | null>(null);
const selectedDateActivities = ref<RecentActivity[]>([]);
const loadingActivities = ref(false);
const activitiesContainer = ref<HTMLElement | null>(null);

// Cache for fetched activities by date
const activitiesCache = ref<Record<string, RecentActivity[]>>({});

// Edit activity modal state
const showEditModal = ref(false);
const selectedActivity = ref<RecentActivity | null>(null);

const handleDayClick = async (date: string) => {
    if (!props.is_own_profile) return; // Only for own profile

    // If clicking the same date, toggle it (close the panel)
    if (selectedDate.value === date) {
        selectedDate.value = null;
        selectedDateActivities.value = [];
        return;
    }

    // Set the selected date
    selectedDate.value = date;

    // Check if we have cached activities for this date
    if (activitiesCache.value[date]) {
        selectedDateActivities.value = activitiesCache.value[date];
        // Scroll to activities after DOM update
        await nextTick();
        // Add a small delay to ensure the content is fully rendered
        setTimeout(() => {
            scrollToActivities();
        }, 250);
        return;
    }

    // Fetch activities for the selected date
    loadingActivities.value = true;

    try {
        const response = await fetch(`/api/activities/date/${date}`, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.ok) {
            const data = await response.json();
            const activities = data.activities || [];

            // Cache the activities
            activitiesCache.value[date] = activities;
            selectedDateActivities.value = activities;

            // Scroll to activities after DOM update
            await nextTick();
            // Add a small delay to ensure the content is fully rendered
            setTimeout(() => {
                scrollToActivities();
            }, 100);
        }
    } catch {
        selectedDateActivities.value = [];
    } finally {
        loadingActivities.value = false;
    }
};

// Function to scroll to activities container
const scrollToActivities = () => {
    if (activitiesContainer.value) {
        // Get the element's position and add some offset to ensure full visibility
        const elementRect = activitiesContainer.value.getBoundingClientRect();
        const absoluteElementTop = elementRect.top + window.pageYOffset;
        const offset = 20; // Add some padding from the top

        window.scrollTo({
            top: absoluteElementTop - offset,
            behavior: 'smooth',
        });
    }
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    const locale = isRTL.value ? 'ar-SA' : 'en-US';
    return date.toLocaleDateString(locale, {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        calendar: 'gregory', // Force Gregorian calendar for Arabic
    });
};

// Get badge style based on ranking type
const getRankingBadgeStyle = (history: RankingHistory) => {
    // Yearly rankings (season is null) - more prominent with gradient
    if (history.season === null) {
        return {
            class: 'inline-flex items-center gap-2 rounded-full border border-amber-500/30 bg-gradient-to-r from-amber-500/20 to-orange-500/20 px-3 py-1.5 text-xs font-semibold text-amber-700 dark:text-amber-400 shadow-sm',
            icon: 'trophy',
        };
    }

    // Seasonal rankings (Q1-Q4) - subtle style
    return {
        class: 'inline-flex items-center gap-2 rounded-full border border-secondary/20 bg-secondary px-3 py-1.5 text-xs font-medium text-secondary-foreground',
        icon: 'award',
    };
};

// Edit activity function
const editActivity = (activity: RecentActivity) => {
    selectedActivity.value = activity;
    showEditModal.value = true;
};

// Handle edit modal close
const handleEditModalClose = () => {
    showEditModal.value = false;
    selectedActivity.value = null;
};

// Handle successful activity edit
const handleActivityEditSuccess = (updatedActivity: RecentActivity) => {
    // Update the activity in the current activities list
    const activityIndex = selectedDateActivities.value.findIndex(
        (activity) => activity.id === updatedActivity.id,
    );

    if (activityIndex !== -1) {
        // Update the activity in the current list
        selectedDateActivities.value[activityIndex] = updatedActivity;

        // Also update the cache if it exists
        if (selectedDate.value && activitiesCache.value[selectedDate.value]) {
            const cacheIndex = activitiesCache.value[
                selectedDate.value
            ].findIndex((activity) => activity.id === updatedActivity.id);
            if (cacheIndex !== -1) {
                activitiesCache.value[selectedDate.value][cacheIndex] =
                    updatedActivity;
            }
        }
    }
};
</script>

<template>
    <Head
        :title="user ? `${user.name} (@${user.username})` : t('profile.title')"
    />

    <AppLayout>
        <TooltipProvider>
            <div
                class="min-h-screen bg-gradient-to-br from-background via-muted/10 to-background"
                :dir="isRTL ? 'rtl' : 'ltr'"
            >
                <div
                    v-if="!user"
                    class="flex min-h-[50vh] items-center justify-center"
                >
                    <Card class="w-full max-w-md">
                        <CardContent class="p-8 text-center">
                            <div
                                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-muted"
                            >
                                <Lock class="h-8 w-8 text-muted-foreground" />
                            </div>
                            <h2
                                class="mb-2 text-xl font-medium text-foreground"
                            >
                                User Not Found
                            </h2>
                            <p class="text-muted-foreground">
                                The profile you're looking for doesn't exist.
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div v-else class="space-y-4 sm:space-y-6 lg:space-y-8">
                    <!-- Profile Header -->
                    <div class="mb-4 sm:mb-6 lg:mb-8">
                        <!-- Hero Section with Gradient Background -->
                        <Card
                            class="overflow-hidden border-primary/20 bg-gradient-to-br from-primary/5 via-background to-secondary/5"
                        >
                            <CardContent class="p-0">
                                <!-- Background Pattern -->
                                <div class="relative">
                                    <div
                                        class="bg-grid-white/[0.02] absolute inset-0 bg-[size:60px_60px]"
                                    ></div>

                                    <!-- Edit Profile Button (Top Right/Left for RTL) -->
                                    <div
                                        v-if="is_own_profile"
                                        :class="[
                                            'absolute top-6 z-10',
                                            isRTL ? 'left-6' : 'right-6',
                                        ]"
                                    >
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            as-child
                                            class="bg-white/80 backdrop-blur-sm hover:bg-primary hover:text-primary-foreground dark:bg-black/50 dark:hover:bg-primary dark:hover:text-primary-foreground"
                                        >
                                            <Link href="/settings/profile">
                                                <Edit class="mr-2 h-4 w-4" />
                                                {{ t('profile.edit_profile') }}
                                            </Link>
                                        </Button>
                                    </div>

                                    <div class="relative p-4 sm:p-6 lg:p-8">
                                        <div
                                            class="flex flex-col gap-4 sm:gap-6 lg:flex-row lg:items-center lg:gap-8"
                                        >
                                            <!-- Profile Picture -->
                                            <div class="flex-shrink-0">
                                                <div class="relative">
                                                    <div
                                                        class="flex h-20 w-20 items-center justify-center rounded-full border-2 border-primary/20 bg-background shadow-md transition-transform duration-200 hover:scale-105 sm:h-24 sm:w-24 lg:h-28 lg:w-28"
                                                    >
                                                        <img
                                                            v-if="user.avatar"
                                                            :src="user.avatar"
                                                            :alt="user.name"
                                                            class="h-20 w-20 rounded-full object-cover sm:h-24 sm:w-24 lg:h-28 lg:w-28"
                                                        />
                                                        <span
                                                            v-else
                                                            class="text-xl font-medium text-primary sm:text-2xl lg:text-3xl"
                                                        >
                                                            {{
                                                                user.name
                                                                    ?.charAt(0)
                                                                    .toUpperCase() ||
                                                                user.username
                                                                    .charAt(0)
                                                                    .toUpperCase()
                                                            }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Profile Info -->
                                            <div
                                                class="flex-1 space-y-4 overflow-hidden sm:space-y-6"
                                            >
                                                <!-- Name and Details -->
                                                <div
                                                    class="space-y-2 sm:space-y-3"
                                                >
                                                    <div class="space-y-2">
                                                        <div
                                                            class="flex max-w-full min-w-0 items-center gap-2 sm:gap-4"
                                                        >
                                                            <h1
                                                                class="truncate text-2xl tracking-tight text-foreground sm:text-3xl lg:text-4xl"
                                                            >
                                                                {{ user.name }}
                                                            </h1>

                                                            <!-- Longest Streak Display -->
                                                            <Tooltip>
                                                                <TooltipTrigger
                                                                    as-child
                                                                >
                                                                    <div
                                                                        class="flex items-center gap-1.5 rounded-full border border-secondary/30 bg-secondary px-2 py-1 text-secondary-foreground transition-colors hover:bg-secondary/80 sm:gap-2 sm:px-3 sm:py-1.5"
                                                                    >
                                                                        <Flame
                                                                            class="h-3 w-3 sm:h-4 sm:w-4"
                                                                        />
                                                                        <span
                                                                            class="text-xs font-medium sm:text-sm"
                                                                        >
                                                                            {{
                                                                                user.longest_streak ||
                                                                                0
                                                                            }}
                                                                        </span>
                                                                    </div>
                                                                </TooltipTrigger>
                                                                <TooltipContent
                                                                    side="bottom"
                                                                    align="center"
                                                                    :side-offset="
                                                                        8
                                                                    "
                                                                    class="max-w-xs rounded-lg border border-border/50 bg-popover px-3 py-2 text-popover-foreground shadow-lg"
                                                                    :dir="
                                                                        isRTL
                                                                            ? 'rtl'
                                                                            : 'ltr'
                                                                    "
                                                                >
                                                                    <div
                                                                        class="space-y-2"
                                                                    >
                                                                        <div
                                                                            class="flex items-center gap-2"
                                                                        >
                                                                            <Flame
                                                                                class="h-3 w-3 text-amber-500"
                                                                            />
                                                                            <span
                                                                                class="text-sm font-semibold text-foreground"
                                                                                >{{
                                                                                    t(
                                                                                        'profile.longest_streak',
                                                                                    )
                                                                                }}</span
                                                                            >
                                                                        </div>
                                                                        <p
                                                                            class="text-xs text-muted-foreground"
                                                                        >
                                                                            {{
                                                                                t(
                                                                                    'profile.best_consecutive_record',
                                                                                )
                                                                            }}:
                                                                            <span
                                                                                class="font-medium text-foreground"
                                                                            >
                                                                                {{
                                                                                    user.longest_streak ||
                                                                                    0
                                                                                }}
                                                                                {{
                                                                                    getDaysText(
                                                                                        user.longest_streak ||
                                                                                            0,
                                                                                    )
                                                                                }}
                                                                            </span>
                                                                        </p>
                                                                    </div>
                                                                </TooltipContent>
                                                            </Tooltip>
                                                        </div>
                                                    </div>

                                                    <p
                                                        v-if="user.bio"
                                                        class="max-w-2xl text-sm leading-relaxed text-foreground/80 sm:text-base"
                                                    >
                                                        {{ user.bio }}
                                                    </p>
                                                </div>

                                                <!-- Website Link -->
                                                <div
                                                    v-if="user.website_url"
                                                    class="flex items-center"
                                                >
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        as-child
                                                        class="border-border/50 bg-white/50 backdrop-blur-sm transition-all hover:border-primary/50 hover:bg-primary hover:text-primary-foreground dark:bg-black/20 dark:hover:bg-primary dark:hover:text-primary-foreground"
                                                    >
                                                        <a
                                                            :href="
                                                                user.website_url
                                                            "
                                                            target="_blank"
                                                            class="flex items-center gap-1.5 text-xs sm:gap-2 sm:text-sm"
                                                        >
                                                            <LinkIcon
                                                                class="h-3 w-3 sm:h-4 sm:w-4"
                                                            />
                                                            <span
                                                                class="max-w-[200px] truncate sm:max-w-[250px]"
                                                                >{{
                                                                    user.website_url
                                                                }}</span
                                                            >
                                                        </a>
                                                    </Button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Main Content -->
                    <div class="space-y-4 sm:space-y-6 lg:space-y-8">
                        <!-- Ranking Badges Section -->
                        <Card
                            v-if="
                                ranking_histories &&
                                ranking_histories.length > 0
                            "
                        >
                            <CardHeader>
                                <CardTitle class="text-lg sm:text-xl">{{
                                    t('profile.ranking_badges')
                                }}</CardTitle>
                                <CardDescription class="text-sm">
                                    {{
                                        is_own_profile
                                            ? t('profile.your_achievements')
                                            : t('profile.achievements')
                                    }}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-3 sm:space-y-4">
                                    <div
                                        class="flex cursor-default flex-wrap gap-1.5 sm:gap-2"
                                    >
                                        <template
                                            v-for="history in ranking_histories.slice(
                                                0,
                                                8,
                                            )"
                                            :key="history.id"
                                        >
                                            <div
                                                :title="`${history.display_name} - ${formatNumber(history.points)} points`"
                                                :class="[
                                                    getRankingBadgeStyle(
                                                        history,
                                                    ).class,
                                                    'text-xs sm:text-sm',
                                                ]"
                                            >
                                                <Trophy
                                                    v-if="
                                                        history.season === null
                                                    "
                                                    class="h-2.5 w-2.5 sm:h-3 sm:w-3"
                                                />
                                                <Award
                                                    v-else
                                                    class="h-2.5 w-2.5 sm:h-3 sm:w-3"
                                                />
                                                <span
                                                    class="max-w-[150px] truncate sm:max-w-[200px]"
                                                >
                                                    {{ history.display_name }}
                                                </span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Activity Streak -->
                        <Card>
                            <CardHeader>
                                <CardTitle class="text-lg sm:text-xl">{{
                                    t('profile.activity_streak')
                                }}</CardTitle>
                                <CardDescription
                                    class="text-sm"
                                    v-if="is_own_profile"
                                >
                                    {{ t('profile.health_journey') }}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <ActivityCalendarHeatmap
                                    :days="heatmapDays"
                                    :week_starts_on="user.week_starts_on"
                                    :enable-lazy-loading="true"
                                    :username="user.username"
                                    :user-created-at="user.created_at"
                                    @day-click="handleDayClick"
                                />

                                <!-- Selected Date Activities (Only for own profile) -->
                                <div
                                    v-if="is_own_profile && selectedDate"
                                    ref="activitiesContainer"
                                    class="mt-4 space-y-3 sm:mt-6 sm:space-y-4"
                                >
                                    <div
                                        class="flex items-center justify-between border-t border-border pt-3 sm:pt-4"
                                    >
                                        <h4
                                            class="text-base font-semibold text-foreground sm:text-lg"
                                        >
                                            {{ formatDate(selectedDate) }}
                                        </h4>
                                    </div>

                                    <!-- Loading State -->
                                    <div
                                        v-if="loadingActivities"
                                        class="flex items-center justify-center py-6 sm:py-8"
                                    >
                                        <div
                                            class="text-xs text-muted-foreground sm:text-sm"
                                        >
                                            {{ t('activity.loading') }}
                                        </div>
                                    </div>

                                    <!-- Activities List -->
                                    <div
                                        v-else-if="
                                            selectedDateActivities.length > 0
                                        "
                                        class="space-y-2 sm:space-y-3"
                                    >
                                        <div
                                            v-for="activity in selectedDateActivities"
                                            :key="activity.id"
                                            :class="[
                                                'flex items-center justify-between rounded-lg border p-3 transition-colors sm:p-4',
                                                activity.is_inactive
                                                    ? 'border-orange-200 bg-orange-50/50 hover:bg-orange-100/50 dark:border-orange-800 dark:bg-orange-950/20 dark:hover:bg-orange-900/30'
                                                    : 'border-border/50 bg-card/50 hover:bg-card',
                                            ]"
                                        >
                                            <div
                                                class="flex items-center gap-2 sm:gap-3"
                                            >
                                                <span
                                                    class="text-lg sm:text-2xl"
                                                    >{{
                                                        activity.activity_type_icon
                                                    }}</span
                                                >
                                                <div class="min-w-0 flex-1">
                                                    <div
                                                        class="flex items-center gap-2"
                                                    >
                                                        <p
                                                            class="truncate text-sm font-medium text-foreground sm:text-base"
                                                        >
                                                            {{
                                                                activity.custom_name
                                                            }}
                                                        </p>
                                                        <Badge
                                                            variant="secondary"
                                                            class="text-xs sm:text-sm"
                                                        >
                                                            +{{
                                                                activity.points_earned
                                                            }}
                                                            {{
                                                                t('profile.pts')
                                                            }}
                                                        </Badge>
                                                        <span
                                                            v-if="
                                                                activity.is_inactive
                                                            "
                                                            class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-800 dark:bg-orange-900/30 dark:text-orange-300"
                                                        >
                                                            {{
                                                                t(
                                                                    'activity.inactive_habit',
                                                                )
                                                            }}
                                                        </span>
                                                    </div>
                                                    <p
                                                        v-if="activity.notes"
                                                        class="mt-1 text-xs text-muted-foreground italic sm:text-sm"
                                                    >
                                                        "{{ activity.notes }}"
                                                    </p>
                                                    <Button
                                                        v-if="
                                                            activity.memory_url
                                                        "
                                                        variant="outline"
                                                        size="sm"
                                                        as-child
                                                        class="mt-1.5 h-6 border-border/50 bg-white/50 backdrop-blur-sm transition-all hover:border-primary/50 hover:bg-primary hover:text-primary-foreground sm:mt-2 sm:h-7 dark:bg-black/20 dark:hover:bg-primary dark:hover:text-primary-foreground"
                                                    >
                                                        <a
                                                            :href="
                                                                activity.memory_url
                                                            "
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="flex items-center gap-1 text-xs sm:gap-1.5"
                                                        >
                                                            <LinkIcon
                                                                class="h-2.5 w-2.5 sm:h-3 sm:w-3"
                                                            />
                                                            {{
                                                                t(
                                                                    'activity.view_memory',
                                                                )
                                                            }}
                                                        </a>
                                                    </Button>
                                                </div>
                                            </div>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                @click="editActivity(activity)"
                                                class="h-8 w-8 p-0 hover:bg-muted"
                                            >
                                                <Edit class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </div>

                                    <!-- Empty State -->
                                    <div
                                        v-else
                                        class="flex flex-col items-center justify-center rounded-lg border border-dashed border-border py-6 text-center sm:py-8"
                                    >
                                        <Activity
                                            class="mb-2 h-6 w-6 text-muted-foreground opacity-50 sm:h-8 sm:w-8"
                                        />
                                        <p
                                            class="text-xs text-muted-foreground sm:text-sm"
                                        >
                                            {{ t('activity.no_activities') }}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </TooltipProvider>

        <!-- Edit Activity Modal -->
        <EditActivityModal
            v-model:open="showEditModal"
            :activity="selectedActivity"
            @update:open="handleEditModalClose"
            @activity-updated="handleActivityEditSuccess"
        />
    </AppLayout>
</template>
