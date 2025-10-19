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
import { useTranslations } from '@/composables/useTranslations';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

interface Habit {
    id: number;
    name: string;
    icon?: string;
    activity_type_id: number | null;
    base_points: number;
    has_activity_today?: boolean;
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

// Get current locale and initialize translations
const { t, isRTL } = useTranslations();

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
    notes: '',
    memory_url: '',
});

// Filter out habits that already have activities today
const availableHabits = computed(() => {
    if (!props.habits || !Array.isArray(props.habits)) {
        return [];
    }
    return props.habits.filter((habit) => !habit.has_activity_today);
});

const selectedHabit = computed(() => {
    if (!props.habits || !Array.isArray(props.habits)) {
        return null;
    }
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

// Watch for modal state changes to handle cleanup
watch(() => props.open, (newValue) => {
    if (!newValue) {
        // Modal is closing, reset form and clear errors
        form.reset();
        form.clearErrors();
    }
});
</script>

<template>
    <Dialog :open="open" @update:open="handleClose">
        <DialogContent class="sm:max-w-[500px]">
            <div :dir="isRTL ? 'rtl' : 'ltr'">
                <DialogHeader>
                    <DialogTitle>{{
                        t('modal.log_activity.title')
                    }}</DialogTitle>
                    <DialogDescription>
                        {{ t('modal.log_activity.description') }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <!-- Today's Date Info -->
                    <div
                        class="rounded-lg border border-primary/20 bg-primary/5 p-3"
                    >
                        <p class="text-sm text-muted-foreground">
                            📅 {{ t('modal.log_activity.logging_for_today') }}
                            {{
                                new Date().toLocaleDateString('en-US', {
                                    month: 'short',
                                    day: 'numeric',
                                    year: 'numeric',
                                })
                            }}
                        </p>
                    </div>

                    <!-- Habit Selection -->
                    <div class="space-y-2">
                        <Label for="habit_id">{{
                            t('modal.log_activity.activity_type')
                        }}</Label>
                        <Select v-model="form.habit_id" required>
                            <SelectTrigger id="habit_id">
                                <SelectValue
                                    :placeholder="
                                        t('modal.log_activity.select_activity')
                                    "
                                >
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
                                    v-for="habit in availableHabits"
                                    :key="habit.id"
                                    :value="String(habit.id)"
                                >
                                    <span class="flex items-center gap-2">
                                        <span v-if="habit.icon">{{
                                            habit.icon
                                        }}</span>
                                        {{ habit.name || 'Unknown Activity' }}
                                    </span>
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.habit_id" />

                        <!-- No available activities message -->
                        <div
                            v-if="availableHabits.length === 0"
                            class="rounded-lg bg-muted/50 p-4 text-center"
                        >
                            <p class="text-sm text-muted-foreground">
                                🎉
                                {{
                                    t(
                                        'modal.log_activity.no_activities_subtitle',
                                    )
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-2">
                        <Label for="notes">{{
                            t('modal.log_activity.notes')
                        }}</Label>
                        <Textarea
                            id="notes"
                            v-model="form.notes"
                            :placeholder="
                                t('modal.log_activity.notes_placeholder')
                            "
                            :rows="3"
                            :maxlength="2000"
                        />
                        <InputError :message="form.errors.notes" />
                    </div>

                    <!-- Memory URL -->
                    <div class="space-y-2">
                        <Label for="memory_url">{{
                            t('modal.log_activity.memory_url')
                        }}</Label>
                        <Input
                            id="memory_url"
                            v-model="form.memory_url"
                            type="url"
                            :placeholder="
                                t('modal.log_activity.memory_url_placeholder')
                            "
                        />
                        <InputError :message="form.errors.memory_url" />
                        <p class="text-xs text-muted-foreground">
                            {{ t('modal.log_activity.memory_url_help') }}
                        </p>
                    </div>

                    <!-- Points Display (Read-only) -->
                    <div v-if="calculatedPoints" class="space-y-2">
                        <Label>{{
                            t('modal.log_activity.points_youll_earn')
                        }}</Label>
                        <div
                            class="rounded-lg border border-border bg-gradient-to-br from-muted/30 to-muted/50 p-4"
                        >
                            <div class="flex items-center justify-between">
                                <!-- Base Points -->
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-xl font-bold text-foreground"
                                    >
                                        {{ calculatedPoints.base }}
                                    </span>
                                    <span class="text-sm text-muted-foreground">
                                        {{ t('modal.log_activity.pts') }}
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
                                        {{ t('modal.log_activity.pts') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Streak Info -->
                            <p class="mt-3 text-xs text-muted-foreground">
                                <span v-if="calculatedPoints.hasBonus">
                                    🔥 {{ userStreak.current_streak
                                    }}{{
                                        t(
                                            'modal.log_activity.streak_bonus_applied',
                                        )
                                    }}
                                </span>
                                <span v-else>
                                    {{
                                        t('modal.log_activity.base_points_info')
                                    }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4">
                        <Button
                            type="button"
                            variant="outline"
                            @click="handleClose"
                            :disabled="form.processing"
                        >
                            {{ t('modal.log_activity.cancel') }}
                        </Button>
                        <Button
                            type="submit"
                            :disabled="
                                form.processing ||
                                !form.habit_id ||
                                availableHabits.length === 0
                            "
                        >
                            {{
                                form.processing
                                    ? t('modal.log_activity.logging')
                                    : t('modal.log_activity.log_activity')
                            }}
                        </Button>
                    </div>
                </form>
            </div>
        </DialogContent>
    </Dialog>
</template>
