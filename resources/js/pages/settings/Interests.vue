<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit, update } from '@/routes/interests';
import { type BreadcrumbItem } from '@/types';
import { GripVertical, Info, Plus, Save, Trash2 } from 'lucide-vue-next';

interface ActivityType {
    id: number;
    name: string;
    category: string;
    base_points: number;
    icon: string | null;
    description: string | null;
}

interface Interest {
    id: number;
    activity_type_id: number;
    custom_name: string;
    notes: string | null;
    display_order: number;
    activity_type: ActivityType;
}

interface Props {
    interests: Interest[];
    availableActivityTypes: Record<string, ActivityType[]>;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'My Activities settings',
        href: edit().url,
    },
];

// Local state for managing interests
const localInterests = ref<Interest[]>(
    JSON.parse(JSON.stringify(props.interests)),
);
const showAddDialog = ref(false);
const selectedCategory = ref<string | null>(null);

// Form validation errors
const errors = ref<Record<string, string>>({});
const recentlySuccessful = ref(false);

// Computed
const selectedActivityTypeIds = computed(() =>
    localInterests.value.map((i) => i.activity_type_id),
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
function addInterest(activityType: ActivityType) {
    const newInterest: Interest = {
        id: Date.now(), // Temporary ID for new interests
        activity_type_id: activityType.id,
        custom_name: activityType.name,
        notes: null,
        display_order: localInterests.value.length,
        activity_type: activityType,
    };

    localInterests.value.push(newInterest);
    showAddDialog.value = false;
    selectedCategory.value = null;
}

function removeInterest(index: number) {
    localInterests.value.splice(index, 1);
    // Reorder remaining interests
    localInterests.value.forEach((interest, idx) => {
        interest.display_order = idx;
    });
}

function moveUp(index: number) {
    if (index > 0) {
        const temp = localInterests.value[index];
        localInterests.value[index] = localInterests.value[index - 1];
        localInterests.value[index - 1] = temp;
        // Update display orders
        localInterests.value.forEach((interest, idx) => {
            interest.display_order = idx;
        });
    }
}

function validateForm() {
    errors.value = {};

    if (localInterests.value.length === 0) {
        errors.value.interests = 'You must have at least one interest.';
        return false;
    }

    localInterests.value.forEach((interest, index) => {
        if (!interest.custom_name || interest.custom_name.trim().length === 0) {
            errors.value[`interests.${index}.custom_name`] =
                'Name is required.';
        } else if (interest.custom_name.length > 100) {
            errors.value[`interests.${index}.custom_name`] =
                'Name must be 100 characters or less.';
        }

        if (interest.notes && interest.notes.length > 500) {
            errors.value[`interests.${index}.notes`] =
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
        interests: localInterests.value.map((interest) => ({
            activity_type_id: interest.activity_type_id,
            custom_name: interest.custom_name,
            notes: interest.notes || null,
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
        wellness: 'bg-blue-500/10 text-blue-500 border-blue-500/20',
        mindfulness: 'bg-purple-500/10 text-purple-500 border-purple-500/20',
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

                <Alert>
                    <Info class="h-4 w-4" />
                    <AlertDescription>
                        Your interests determine which activities you can log.
                        Add the activities that matter most to your health
                        journey.
                    </AlertDescription>
                </Alert>

                <div class="space-y-4">
                    <!-- Current Interests -->
                    <div v-if="localInterests.length > 0" class="space-y-3">
                        <Card
                            v-for="(interest, index) in localInterests"
                            :key="interest.id"
                            class="relative"
                        >
                            <CardHeader class="pb-3">
                                <div
                                    class="flex items-start justify-between gap-4"
                                >
                                    <div class="flex flex-1 items-center gap-3">
                                        <div class="flex items-center gap-1">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                class="h-8 w-8 p-0"
                                                :disabled="index === 0"
                                                @click="moveUp(index)"
                                            >
                                                <GripVertical class="h-4 w-4" />
                                            </Button>
                                        </div>
                                        <div class="flex-1">
                                            <div
                                                class="mb-2 flex items-center gap-2"
                                            >
                                                <span
                                                    v-if="
                                                        interest.activity_type
                                                            .icon
                                                    "
                                                    class="text-2xl"
                                                >
                                                    {{
                                                        interest.activity_type
                                                            .icon
                                                    }}
                                                </span>
                                                <Badge
                                                    variant="outline"
                                                    :class="
                                                        getCategoryColor(
                                                            interest
                                                                .activity_type
                                                                .category,
                                                        )
                                                    "
                                                >
                                                    {{
                                                        interest.activity_type
                                                            .category
                                                    }}
                                                </Badge>
                                                <span
                                                    class="text-xs text-muted-foreground"
                                                >
                                                    {{
                                                        interest.activity_type
                                                            .base_points
                                                    }}
                                                    pts
                                                </span>
                                            </div>
                                            <div class="space-y-3">
                                                <div>
                                                    <Label
                                                        :for="`name-${interest.id}`"
                                                    >
                                                        Custom Name
                                                    </Label>
                                                    <Input
                                                        :id="`name-${interest.id}`"
                                                        v-model="
                                                            interest.custom_name
                                                        "
                                                        type="text"
                                                        placeholder="e.g., Morning Workout"
                                                        class="mt-1"
                                                    />
                                                    <InputError
                                                        :message="
                                                            errors[
                                                                `interests.${index}.custom_name`
                                                            ]
                                                        "
                                                    />
                                                </div>
                                                <div>
                                                    <Label
                                                        :for="`notes-${interest.id}`"
                                                    >
                                                        Notes (optional)
                                                    </Label>
                                                    <Textarea
                                                        :id="`notes-${interest.id}`"
                                                        v-model="interest.notes"
                                                        placeholder="Add any personal notes..."
                                                        :rows="2"
                                                        class="mt-1"
                                                    />
                                                    <InputError
                                                        :message="
                                                            errors[
                                                                `interests.${index}.notes`
                                                            ]
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 w-8 p-0 text-destructive hover:text-destructive"
                                            @click="removeInterest(index)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </CardHeader>
                        </Card>
                    </div>

                    <div
                        v-else
                        class="rounded-lg border-2 border-dashed py-12 text-center"
                    >
                        <p class="mb-4 text-muted-foreground">
                            No interests added yet. Add your first interest to
                            start tracking activities!
                        </p>
                    </div>

                    <InputError :message="errors.interests" />

                    <!-- Add Activity Button -->
                    <Button
                        variant="outline"
                        class="w-full"
                        @click="showAddDialog = true"
                        :disabled="availableTypes.length === 0"
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

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3 pt-4">
                        <Button
                            @click="handleSubmit"
                            :disabled="localInterests.length === 0"
                        >
                            <Save class="mr-2 h-4 w-4" />
                            Save Changes
                        </Button>
                        <Button
                            variant="outline"
                            @click="
                                localInterests = JSON.parse(
                                    JSON.stringify(props.interests),
                                )
                            "
                        >
                            Reset
                        </Button>
                        <span
                            v-if="recentlySuccessful"
                            class="text-sm text-green-600"
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
                                @click="addInterest(type)"
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
