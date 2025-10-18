<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/**
 * Calculate current season dates
 * Q1 = Jan-Mar, Q2 = Apr-Jun, Q3 = Jul-Sep, Q4 = Oct-Dec
 */
function getCurrentSeasonDates() {
    const now = new Date();
    const currentMonth = now.getMonth() + 1; // 1-12
    const currentYear = now.getFullYear();
    const seasonNumber = Math.ceil(currentMonth / 3); // 1-4
    
    const startMonths = { 1: 0, 2: 3, 3: 6, 4: 9 }; // 0-indexed
    const endMonths = { 1: 2, 2: 5, 3: 8, 4: 11 };   // 0-indexed
    
    const startDate = new Date(currentYear, startMonths[seasonNumber], 1);
    const endDate = new Date(currentYear, endMonths[seasonNumber] + 1, 0); // Last day of month
    
    return {
        start: startDate,
        end: endDate,
        seasonNumber,
        seasonName: `Q${seasonNumber}`,
    };
}

interface Habit {
    id: number;
    name: string;
    icon?: string;
    activity_type_id: number;
    base_points: number;
}

interface Props {
    open: boolean;
    habits: Habit[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const page = usePage();

// Get user streak data from shared auth
const userStreak = computed(() => {
    const authUser = page.props.auth?.user as any;
    return {
        current_streak: authUser?.current_streak ?? 0,
        multiplier: authUser?.streak_multiplier ?? 1.0,
        tier_name: authUser?.streak_tier?.name ?? 'Newcomer',
        tier_icon: authUser?.streak_tier?.icon ?? '🌱',
    };
});

const form = useForm({
    habit_id: '',
    date: new Date().toISOString().split('T')[0],
    notes: '',
    memory_url: '',
});

const selectedHabit = computed(() => {
    return props.habits.find((i) => i.id === Number(form.habit_id));
});

const calculatedPoints = computed(() => {
    if (!selectedHabit.value) return null;

    const basePoints = selectedHabit.value.base_points;
    const multiplier = userStreak.value.multiplier;
    const finalPoints = Math.round(basePoints * multiplier);

    return {
        base: basePoints,
        multiplier: multiplier,
        final: finalPoints,
        hasBonus: multiplier > 1.0,
    };
});

// Get current season date boundaries
const currentSeason = computed(() => getCurrentSeasonDates());

// Min date for activity logging (start of current season)
const minDate = computed(() => {
    return currentSeason.value.start.toISOString().split('T')[0];
});

// Max date for activity logging (today or end of season, whichever is earlier)
const maxDate = computed(() => {
    const today = new Date();
    const seasonEnd = currentSeason.value.end;
    const maxAllowedDate = today < seasonEnd ? today : seasonEnd;
    return maxAllowedDate.toISOString().split('T')[0];
});

// Helper text for date restrictions
const dateHelperText = computed(() => {
    const season = currentSeason.value;
    const startFormatted = season.start.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric' 
    });
    const endFormatted = season.end.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric',
        year: 'numeric'
    });
    return `Activities can only be logged for ${season.seasonName} (${startFormatted} - ${endFormatted})`;
});

const handleSubmit = () => {
    form.post('/activities', {
        preserveScroll: true,
        onSuccess: () => {
            emit('update:open', false);
            form.reset();
        },
    });
};

