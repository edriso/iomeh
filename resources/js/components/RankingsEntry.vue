<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { useNumberFormat } from '@/composables/useNumberFormat';
import { useTranslations } from '@/composables/useTranslations';
import { Link } from '@inertiajs/vue3';
import { Activity, Award, Crown, Trophy } from 'lucide-vue-next';

const { t, isRTL } = useTranslations();

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
    entry: RankingsEntry;
    currentUserId?: number;
    showSeason?: boolean;
    showYear?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showSeason: false,
    showYear: false,
});

const { formatNumber } = useNumberFormat();

const getRankIcon = (rank: number) => {
    switch (rank) {
        case 1:
            return Crown;
        case 2:
            return Trophy;
        case 3:
            return Award;
        default:
            return null;
    }
};

const getRankColor = (rank: number) => {
    switch (rank) {
        case 1:
            return 'text-yellow-500';
        case 2:
            return 'text-gray-400';
        case 3:
            return 'text-amber-600';
        default:
            return 'text-muted-foreground';
    }
};

const getRankBg = (rank: number) => {
    switch (rank) {
        case 1:
            return 'bg-gradient-to-r from-yellow-500/10 to-transparent border-yellow-500/20';
        case 2:
            return 'bg-gradient-to-r from-gray-300/10 to-transparent border-gray-300/20';
        case 3:
            return 'bg-gradient-to-r from-amber-600/10 to-transparent border-amber-600/20';
        default:
            return 'bg-background border-border';
    }
};

const isCurrentUser = () => {
    return props.currentUserId === props.entry.user.id;
};

const getUserInitials = (name: string) => {
    if (!name) return '?';
    return name
        .split(' ')
        .map((word) => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2);
};
</script>

<template>
    <div
        class="flex items-center justify-between gap-2 rounded-lg border p-3 transition-all duration-200 sm:gap-4 sm:p-4"
        :class="[
            getRankBg(entry.rank),
            isCurrentUser() ? 'ring-2 ring-primary/50' : '',
        ]"
    >
        <div class="flex max-w-full min-w-0 items-center gap-2 sm:gap-4">
            <!-- Rank Number -->
            <div
                class="flex h-8 w-8 flex-shrink-0 items-center justify-center sm:h-10 sm:w-10"
            >
                <span
                    class="text-sm font-bold sm:text-lg"
                    :class="getRankColor(entry.rank)"
                >
                    #{{ entry.rank }}
                </span>
            </div>

            <!-- User Info -->
            <Link
                :href="`/profile/${entry.user.username}`"
                class="flex max-w-full min-w-0 cursor-pointer items-center gap-2 transition-opacity hover:opacity-80 sm:gap-3"
            >
                <!-- Avatar -->
                <div
                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary/10 sm:h-10 sm:w-10"
                >
                    <img
                        v-if="entry.user.avatar"
                        :src="entry.user.avatar"
                        :alt="entry.user.name"
                        class="h-8 w-8 rounded-full object-cover sm:h-10 sm:w-10"
                    />
                    <span
                        v-else
                        class="text-xs font-medium text-primary sm:text-sm"
                    >
                        {{ getUserInitials(entry.user.name) }}
                    </span>
                </div>

                <!-- Name and Badges -->
                <div class="min-w-0 overflow-hidden">
                    <div class="flex items-center gap-1 sm:gap-2">
                        <h4
                            class="truncate text-sm font-medium text-foreground sm:text-base"
                        >
                            {{ entry.user.name }}
                        </h4>
                        <component
                            v-if="getRankIcon(entry.rank)"
                            :is="getRankIcon(entry.rank)"
                            class="h-4 w-4 flex-shrink-0 sm:h-5 sm:w-5"
                            :class="getRankColor(entry.rank)"
                        />
                        <Badge
                            v-if="isCurrentUser()"
                            variant="outline"
                            class="text-xs"
                        >
                            {{ t('rankings.you') }}
                        </Badge>
                    </div>
                    <p
                        class="truncate text-xs text-muted-foreground sm:text-sm"
                    >
                        @{{ entry.user.username }}
                    </p>
                </div>
            </Link>
        </div>

        <!-- Points and Stats -->
        <div class="flex flex-shrink-0 items-center gap-2 text-right sm:gap-4">
            <div>
                <div class="flex items-center justify-end gap-1 sm:gap-2">
                    <Trophy class="h-3 w-3 text-primary sm:h-4 sm:w-4" />
                    <p class="text-sm font-bold text-foreground sm:text-lg">
                        {{ formatNumber(entry.points) }}
                    </p>
                </div>
                <p
                    v-if="entry.activities_count"
                    class="text-xs text-muted-foreground"
                >
                    <Activity class="inline h-2.5 w-2.5 sm:h-3 sm:w-3" />
                    {{ entry.activities_count }}
                    {{
                        entry.activities_count === 1
                            ? t('rankings.activity')
                            : t('rankings.activities')
                    }}
                </p>
                <p
                    v-if="showSeason && entry.season"
                    class="text-xs text-muted-foreground"
                >
                    {{
                        isRTL
                            ? `${t('rankings.q4_rankings')} ${entry.year}`
                            : `${entry.season} ${entry.year}`
                    }}
                </p>
                <p
                    v-if="showYear && entry.year && !entry.season"
                    class="text-xs text-muted-foreground"
                >
                    {{ entry.year }} {{ t('rankings.year') }}
                </p>
            </div>
        </div>
    </div>
</template>
