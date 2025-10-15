<script setup lang="ts">
import ActivityCalendarHeatmap from '@/components/ActivityCalendarHeatmap.vue';
import IconPointCounter from '@/components/IconPointCounter.vue';
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
import { Head, Link } from '@inertiajs/vue3';
import { Activity, Edit, Link as LinkIcon, Lock } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';

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
});

const { formatNumber } = useNumberFormat();

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
                'Accept': 'application/json',
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
                                                                    user.current_year_points ||
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
                                <div class="flex items-center justify-between border-t border-border pt-4">
                                    <h4 class="text-lg font-semibold text-foreground">
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
                                    v-else-if="selectedDateActivities.length > 0"
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
                                                <p class="font-medium text-foreground">
                                                    {{ activity.custom_name }}
                                                </p>
                                                <p class="text-sm text-muted-foreground">
                                                    {{ activity.activity_type_name }}
                                                </p>
                                                <p
                                                    v-if="activity.notes"
                                                    class="mt-1 text-xs italic text-muted-foreground"
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
                                                        :href="activity.proof_url"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="flex items-center gap-1.5 text-xs"
                                                    >
                                                        <LinkIcon class="h-3 w-3" />
                                                        View proof
                                                    </a>
                                                </Button>
                                            </div>
                                        </div>
                                        <Badge variant="secondary" class="ml-2 flex-shrink-0">
                                            +{{ activity.points_earned }} pts
                                        </Badge>
                                    </div>
                                </div>

                                <!-- Empty State -->
                                <div
                                    v-else
                                    class="flex flex-col items-center justify-center rounded-lg border border-dashed border-border py-8 text-center"
                                >
                                    <Activity class="mb-2 h-8 w-8 text-muted-foreground opacity-50" />
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
