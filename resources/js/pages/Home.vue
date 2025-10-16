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
import { useLogActivity } from '@/composables/useLogActivity';
import { seoConfigs } from '@/config/seo';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { Award, Calendar, Plus, TrendingUp, Trophy } from 'lucide-vue-next';
import { computed } from 'vue';

interface TodayActivity {
    id: number;
    habit_name: string;
    points_earned: number;
    notes?: string;
    created_at: string;
}

interface RecentActivity {
    id: number;
    date: string;
    habit_name: string;
    icon: string;
    points: number;
    notes?: string;
}

interface Props {
    user: {
        id: number;
        username: string;
        name: string;
        avatar?: string;
        season_rank?: {
            rank: number;
            season: string;
            year: number;
        };
        year_rank?: {
            rank: number;
            year: number;
        };
    };
    today_activities: TodayActivity[];
    recent_activities: RecentActivity[];
}

const props = defineProps<Props>();

// Computed
const todayPoints = computed(() => {
    return props.today_activities.reduce(
        (sum, activity) => sum + activity.points_earned,
        0,
    );
});

// State
const { openLogActivityModal } = useLogActivity();

// Methods
const handleLogActivity = () => {
    openLogActivityModal();
};

const goToRankings = () => {
    router.visit('/rankings');
};

const handleEditActivity = () => {
    router.visit('/settings/habits');
};
</script>

<template>
    <AppLayout>
        <SEO v-bind="seoConfigs.home" />

        <div class="container mx-auto px-4 py-8">
            <!-- Welcome Header -->
            <div class="mb-8">
                <h1 class="mb-2 text-3xl font-bold text-foreground">
                    Welcome back, {{ user.name }}! 👋
                </h1>
                <p class="text-muted-foreground">
                    Track your health, earn points, and climb the rankings
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Left Column: Today's Activities -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Today's Activities -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div>
                                    <CardTitle>Today's Activities</CardTitle>
                                    <CardDescription>
                                        {{ today_activities.length }} activities
                                        • {{ todayPoints }} points
                                    </CardDescription>
                                </div>
                                <Button size="sm" @click="handleLogActivity">
                                    <Plus class="h-4 w-4" />
                                    Log Activity
                                </Button>
                            </div>
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
                                                pts
                                            </Badge>
                                        </div>
                                        <p
                                            v-if="activity.notes"
                                            class="text-sm text-muted-foreground"
                                        >
                                            {{ activity.notes }}
                                        </p>
                                        <p
                                            class="mt-1 text-xs text-muted-foreground"
                                        >
                                            {{ activity.created_at }}
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
                                <p class="mb-2">No activities logged today</p>
                                <p class="mb-4 text-sm">
                                    Start tracking your health!
                                </p>
                                <Button size="sm" @click="handleEditActivity">
                                    Edit Activities
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column: Recent Activities & Quick Actions -->
                <div class="space-y-6">
                    <!-- Rankings Quick View -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <TrendingUp class="h-5 w-5 text-primary" />
                                Your Rankings
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
                                        {{ user.season_rank.season }}
                                        {{ user.season_rank.year }}
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
                                        {{ user.year_rank.year }} Year
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
                                @click="goToRankings"
                            >
                                <TrendingUp class="h-4 w-4" />
                                View All Rankings
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Recent Activities -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Activity</CardTitle>
                            <CardDescription>Last 7 days</CardDescription>
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
                                <p class="text-sm">No recent activities</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
