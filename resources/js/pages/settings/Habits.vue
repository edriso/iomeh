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
    activity_type_id: number;
    custom_name: string;
    notes: string | null;
    display_order: number;
    activity_type: ActivityType;
}

interface Props {
    habits: Habit[];
    availableActivityTypes: Record<string, ActivityType[]>;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'My Activities settings',
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
        errors.value.habits =
            'You can have a maximum of 15 habits. Please remove some habits before adding new ones.';
        return;
    }

    const newHabit: Habit = {
        id: Date.now(), // Temporary ID for new habits
        activity_type_id: activityType.id,
        custom_name: activityType.name,
        notes: null,
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
        errors.value.habits = 'You must have at least one habit.';
        return false;
    }

    localHabits.value.forEach((habit, index) => {
        if (!habit.custom_name || habit.custom_name.trim().length === 0) {
            errors.value[`habits.${index}.custom_name`] = 'Name is required.';
        } else if (habit.custom_name.length > 100) {
            errors.value[`habits.${index}.custom_name`] =
                'Name must be 100 characters or less.';
        }

        if (habit.notes && habit.notes.length > 500) {
            errors.value[`habits.${index}.notes`] =
                'Notes must be 500 characters or less.';
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
        workout: 'bg-red-500/10 text-red-500 border-red-500/20',
        nutrition: 'bg-green-500/10 text-green-500 border-green-500/20',
    };
    return colors[category.toLowerCase()] || 'bg-gray-500/10 text-gray-500';
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="My Activities settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="My Activities settings"
                    description="Manage your health and wellness activities"
                />

                <!-- Habit Counter -->
                <div
                    class="flex items-center justify-between rounded-lg border bg-muted/50 p-4"
                >
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10"
                        >
                            <span class="text-sm font-medium text-primary">{{
                                localHabits.length
                            }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Current Habits</p>
                            <p class="text-xs text-muted-foreground">
                                Maximum of 15 habits allowed
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-muted-foreground">
                            {{ localHabits.length }}/15
                        </div>
                        <div class="h-2 w-20 rounded-full bg-muted">
                            <div
                                class="h-2 rounded-full bg-primary transition-all duration-300"
                                :style="{
                                    width: `${(localHabits.length / 15) * 100}%`,
                                }"
                            ></div>
                        </div>
                    </div>
                </div>

                <Alert>
                    <Info class="h-4 w-4" />
                    <AlertDescription>
                        Add the activities that matter most to your health
                        journey.
                    </AlertDescription>
                </Alert>

                <div class="space-y-4">
                    <!-- Current Habits -->
                    <div v-if="localHabits.length > 0" class="space-y-3">
                        <div ref="habitsContainer" class="space-y-3">
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
                        class="rounded-lg border-2 border-dashed py-12 text-center"
                    >
                        <p class="mb-4 text-muted-foreground">
                            No habits added yet. Add your first habit to start
                            tracking activities!
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
                        <Plus class="mr-2 h-4 w-4" />
                        Add Activity
                    </Button>

                    <p
                        v-if="availableTypes.length === 0"
                        class="text-center text-sm text-muted-foreground"
                    >
                        You've added all available activity types!
                    </p>

                    <p
                        v-else-if="localHabits.length >= 15"
                        class="text-center text-sm text-muted-foreground"
                    >
                        Maximum of 15 habits reached. Remove some habits to add
                        new ones.
                    </p>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3 pt-4">
                        <Button
                            @click="handleSubmit"
                            :disabled="localHabits.length === 0"
                        >
                            <Save class="mr-2 h-4 w-4" />
                            Save Changes
                        </Button>
                        <Button
                            variant="outline"
                            @click="
                                localHabits = JSON.parse(
                                    JSON.stringify(props.habits),
                                )
                            "
                        >
                            Reset
                        </Button>
                        <span
                            v-if="recentlySuccessful"
                            class="text-sm text-muted-foreground"
                        >
                            Saved successfully!
                        </span>
                    </div>
                </div>
            </div>
        </SettingsLayout>

        <!-- Add Activity Dialog -->
        <Dialog v-model:open="showAddDialog">
            <DialogContent class="max-h-[80vh] max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Add New Activity</DialogTitle>
                    <DialogDescription>
                        Select an activity type to add to your activities
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <!-- Category Filter -->
                    <div class="flex flex-wrap gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            :class="{
                                'bg-primary text-primary-foreground':
                                    selectedCategory === null,
                            }"
                            @click="selectedCategory = null"
                        >
                            All
                        </Button>
                        <Button
                            v-for="category in categories"
                            :key="category"
                            variant="outline"
                            size="sm"
                            class="capitalize"
                            :class="{
                                'bg-primary text-primary-foreground':
                                    selectedCategory === category,
                            }"
                            @click="selectedCategory = category"
                        >
                            {{ category }}
                        </Button>
                    </div>

                    <!-- Activity Types List -->
                    <ScrollArea class="h-[400px] pr-4">
                        <div class="space-y-2">
                            <Card
                                v-for="type in filteredAvailableTypes"
                                :key="type.id"
                                class="cursor-pointer transition-colors hover:border-primary"
                                @click="addHabit(type)"
                            >
                                <CardHeader class="p-4">
                                    <div class="flex items-center gap-3">
                                        <span v-if="type.icon" class="text-3xl">
                                            {{ type.icon }}
                                        </span>
                                        <div class="flex-1">
                                            <CardTitle class="text-base">
                                                {{ type.name }}
                                            </CardTitle>
                                            <CardDescription
                                                v-if="type.description"
                                                class="mt-1 text-xs"
                                            >
                                                {{ type.description }}
                                            </CardDescription>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <Badge
                                                variant="outline"
                                                :class="
                                                    getCategoryColor(
                                                        type.category,
                                                    )
                                                "
                                            >
                                                {{ type.category }}
                                            </Badge>
                                            <span
                                                class="text-sm font-medium text-primary"
                                            >
                                                {{ type.base_points }} pts
                                            </span>
                                        </div>
                                    </div>
                                </CardHeader>
                            </Card>

                            <div
                                v-if="filteredAvailableTypes.length === 0"
                                class="py-8 text-center text-muted-foreground"
                            >
                                No available activity types in this category
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
