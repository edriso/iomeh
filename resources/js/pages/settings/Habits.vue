<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import Sortable from 'sortablejs';
import { computed, nextTick, onMounted, ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import SortableHabitItem from '@/components/SortableHabitItem.vue';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ScrollArea } from '@/components/ui/scroll-area';
import { useTranslations } from '@/composables/useTranslations';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit, update } from '@/routes/habits';
import { type BreadcrumbItem } from '@/types';
import { Info, Plus, Save } from 'lucide-vue-next';

interface ActivityType {
    id: number;
    name: string;
    category: string;
    base_points: number;
    icon: string | null;
    description: string | null;
}

interface Habit {
    id: number;
    activity_type_id: number | null;
    custom_name: string;
    custom_icon: string | null;
    notes: string | null;
    display_order: number;
    activity_type: ActivityType | null;
}

interface Props {
    habits: Habit[];
    availableActivityTypes: Record<string, ActivityType[]>;
}

const props = defineProps<Props>();

// Use translations with reactive locale
const { t, isRTL } = useTranslations();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('habits.title'),
        href: edit().url,
    },
];

// Local state for managing habits
const localHabits = ref<Habit[]>(JSON.parse(JSON.stringify(props.habits)));
const showAddDialog = ref(false);
const selectedCategory = ref<string | null>(null);

// Drag and drop setup
const habitsContainer = ref<HTMLElement | null>(null);

// Form validation errors
const errors = ref<Record<string, string>>({});
const recentlySuccessful = ref(false);

// Computed
const selectedActivityTypeIds = computed(() =>
    localHabits.value.map((i) => i.activity_type_id),
);

const availableTypes = computed(() => {
    const types: ActivityType[] = [];
    Object.values(props.availableActivityTypes).forEach((categoryTypes) => {
        categoryTypes.forEach((type) => {
            if (!selectedActivityTypeIds.value.includes(type.id)) {
                types.push(type);
            }
        });
    });
    return types;
});

const filteredAvailableTypes = computed(() => {
    if (!selectedCategory.value) return availableTypes.value;
    return availableTypes.value.filter(
        (type) => type.category === selectedCategory.value,
    );
});

const categories = computed(() => {
    return Object.keys(props.availableActivityTypes);
});

// Functions
function addHabit(activityType: ActivityType) {
    // Check if user has reached the maximum number of habits
    if (localHabits.value.length >= 15) {
        errors.value.habits = t('habits.maximum_reached');
        return;
    }

    const newHabit: Habit = {
        id: Date.now(), // Temporary ID for new habits
        activity_type_id: activityType.id,
        custom_name: activityType.name,
        custom_icon: null,
        notes: activityType.description || null, // Auto-populate with activity description
        display_order: localHabits.value.length,
        activity_type: activityType,
    };

    localHabits.value.push(newHabit);
    showAddDialog.value = false;
    selectedCategory.value = null;

    // Clear any previous errors
    if (errors.value.habits) {
        delete errors.value.habits;
    }
}

function removeHabit(index: number) {
    localHabits.value.splice(index, 1);
    // Reorder remaining habits
    localHabits.value.forEach((habit, idx) => {
        habit.display_order = idx;
    });
}

// Initialize sortable after component mounts
onMounted(() => {
    nextTick(() => {
        if (habitsContainer.value) {
            new Sortable(habitsContainer.value, {
                animation: 200,
                ghostClass: 'opacity-50',
                chosenClass: '',
                dragClass: 'sortable-dragging',
                onEnd: (evt: any) => {
                    const { oldIndex, newIndex } = evt;
                    if (
                        oldIndex !== undefined &&
                        newIndex !== undefined &&
                        oldIndex !== newIndex
                    ) {
                        // Move item in array
                        const item = localHabits.value.splice(oldIndex, 1)[0];
                        localHabits.value.splice(newIndex, 0, item);

                        // Update display orders
                        localHabits.value.forEach((habit, idx) => {
                            habit.display_order = idx;
                        });
                    }
                },
            });
        }
    });
});

function validateForm() {
    errors.value = {};

    if (localHabits.value.length === 0) {
        errors.value.habits = t('validation.required');
        return false;
    }

    localHabits.value.forEach((habit, index) => {
        if (!habit.custom_name || habit.custom_name.trim().length === 0) {
            errors.value[`habits.${index}.custom_name`] = t(
                'validation.habit_name.required',
            );
        } else if (habit.custom_name.length > 100) {
            errors.value[`habits.${index}.custom_name`] = t(
                'validation.habit_name.max',
            );
        }

        if (habit.notes && habit.notes.length > 500) {
            errors.value[`habits.${index}.notes`] = t(
                'validation.habit_notes.max',
            );
        }

        if (habit.custom_icon && habit.custom_icon.length > 50) {
            errors.value[`habits.${index}.custom_icon`] = t(
                'validation.max.string',
                '50',
            );
        }
    });

    return Object.keys(errors.value).length === 0;
}

