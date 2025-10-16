<script setup lang="ts">
import ActivityCalendarHeatmap from '@/components/ActivityCalendarHeatmap.vue';
import IconPointCounter from '@/components/IconPointCounter.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { useNumberFormat } from '@/composables/useNumberFormat';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Activity,
    Award,
    CheckCircle,
    Edit,
    Link as LinkIcon,
    Lock,
    Settings,
    Trophy,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

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
    joined_at: string;
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
    proof_url?: string;
}

interface CalendarDay {
    date: string;
    activities_count: number;
    points: number;
}

interface Interest {
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
        interests_count: number;
    };
    is_own_profile?: boolean;
    interests?: Interest[];
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
        interests_count: 0,
    }),
    is_own_profile: false,
    interests: () => [],
    ranking_histories: () => [],
});

const { formatNumber } = useNumberFormat();

const categoryColors: Record<string, string> = {
    workout: 'bg-primary/10 text-primary border-primary/20',
    nutrition: 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
    wellness: 'bg-blue-500/10 text-blue-600 border-blue-500/20',
    mindfulness: 'bg-purple-500/10 text-purple-600 border-purple-500/20',
};

const handleEditActivities = () => {
    router.visit('/settings/interests');
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

const handleDayClick = async (date: string) => {
    if (!props.is_own_profile) return; // Only for own profile

    selectedDate.value = date;
    loadingActivities.value = true;

    try {
        // Fetch activities for the selected date
        const response = await fetch(`/api/activities/date/${date}`, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (response.ok) {
            const data = await response.json();
            selectedDateActivities.value = data.activities || [];
        }
    } catch (error) {
        console.error('Error fetching activities:', error);
        selectedDateActivities.value = [];
    } finally {
        loadingActivities.value = false;
    }
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
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
</script>

<template>
    <Head :title="user ? `${user.name} (@${user.username})` : 'Profile'" />

    <AppLayout>
        <div
            class="min-h-screen bg-gradient-to-br from-background via-muted/10 to-background"
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
                        <h2 class="mb-2 text-xl font-medium text-foreground">
                            User Not Found
                        </h2>
                        <p class="text-muted-foreground">
                            The profile you're looking for doesn't exist.
                        </p>
                    </CardContent>
                </Card>
            </div>

            <div v-else class="space-y-8">
                <!-- Profile Header -->
                <div class="mb-8">
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

                                <!-- Edit Profile Button (Top Right) -->
                                <div
                                    v-if="is_own_profile"
                                    class="absolute top-6 right-6 z-10"
                                >
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                        class="bg-white/80 backdrop-blur-sm hover:bg-primary hover:text-primary-foreground dark:bg-black/50 dark:hover:bg-primary dark:hover:text-primary-foreground"
                                    >
                                        <Link href="/settings/profile">
                                            <Edit class="mr-2 h-4 w-4" />
                                            Edit Profile
                                        </Link>
                                    </Button>
                                </div>

                                <div class="relative p-8">
                                    <div
                                        class="flex flex-col gap-8 lg:flex-row lg:items-center"
                                    >
                                        <!-- Profile Picture -->
                                        <div class="flex-shrink-0">
                                            <div class="relative">
                                                <div
                                                    class="flex h-28 w-28 items-center justify-center rounded-full border-2 border-primary/20 bg-background shadow-md transition-transform duration-200 hover:scale-105"
                                                >
                                                    <img
                                                        v-if="user.avatar"
                                                        :src="user.avatar"
                                                        :alt="user.name"
                                                        class="h-28 w-28 rounded-full object-cover"
                                                    />
                                                    <span
                                                        v-else
                                                        class="text-3xl font-medium text-primary"
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
                                            class="flex-1 space-y-6 overflow-hidden"
                                        >
                                            <!-- Name and Details -->
                                            <div class="space-y-3">
                                                <div class="space-y-2">
                                                    <div
                                                        class="flex max-w-full min-w-0 items-center gap-4"
                                                    >
                                                        <h1
                                                            class="truncate text-4xl tracking-tight text-foreground"
                                                        >
                                                            {{ user.name }}
                                                        </h1>

                                                        <!-- Points Display -->
                                                        <div
                                                            class="flex items-center gap-2 rounded-full border border-secondary/30 bg-secondary px-3 py-1.5"
                                                        >
                                                            <IconPointCounter
                                                                class="h-4 w-4 text-secondary-foreground"
                                                            />
                                                            <span
                                                                class="text-sm font-medium text-secondary-foreground"
                                                            >
                                                                {{
                                                                    formatNumber(
                                                                        user.current_season_points ||
                                                                            0,
                                                                    )
                                                                }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <p
                                                    v-if="user.bio"
                                                    class="max-w-2xl leading-relaxed text-foreground/80"
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
                                                        :href="user.website_url"
                                                        target="_blank"
                                                        class="flex items-center gap-2 text-sm"
                                                    >
                                                        <LinkIcon
                                                            class="h-4 w-4"
                                                        />
                                                        <span
                                                            class="max-w-[250px] truncate"
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
                <div class="space-y-8">
                    <!-- My Activities -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div>
                                    <CardTitle>{{
                                        is_own_profile
                                            ? 'My Activities'
                                            : 'Activities'
                                    }}</CardTitle>
                                    <CardDescription v-if="is_own_profile">
                                        Activities you're tracking
                                    </CardDescription>
                                    <CardDescription v-else>
                                        Activities being tracked
                                    </CardDescription>
                                </div>
                                <div v-if="is_own_profile">
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        @click="handleEditActivities"
                                    >
                                        <Settings class="h-4 w-4" />
                                        Edit Activities
                                    </Button>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div
                                v-if="interests && interests.length > 0"
                                class="flex cursor-default flex-wrap gap-2"
                            >
                                <Badge
                                    v-for="interest in interests"
                                    :key="interest.id"
                                    variant="outline"
                                    :class="[
                                        'px-3 py-1.5 text-sm',
                                        interest.has_activity_today
                                            ? 'border-primary/20 bg-primary/10 text-primary'
                                            : categoryColors[
                                                  interest.category
                                              ] || 'bg-muted',
                                    ]"
                                >
                                    <span class="mr-1.5">{{
                                        interest.icon
                                    }}</span>
                                    {{ interest.name }}
                                    <CheckCircle
                                        v-if="interest.has_activity_today"
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
                                <p v-if="is_own_profile">
                                    No interests selected yet
                                </p>
                                <p v-else>No activities selected</p>
                                <Button
                                    v-if="is_own_profile"
                                    size="sm"
                                    variant="outline"
                                    class="mt-3"
                                    @click="handleEditActivities"
                                >
                                    Select Activities
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Ranking Badges Section -->
                    <Card
                        v-if="ranking_histories && ranking_histories.length > 0"
                    >
                        <CardHeader>
                            <CardTitle>Ranking Badges</CardTitle>
                            <CardDescription>
                                {{
                                    is_own_profile
                                        ? 'Your achievements across seasons'
                                        : 'Achievements across seasons'
                                }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div
                                    class="flex cursor-default flex-wrap gap-2"
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
                                            :class="
                                                getRankingBadgeStyle(history)
                                                    .class
                                            "
                                        >
                                            <Trophy
                                                v-if="history.season === null"
                                                class="h-3 w-3"
                                            />
                                            <Award v-else class="h-3 w-3" />
                                            <span
                                                class="max-w-[200px] truncate"
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
                            <CardTitle>Activity Streak</CardTitle>
                            <CardDescription v-if="is_own_profile">
                                Your health journey - every day counts!
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
                                class="mt-6 space-y-4"
                            >
                                <div
                                    class="flex items-center justify-between border-t border-border pt-4"
                                >
                                    <h4
                                        class="text-lg font-semibold text-foreground"
                                    >
                                        {{ formatDate(selectedDate) }}
                                    </h4>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="selectedDate = null"
                                    >
                                        Close
                                    </Button>
                                </div>

                                <!-- Loading State -->
                                <div
                                    v-if="loadingActivities"
                                    class="flex items-center justify-center py-8"
                                >
                                    <div class="text-sm text-muted-foreground">
                                        Loading activities...
                                    </div>
                                </div>

                                <!-- Activities List -->
                                <div
                                    v-else-if="
                                        selectedDateActivities.length > 0
                                    "
                                    class="space-y-3"
                                >
                                    <div
                                        v-for="activity in selectedDateActivities"
                                        :key="activity.id"
                                        class="flex items-center justify-between rounded-lg border border-border/50 bg-card/50 p-4 transition-colors hover:bg-card"
                                    >
                                        <div class="flex items-center gap-3">
                                            <span class="text-2xl">{{
                                                activity.activity_type_icon
                                            }}</span>
                                            <div class="flex-1">
                                                <p
                                                    class="font-medium text-foreground"
                                                >
                                                    {{ activity.custom_name }}
                                                </p>
                                                <p
                                                    class="text-sm text-muted-foreground"
                                                >
                                                    {{
                                                        activity.activity_type_name
                                                    }}
                                                </p>
                                                <p
                                                    v-if="activity.notes"
                                                    class="mt-1 text-xs text-muted-foreground italic"
                                                >
                                                    "{{ activity.notes }}"
                                                </p>
                                                <Button
                                                    v-if="activity.proof_url"
                                                    variant="outline"
                                                    size="sm"
                                                    as-child
                                                    class="mt-2 h-7 border-border/50 bg-white/50 backdrop-blur-sm transition-all hover:border-primary/50 hover:bg-primary hover:text-primary-foreground dark:bg-black/20 dark:hover:bg-primary dark:hover:text-primary-foreground"
                                                >
                                                    <a
                                                        :href="
                                                            activity.proof_url
                                                        "
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="flex items-center gap-1.5 text-xs"
                                                    >
                                                        <LinkIcon
                                                            class="h-3 w-3"
                                                        />
                                                        View proof
                                                    </a>
                                                </Button>
                                            </div>
                                        </div>
                                        <Badge
                                            variant="secondary"
                                            class="ml-2 flex-shrink-0"
                                        >
                                            +{{ activity.points_earned }} pts
                                        </Badge>
                                    </div>
                                </div>

                                <!-- Empty State -->
                                <div
                                    v-else
                                    class="flex flex-col items-center justify-center rounded-lg border border-dashed border-border py-8 text-center"
                                >
                                    <Activity
                                        class="mb-2 h-8 w-8 text-muted-foreground opacity-50"
                                    />
                                    <p class="text-sm text-muted-foreground">
                                        No activities logged on this day
                                    </p>
                                </div>
                            </div>

                            <!-- Join Date -->
                            <div class="mt-4 border-t border-border pt-4">
                                <div
                                    class="flex items-center gap-2 text-sm text-muted-foreground"
                                >
                                    <div
                                        class="h-1 w-1 rounded-full bg-muted-foreground"
                                    ></div>
                                    <span>
                                        {{
                                            (() => {
                                                const daysDiff = Math.floor(
                                                    (new Date().getTime() -
                                                        new Date(
                                                            user.joined_at,
                                                        ).getTime()) /
                                                        (1000 * 60 * 60 * 24),
                                                );
                                                if (daysDiff === 0)
                                                    return 'Joined today';
                                                if (daysDiff === 1)
                                                    return 'Joined yesterday';
                                                return `Joined ${daysDiff} days ago`;
                                            })()
                                        }}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
