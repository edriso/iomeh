<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { useNumberFormat } from '@/composables/useNumberFormat';
import { Link } from '@inertiajs/vue3';
import { Activity, Award, Crown, Trophy } from 'lucide-vue-next';

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
        class="flex items-center justify-between gap-4 rounded-lg border p-4 transition-all duration-200"
        :class="[
            getRankBg(entry.rank),
            isCurrentUser() ? 'ring-2 ring-primary/50' : '',
        ]"
    >
        <div class="flex max-w-full min-w-0 items-center gap-4">
            <!-- Rank Number -->
            <div
                class="flex h-10 w-10 flex-shrink-0 items-center justify-center"
            >
                <span
                    class="text-lg font-bold"
                    :class="getRankColor(entry.rank)"
                >
                    #{{ entry.rank }}
                </span>
            </div>

            <!-- User Info -->
            <Link
                :href="`/profile/${entry.user.username}`"
                class="flex max-w-full min-w-0 cursor-pointer items-center gap-3 transition-opacity hover:opacity-80"
            >
                <!-- Avatar -->
                <div
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary/10"
                >
                    <img
                        v-if="entry.user.avatar"
                        :src="entry.user.avatar"
                        :alt="entry.user.name"
                        class="h-10 w-10 rounded-full object-cover"
                    />
                    <span v-else class="text-sm font-medium text-primary">
                        {{ getUserInitials(entry.user.name) }}
                    </span>
                </div>

                <!-- Name and Badges -->
                <div class="min-w-0 overflow-hidden">
                    <div class="flex items-center gap-2">
                        <h4 class="truncate font-medium text-foreground">
                            {{ entry.user.name }}
                        </h4>
                        <component
                            v-if="getRankIcon(entry.rank)"
                            :is="getRankIcon(entry.rank)"
                            class="h-5 w-5 flex-shrink-0"
                            :class="getRankColor(entry.rank)"
                        />
                        <Badge
                            v-if="isCurrentUser()"
                            variant="outline"
                            class="text-xs"
                        >
                            You
                        </Badge>
                    </div>
                    <p class="truncate text-sm text-muted-foreground">
                        @{{ entry.user.username }}
                    </p>
                </div>
            </Link>
        </div>

        <!-- Points and Stats -->
        <div class="flex flex-shrink-0 items-center gap-4 text-right">
            <div>
                <div class="flex items-center justify-end gap-2">
                    <Trophy class="h-4 w-4 text-primary" />
                    <p class="text-lg font-bold text-foreground">
                        {{ formatNumber(entry.points) }}
                    </p>
                </div>
                <p
                    v-if="entry.activities_count"
                    class="text-xs text-muted-foreground"
                >
                    <Activity class="inline h-3 w-3" />
                    {{ entry.activities_count }}
                    {{
                        entry.activities_count === 1 ? 'activity' : 'activities'
                    }}
                </p>
                <p
                    v-if="showSeason && entry.season"
                    class="text-xs text-muted-foreground"
                >
                    {{ entry.season }} {{ entry.year }}
                </p>
                <p
                    v-if="showYear && entry.year && !entry.season"
                    class="text-xs text-muted-foreground"
                >
                    {{ entry.year }} Year
                </p>
            </div>
        </div>
    </div>
</template>
