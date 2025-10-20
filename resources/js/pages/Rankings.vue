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
import { useTranslations } from '@/composables/useTranslations';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    Award,
    CalendarCheck,
    Crown,
    Footprints,
    Trophy,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

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

// Use translations with reactive locale
const { t, isRTL } = useTranslations();

const activeTab = ref('today');

// Computed properties for proper Arabic formatting
const seasonRankingsTitle = computed(() => {
    if (isRTL.value) {
        // Arabic: "تصنيفات الموسم الرابع 2025"
        return `${t('rankings.q4_rankings')} ${props.current_year}`;
    } else {
        // English: "Q4 2025 Rankings"
        return `${props.current_season} ${props.current_year} ${t('rankings.rankings')}`;
    }
});

const annualRankingsTitle = computed(() => {
    if (isRTL.value) {
        // Arabic: "التصنيفات السنوية 2025"
        return `${t('rankings.annual_rankings')} ${props.current_year}`;
    } else {
        // English: "2025 Annual Rankings"
        return `${props.current_year} ${t('rankings.annual_rankings')}`;
    }
});

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
    if (!rank) return `0 ${t('points.short')}`;
    return `${rank.points} ${t('points.short')}`;
};

const onTabChange = (newTab: string) => {
    activeTab.value = newTab;
};
</script>

<template>
    <AppLayout :dir="isRTL ? 'rtl' : 'ltr'">
        <Head :title="t('rankings.title')" />

        <div class="container mx-auto px-4 py-4 sm:py-6 lg:py-8">
            <!-- Header -->
            <div class="mb-6 sm:mb-8">
                <h1
                    class="mb-2 flex items-center gap-2 text-2xl font-bold text-foreground sm:gap-3 sm:text-3xl"
                >
                    <Crown class="h-6 w-6 text-primary sm:h-8 sm:w-8" />
                    {{ t('rankings.title') }}
                </h1>
                <p class="text-sm text-muted-foreground sm:text-base">
                    {{ t('rankings.description') }}
                </p>
            </div>

            <!-- User Stats Card -->
            <Card
                class="mb-4 border-primary/20 bg-gradient-to-r from-primary/5 to-primary/10 sm:mb-6"
            >
                <CardHeader>
                    <CardTitle class="text-lg sm:text-xl">{{
                        t('rankings.your_rankings')
                    }}</CardTitle>
                    <CardDescription class="text-sm">
                        {{ t('rankings.your_rankings_description') }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-6">
                        <div class="text-center">
                            <p class="mb-1 text-xs text-muted-foreground">
                                {{ t('rankings.today') }}
                            </p>
                            <p
                                class="text-xl font-bold text-primary sm:text-2xl"
                            >
                                {{ getCurrentUserRankText('today') }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ getCurrentUserPointsText('today') }}
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="mb-1 text-xs text-muted-foreground">
                                {{ t('rankings.yesterday') }}
                            </p>
                            <p
                                class="text-xl font-bold text-primary sm:text-2xl"
                            >
                                {{ getCurrentUserRankText('yesterday') }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ getCurrentUserPointsText('yesterday') }}
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="mb-1 text-xs text-muted-foreground">
                                {{
                                    isRTL
                                        ? t('rankings.q4_rankings')
                                        : current_season
                                }}
                            </p>
                            <p
                                class="text-xl font-bold text-primary sm:text-2xl"
                            >
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
                            <p
                                class="text-xl font-bold text-primary sm:text-2xl"
                            >
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
                <TabsList class="grid w-full grid-cols-4 gap-1 sm:gap-2">
                    <TabsTrigger
                        value="today"
                        class="flex items-center gap-1 text-xs sm:gap-2 sm:text-sm"
                    >
                        <Footprints class="h-3 w-3 sm:h-4 sm:w-4" />
                        <span class="hidden sm:inline">{{
                            t('rankings.today')
                        }}</span>
                    </TabsTrigger>
                    <TabsTrigger
                        value="yesterday"
                        class="flex items-center gap-1 text-xs sm:gap-2 sm:text-sm"
                    >
                        <CalendarCheck class="h-3 w-3 sm:h-4 sm:w-4" />
                        <span class="hidden sm:inline">{{
                            t('rankings.yesterday')
                        }}</span>
                    </TabsTrigger>
                    <TabsTrigger
                        value="season"
                        class="flex items-center gap-1 text-xs sm:gap-2 sm:text-sm"
                    >
                        <Award class="h-3 w-3 sm:h-4 sm:w-4" />
                        <span class="hidden sm:inline">{{
                            t('rankings.season')
                        }}</span>
                    </TabsTrigger>
                    <TabsTrigger
                        value="year"
                        class="flex items-center gap-1 text-xs sm:gap-2 sm:text-sm"
                    >
                        <Trophy class="h-3 w-3 sm:h-4 sm:w-4" />
                        <span class="hidden sm:inline">{{
                            t('rankings.year')
                        }}</span>
                    </TabsTrigger>
                </TabsList>

                <!-- Today -->
                <TabsContent value="today">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg sm:text-xl">{{
                                t('rankings.todays_leaders')
                            }}</CardTitle>
                            <CardDescription class="text-sm">
                                {{ t('rankings.todays_leaders_description') }}
                                {{
                                    isRTL
                                        ? `${new Date().getDate()} ${new Date().toLocaleDateString('ar-EG', { month: 'long' })}`
                                        : new Date().toLocaleDateString(
                                              'en-US',
                                              {
                                                  month: 'long',
                                                  day: 'numeric',
                                              },
                                          )
                                }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <RankingsList
                                :rankings="rankings.today"
                                :current-user-id="user.id"
                                :empty-message="
                                    t('rankings.no_activities_today')
                                "
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Yesterday -->
                <TabsContent value="yesterday">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg sm:text-xl">{{
                                t('rankings.yesterdays_champions')
                            }}</CardTitle>
                            <CardDescription class="text-sm">
                                {{
                                    t(
                                        'rankings.yesterdays_champions_description',
                                    )
                                }}
                                {{
                                    isRTL
                                        ? (() => {
                                              const yesterday = new Date(
                                                  Date.now() - 86400000,
                                              );
                                              return `${yesterday.getDate()} ${yesterday.toLocaleDateString('ar-EG', { month: 'long' })}`;
                                          })()
                                        : new Date(
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
                                :empty-message="
                                    t('rankings.no_activities_yesterday')
                                "
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Season -->
                <TabsContent value="season">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg sm:text-xl">{{
                                seasonRankingsTitle
                            }}</CardTitle>
                            <CardDescription class="text-sm">
                                {{ t('rankings.season_description') }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <RankingsList
                                :rankings="rankings.season"
                                :current-user-id="user.id"
                                show-season
                                :empty-message="
                                    t('rankings.no_season_rankings')
                                "
                            />
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Year -->
                <TabsContent value="year">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg sm:text-xl">{{
                                annualRankingsTitle
                            }}</CardTitle>
                            <CardDescription class="text-sm">
                                {{ t('rankings.year_description') }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <RankingsList
                                :rankings="rankings.year"
                                :current-user-id="user.id"
                                show-year
                                :empty-message="
                                    t('rankings.no_yearly_rankings')
                                "
                            />
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>