const handleClose = () => {
    emit('update:open', false);
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <Dialog :open="open" @update:open="handleClose">
        <DialogContent class="sm:max-w-[500px]">
            <DialogHeader>
                <DialogTitle>Log Activity</DialogTitle>
                <DialogDescription>
                    Track your health activities and earn points
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="handleSubmit" class="space-y-4">
                <!-- Habit Selection -->
                <div class="space-y-2">
                    <Label for="habit_id">Activity Type</Label>
                    <Select v-model="form.habit_id" required>
                        <SelectTrigger id="habit_id">
                            <SelectValue placeholder="Select an activity">
                                <span
                                    v-if="selectedHabit"
                                    class="flex items-center gap-2"
                                >
                                    <span v-if="selectedHabit.icon">{{
                                        selectedHabit.icon
                                    }}</span>
                                    {{ selectedHabit.name }}
                                </span>
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="habit in habits"
                                :key="habit.id"
                                :value="String(habit.id)"
                            >
                                <span class="flex items-center gap-2">
                                    <span v-if="habit.icon">{{
                                        habit.icon
                                    }}</span>
                                    {{ habit.name }}
                                </span>
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.habit_id" />
                </div>

                <!-- Date -->
                <div class="space-y-2">
                    <Label for="date">Date</Label>
                    <Input
                        id="date"
                        v-model="form.date"
                        type="date"
                        :min="minDate"
                        :max="maxDate"
                        required
                    />
                    <p class="text-xs text-muted-foreground">
                        {{ dateHelperText }}
                    </p>
                    <InputError :message="form.errors.date" />
                </div>

                <!-- Points Display (Read-only) -->
                <div v-if="calculatedPoints" class="space-y-2">
                    <Label>Points You'll Earn</Label>
                    <div
                        class="rounded-lg border border-border bg-gradient-to-br from-muted/30 to-muted/50 p-4"
                    >
                        <div class="flex items-center justify-between">
                            <!-- Base Points -->
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-bold text-foreground">
                                    {{ calculatedPoints.base }}
                                </span>
                                <span class="text-sm text-muted-foreground">
                                    pts
                                </span>
                            </div>

                            <!-- Multiplier (if applicable) -->
                            <div
                                v-if="calculatedPoints.hasBonus"
                                class="flex items-center gap-2"
                            >
                                <span class="text-muted-foreground">×</span>
                                <div
                                    class="flex items-center gap-1 rounded-full bg-amber-500/10 px-2 py-1 text-xs font-semibold text-amber-700 dark:text-amber-400"
                                >
                                    <span
                                        >{{
                                            calculatedPoints.multiplier
                                        }}×</span
                                    >
                                    <span class="text-[10px]"
                                        >({{ userStreak.tier_icon }}
                                        {{ userStreak.tier_name }})</span
                                    >
                                </div>
                            </div>

                            <!-- Arrow -->
                            <div
                                v-if="calculatedPoints.hasBonus"
                                class="text-muted-foreground"
                            >
                                →
                            </div>

                            <!-- Final Points -->
                            <div
                                class="flex items-center gap-2 rounded-lg bg-primary/10 px-3 py-2"
                            >
                                <span
                                    class="text-2xl font-bold text-primary"
                                    :class="{
                                        'text-amber-600 dark:text-amber-500':
                                            calculatedPoints.hasBonus,
                                    }"
                                >
                                    {{ calculatedPoints.final }}
                                </span>
                                <span class="text-sm text-muted-foreground">
                                    pts
                                </span>
                            </div>
                        </div>

                        <!-- Streak Info -->
                        <p class="mt-3 text-xs text-muted-foreground">
                            <span v-if="calculatedPoints.hasBonus">
                                🔥 {{ userStreak.current_streak }}-day streak
                                bonus applied!
                            </span>
                            <span v-else>
                                Base points • Build a streak to multiply your
                                rewards
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Notes -->
                <div class="space-y-2">
                    <Label for="notes">Notes (Optional)</Label>
                    <Textarea
                        id="notes"
                        v-model="form.notes"
                        placeholder="Add any notes about this activity..."
                        :rows="3"
                        :maxlength="2000"
                    />
                    <InputError :message="form.errors.notes" />
                </div>

                <!-- Memory URL -->
                <div class="space-y-2">
                    <Label for="memory_url">Memory URL (Optional)</Label>
                    <Input
                        id="memory_url"
                        v-model="form.memory_url"
                        type="url"
                        placeholder="https://..."
                    />
                    <InputError :message="form.errors.memory_url" />
                    <p class="text-xs text-muted-foreground">
                        Link to memory (workout screenshot, meal photo, etc.)
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-4">
                    <Button
                        type="button"
                        variant="outline"
                        @click="handleClose"
                        :disabled="form.processing"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        :disabled="form.processing || !form.habit_id"
                    >
                        {{ form.processing ? 'Logging...' : 'Log Activity' }}
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>
