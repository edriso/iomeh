<script setup lang="ts">
import RankingsList from '@/components/RankingsList.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Award, CalendarCheck, Footprints, Trophy } from 'lucide-vue-next';
import { ref } from 'vue';

interface RankingsEntry {
    rank: number;
    user: {
        id: number;
        name: string;
        username: string;
        avatar?: string;
    };
    points: number;
    activities_count?: number;
    season?: string;
    year?: number;
}

interface Props {
    rankings: {
        today: RankingsEntry[];
        yesterday: RankingsEntry[];
        season: RankingsEntry[];
        year: RankingsEntry[];
    };
    current_user_rank: {
        today: { rank: number; points: number } | null;
        yesterday: { rank: number; points: number } | null;
        season: { rank: number; points: number; season: string } | null;
        year: { rank: number; points: number } | null;
    };
    user: {
        id: number;
        name: string;
        username: string;
    };
    current_season: string;
    current_year: number;
}

const props = defineProps<Props>();

const activeTab = ref('today');

const getCurrentUserRankText = (
    period: keyof typeof props.current_user_rank,
) => {
    const rank = props.current_user_rank[period];
    if (!rank) return '-';
    return `#${rank.rank}`;
};

const getCurrentUserPointsText = (
    period: keyof typeof props.current_user_rank,
) => {
    const rank = props.current_user_rank[period];
    if (!rank) return '0 pts';
    return `${rank.points} pts`;
};

const onTabChange = (newTab: string) => {
    activeTab.value = newTab;
};
</script>

<template>
    <AppLayout>
        <Head title="Rankings" />

        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1
                    class="mb-2 flex items-center gap-3 text-3xl font-bold text-foreground"
                >
                    <Trophy class="h-8 w-8 text-primary" />
                    Global Rankings
                </h1>
                <p class="text-muted-foreground">
                    Compete with athletes worldwide across different time
                    periods
                </p>
            </div>

            <!-- User Stats Card -->
            <Card
                class="mb-6 border-primary/20 bg-gradient-to-r from-primary/5 to-primary/10"
            >
                <CardHeader>
                    <CardTitle>Your Rankings</CardTitle>
                    <CardDescription
                        >See where you stand across all
                        leaderboards</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div class="text-center">
                            <p class="mb-1 text-xs text-muted-foreground">
                                Today
                            </p>
                            <p class="text-2xl font-bold text-primary">
                                {{ getCurrentUserRankText('today') }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ getCurrentUserPointsText('today') }}
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="mb-1 text-xs text-muted-foreground">
                                Yesterday
                            </p>
                            <p class="text-2xl font-bold text-primary">
                                {{ getCurrentUserRankText('yesterday') }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ getCurrentUserPointsText('yesterday') }}
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="mb-1 text-xs text-muted-foreground">
                                {{ current_season }}
                            </p>
                            <p class="text-2xl font-bold text-primary">
                                {{ getCurrentUserRankText('season') }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ getCurrentUserPointsText('season') }}
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="mb-1 text-xs text-muted-foreground">
                                {{ current_year }}
                            </p>
                            <p class="text-2xl font-bold text-primary">
                                {{ getCurrentUserRankText('year') }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ getCurrentUserPointsText('year') }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Rankings Tabs -->
            <Tabs :default-value="activeTab" @update:model-value="onTabChange">
                <TabsList class="grid w-full grid-cols-4">
                    <TabsTrigger value="today" class="flex items-center gap-2">
                        <Footprints class="h-4 w-4" />
                        <span class="hidden sm:inline">Today</span>
                    </TabsTrigger>
                    <TabsTrigger
                        value="yesterday"
                        class="flex items-center gap-2"
                    >
                        <CalendarCheck class="h-4 w-4" />
                        <span class="hidden sm:inline">Yesterday</span>
                    </TabsTrigger>
                    <TabsTrigger value="season" class="flex items-center gap-2">
                        <Award class="h-4 w-4" />
                        <span class="hidden sm:inline">Season</span>
                    </TabsTrigger>
                    <TabsTrigger value="year" class="flex items-center gap-2">
                        <Trophy class="h-4 w-4" />
                        <span class="hidden sm:inline">Year</span>
                    </TabsTrigger>
                </TabsList>

                <!-- Today -->
                <TabsContent value="today">
                    <Card>
                        <CardHeader>
                            <CardTitle>Today's Leaders</CardTitle>
                            <CardDescription>
                                Real-time rankings for
                                {{
                                    new Date().toLocaleDateString('en-US', {
                                        month: 'long',
                                        day: 'numeric',
                                    })
                                }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <RankingsList
                                :rankings="rankings.today"
                                :current-user-id="user.id"
                                empty-message="No activities logged today yet. Be the first!"
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Yesterday -->
                <TabsContent value="yesterday">
                    <Card>
                        <CardHeader>
                            <CardTitle>Yesterday's Champions</CardTitle>
                            <CardDescription>
                                Final rankings for
                                {{
                                    new Date(
                                        Date.now() - 86400000,
                                    ).toLocaleDateString('en-US', {
                                        month: 'long',
                                        day: 'numeric',
                                    })
                                }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <RankingsList
                                :rankings="rankings.yesterday"
                                :current-user-id="user.id"
                                empty-message="No activities logged yesterday"
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Season -->
                <TabsContent value="season">
                    <Card>
                        <CardHeader>
                            <CardTitle
                                >{{ current_season }}
                                {{ current_year }} Rankings</CardTitle
                            >
                            <CardDescription>
                                Quarterly leaderboard for the current season
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <RankingsList
                                :rankings="rankings.season"
                                :current-user-id="user.id"
                                show-season
                                empty-message="No season rankings yet. Start logging activities!"
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Year -->
                <TabsContent value="year">
                    <Card>
                        <CardHeader>
                            <CardTitle
                                >{{ current_year }} Annual Rankings</CardTitle
                            >
                            <CardDescription>
                                Yearly leaderboard - the ultimate test of
                                consistency
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <RankingsList
                                :rankings="rankings.year"
                                :current-user-id="user.id"
                                show-year
                                empty-message="No yearly rankings yet"
                            />
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
