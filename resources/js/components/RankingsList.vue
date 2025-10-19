<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';
import { usePage } from '@inertiajs/vue3';
import { Activity } from 'lucide-vue-next';
import RankingsEntry from './RankingsEntry.vue';

// Get current locale from shared Inertia data and initialize
const page = usePage();
const initialLocale = (page.props.currentLocale as string) || 'en';

const { t } = useTranslations(initialLocale);

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

const props = withDefaults(defineProps<Props>(), {
    emptyMessage: '',
    showSeason: false,
    showYear: false,
});

const defaultEmptyMessage = t('rankings.no_activities_yet');
const displayEmptyMessage = props.emptyMessage || defaultEmptyMessage;
</script>

<template>
    <div
        v-if="rankings.length === 0"
        class="py-12 text-center text-muted-foreground"
    >
        <Activity class="mx-auto mb-4 h-12 w-12 opacity-50" />
        <p>{{ displayEmptyMessage }}</p>
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