function handleSubmit() {
    if (!validateForm()) {
        return;
    }

    const formData = {
        habits: localHabits.value.map((habit) => ({
            activity_type_id: habit.activity_type_id,
            custom_name: habit.custom_name,
            custom_icon: habit.custom_icon || null,
            notes: habit.notes || null,
        })),
    };

    router.put(update().url, formData, {
        preserveScroll: true,
        onSuccess: () => {
            recentlySuccessful.value = true;
            setTimeout(() => {
                recentlySuccessful.value = false;
            }, 3000);
        },
        onError: (serverErrors) => {
            errors.value = serverErrors;
        },
    });
}

function getCategoryColor(category: string): string {
    const colors: Record<string, string> = {
        workout: 'bg-orange-500/10 text-orange-500 border-orange-500/20',
        nutrition: 'bg-green-500/10 text-green-500 border-green-500/20',
    };
    return colors[category.toLowerCase()] || 'bg-gray-500/10 text-gray-500';
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems" :dir="isRTL ? 'rtl' : 'ltr'">
        <Head :title="t('habits.title')" />

        <SettingsLayout>
            <div class="space-y-4 sm:space-y-6">
                <HeadingSmall
                    :title="t('habits.title')"
                    :description="t('habits.description')"
                />

                <!-- Habit Counter -->
                <div
                    class="flex flex-col gap-3 rounded-lg border bg-muted/50 p-3 sm:flex-row sm:items-center sm:justify-between sm:gap-0 sm:p-4"
                >
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 sm:h-8 sm:w-8"
                        >
                            <span
                                class="text-xs font-medium text-primary sm:text-sm"
                                >{{ localHabits.length }}</span
                            >
                        </div>
                        <div>
                            <p class="text-xs font-medium sm:text-sm">
                                {{ t('habits.current_habits') }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ t('habits.maximum_habits_allowed') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <div
                            class="text-xs font-medium text-muted-foreground sm:text-sm"
                        >
                            {{ localHabits.length }}/15
                        </div>
                        <div
                            class="h-1.5 w-16 rounded-full bg-muted sm:h-2 sm:w-20"
                        >
                            <div
                                class="h-1.5 rounded-full bg-primary transition-all duration-300 sm:h-2"
                                :style="{
                                    width: `${(localHabits.length / 15) * 100}%`,
                                }"
                            ></div>
                        </div>
                    </div>
                </div>

                <Alert>
                    <Info class="h-3 w-3 sm:h-4 sm:w-4" />
                    <AlertDescription class="text-xs sm:text-sm">
                        {{ t('habits.add_activities_description') }}
                    </AlertDescription>
                </Alert>

                <div class="space-y-3 sm:space-y-4">
                    <!-- Current Habits -->
                    <div
                        v-if="localHabits.length > 0"
                        class="space-y-2 sm:space-y-3"
                    >
                        <div
                            ref="habitsContainer"
                            class="space-y-2 sm:space-y-3"
                        >
                            <SortableHabitItem
                                v-for="(habit, index) in localHabits"
                                :key="habit.id"
                                :habit="habit"
                                :index="index"
                                :errors="errors"
                                :on-remove="removeHabit"
                                :get-category-color="getCategoryColor"
                                @update:habit="
                                    (updatedHabit) =>
                                        (localHabits[index] = updatedHabit)
                                "
                            />
                        </div>
                    </div>

                    <div
                        v-else
                        class="rounded-lg border-2 border-dashed py-8 text-center sm:py-12"
                    >
                        <p
                            class="mb-3 text-sm text-muted-foreground sm:mb-4 sm:text-base"
                        >
                            {{ t('habits.no_habits_added') }}
                        </p>
                    </div>

                    <InputError :message="errors.habits" />

                    <!-- Add Activity Button -->
                    <Button
                        variant="outline"
                        class="w-full"
                        @click="showAddDialog = true"
                        :disabled="
                            availableTypes.length === 0 ||
                            localHabits.length >= 15
                        "
                    >
                        <Plus class="mr-1.5 h-3 w-3 sm:mr-2 sm:h-4 sm:w-4" />
                        <span class="text-sm sm:text-base">{{
                            t('habits.add_activity')
                        }}</span>
                    </Button>

                    <p
                        v-if="availableTypes.length === 0"
                        class="text-center text-xs text-muted-foreground sm:text-sm"
                    >
                        {{ t('habits.youve_added_all') }}
                    </p>

                    <p
                        v-else-if="localHabits.length >= 15"
                        class="text-center text-xs text-muted-foreground sm:text-sm"
                    >
                        {{ t('habits.maximum_reached') }}
                    </p>

                    <!-- Action Buttons -->
                    <div
                        class="flex flex-col gap-3 pt-3 sm:flex-row sm:items-center sm:gap-3 sm:pt-4"
                    >
                        <div class="flex flex-col gap-2 sm:flex-row sm:gap-3">
                            <Button
                                @click="handleSubmit"
                                :disabled="localHabits.length === 0"
                                class="w-full sm:w-auto"
                            >
                                <Save
                                    class="mr-1.5 h-3 w-3 sm:mr-2 sm:h-4 sm:w-4"
                                />
                                <span class="text-sm sm:text-base">{{
                                    t('common.save')
                                }}</span>
                            </Button>
                            <Button
                                variant="outline"
                                class="w-full sm:w-auto"
                                @click="
                                    localHabits = JSON.parse(
                                        JSON.stringify(props.habits),
                                    )
                                "
                            >
                                <span class="text-sm sm:text-base">{{
                                    t('habits.reset')
                                }}</span>
                            </Button>
                        </div>
                        <span
                            v-if="recentlySuccessful"
                            class="text-xs text-muted-foreground sm:text-sm"
                        >
                            {{ t('habits.saved_successfully') }}
                        </span>
                    </div>
                </div>
            </div>
        </SettingsLayout>

        <!-- Add Activity Dialog -->
        <Dialog v-model:open="showAddDialog">
            <DialogContent
                class="max-h-[85vh] w-[calc(100%-1rem)] max-w-sm sm:mx-0 sm:max-w-2xl lg:max-w-3xl"
            >
                <DialogHeader class="space-y-2 sm:space-y-3">
                    <DialogTitle class="text-lg sm:text-xl lg:text-2xl">{{
                        t('habits.add_new_activity')
                    }}</DialogTitle>
                    <DialogDescription class="text-sm sm:text-base">
                        {{ t('habits.select_activity_type') }}
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-2 sm:space-y-3 lg:space-y-4">
                    <!-- Category Filter -->
                    <div class="flex flex-wrap gap-1 sm:gap-1.5 lg:gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            class="px-2 py-1 text-xs sm:px-3 sm:py-1.5 sm:text-sm"
                            :class="{
                                'bg-primary text-primary-foreground dark:bg-primary dark:text-primary-foreground':
                                    selectedCategory === null,
                            }"
                            @click="selectedCategory = null"
                        >
                            {{ t('habits.all') }}
                        </Button>
                        <Button
                            v-for="category in categories"
                            :key="category"
                            variant="outline"
                            size="sm"
                            class="px-2 py-1 text-xs capitalize sm:px-3 sm:py-1.5 sm:text-sm"
                            :class="{
                                'bg-primary text-primary-foreground dark:bg-primary dark:text-primary-foreground':
                                    selectedCategory === category,
                            }"
                            @click="selectedCategory = category"
                        >
                            {{ t(`habits.${category}`) }}
                        </Button>
                    </div>

                    <!-- Activity Types List -->
                    <ScrollArea
                        class="h-[250px] pr-1 sm:h-[350px] sm:pr-2 lg:h-[400px] lg:pr-4"
                    >
                        <div class="space-y-1 sm:space-y-1.5 lg:space-y-2">
                            <Card
                                v-for="type in filteredAvailableTypes"
                                :key="type.id"
                                class="cursor-pointer transition-all duration-200 hover:border-primary hover:shadow-sm"
                                @click="addHabit(type)"
                            >
                                <CardHeader class="p-2.5 sm:p-3 lg:p-4">
                                    <div
                                        class="flex items-start gap-2 sm:gap-3"
                                    >
                                        <span
                                            v-if="type.icon"
                                            class="flex-shrink-0 text-xl sm:text-2xl lg:text-3xl"
                                        >
                                            {{ type.icon }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <CardTitle
                                                class="text-sm font-medium sm:text-base"
                                            >
                                                {{ type.name }}
                                            </CardTitle>
                                            <CardDescription
                                                v-if="type.description"
                                                class="mt-0.5 text-xs text-muted-foreground sm:mt-1"
                                            >
                                                {{ type.description }}
                                            </CardDescription>
                                        </div>
                                        <div
                                            class="flex flex-shrink-0 flex-col items-end gap-1 sm:flex-row sm:items-center sm:gap-2"
                                        >
                                            <Badge
                                                variant="outline"
                                                class="px-1.5 py-0.5 text-xs"
                                                :class="
                                                    getCategoryColor(
                                                        type.category,
                                                    )
                                                "
                                            >
                                                {{
                                                    t(`habits.${type.category}`)
                                                }}
                                            </Badge>
                                            <span
                                                class="text-xs font-semibold text-primary sm:text-sm"
                                            >
                                                {{ type.base_points }}
                                                {{ t('habits.points') }}
                                            </span>
                                        </div>
                                    </div>
                                </CardHeader>
                            </Card>

                            <div
                                v-if="filteredAvailableTypes.length === 0"
                                class="py-8 text-center text-muted-foreground sm:py-12"
                            >
                                <div class="flex flex-col items-center gap-2">
                                    <div class="text-4xl opacity-50">🔍</div>
                                    <p class="text-sm font-medium sm:text-base">
                                        {{ t('habits.no_available_types') }}
                                    </p>
                                    <p
                                        class="text-xs text-muted-foreground sm:text-sm"
                                    >
                                        {{ t('habits.try_different_category') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </ScrollArea>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<style>
.sortable-dragging {
    opacity: 0.8;
    transform: rotate(1deg);
}
</style>
