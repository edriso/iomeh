<script setup lang="ts">
import { Activity } from 'lucide-vue-next';
import RankingsEntry from './RankingsEntry.vue';

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
    rankings: RankingsEntry[];
    currentUserId?: number;
    emptyMessage?: string;
    showSeason?: boolean;
    showYear?: boolean;
}

withDefaults(defineProps<Props>(), {
    emptyMessage: 'No activities yet',
    showSeason: false,
    showYear: false,
});
</script>

<template>
    <div
        v-if="rankings.length === 0"
        class="py-12 text-center text-muted-foreground"
    >
        <Activity class="mx-auto mb-4 h-12 w-12 opacity-50" />
        <p>{{ emptyMessage }}</p>
    </div>
    <div v-else class="space-y-2">
        <RankingsEntry
            v-for="entry in rankings"
            :key="`${entry.user.id}-${entry.rank}`"
            :entry="entry"
            :current-user-id="currentUserId"
            :show-season="showSeason"
            :show-year="showYear"
        />
    </div>
</template>
