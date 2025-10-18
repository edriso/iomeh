<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { GripVertical, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

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
    habit: Habit;
    index: number;
    errors: Record<string, string>;
    onRemove: (index: number) => void;
    getCategoryColor: (category: string) => string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:habit': [habit: Habit];
}>();

// Computed properties for better type safety
const customName = computed({
    get: () => props.habit.custom_name,
    set: (value: string) => {
        emit('update:habit', { ...props.habit, custom_name: value });
    },
});

const notes = computed({
    get: () => props.habit.notes ?? '',
    set: (value: string) => {
        emit('update:habit', { ...props.habit, notes: value || null });
    },
});

// Helper function to get error message
const getErrorMessage = (field: string): string | undefined => {
    return props.errors[`habits.${props.index}.${field}`];
};
</script>

<template>
    <Card class="relative">
        <CardHeader class="pb-3">
            <div class="flex items-start justify-between gap-4">
                <div class="flex flex-1 items-center gap-3">
                    <div class="flex items-center gap-1">
                        <Button
                            variant="ghost"
                            size="sm"
                            class="h-8 w-8 cursor-grab p-0 active:cursor-grabbing"
                        >
                            <GripVertical class="h-4 w-4" />
                        </Button>
                    </div>
                    <div class="flex-1">
                        <div class="mb-2 flex items-center gap-2">
                            <span
                                v-if="habit.activity_type.icon"
                                class="text-2xl"
                            >
                                {{ habit.activity_type.icon }}
                            </span>
                            <Badge
                                variant="outline"
                                :class="
                                    getCategoryColor(
                                        habit.activity_type.category,
                                    )
                                "
                            >
                                {{ habit.activity_type.category }}
                            </Badge>
                            <span class="text-xs text-muted-foreground">
                                {{ habit.activity_type.base_points }} pts
                            </span>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <Label :for="`name-${habit.id}`">
                                    Custom Name
                                </Label>
                                <Input
                                    :id="`name-${habit.id}`"
                                    v-model="customName"
                                    type="text"
                                    placeholder="e.g., Morning Workout"
                                    class="mt-1"
                                />
                                <InputError
                                    :message="getErrorMessage('custom_name')"
                                />
                            </div>
                            <div>
                                <Label :for="`notes-${habit.id}`">
                                    Notes (optional)
                                </Label>
                                <Textarea
                                    :id="`notes-${habit.id}`"
                                    v-model="notes"
                                    placeholder="Add any personal notes..."
                                    :rows="2"
                                    class="mt-1"
                                />
                                <InputError
                                    :message="getErrorMessage('notes')"
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
                        @click="onRemove(index)"
                    >
                        <Trash2 class="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </CardHeader>
    </Card>
</template>
