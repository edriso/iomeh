<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useTranslations } from '@/composables/useTranslations';
import { GripVertical, Trash2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

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

// Translations
const { t } = useTranslations();

// Emoji picker state
const showEmojiPicker = ref(false);
const customEmojiInput = ref('');

// Emoji data
const popularEmojis = [
    '😀',
    '😊',
    '😎',
    '🤔',
    '😴',
    '🤗',
    '😍',
    '🥰',
    '😘',
    '😉',
    '😋',
    '🤤',
    '😏',
    '😒',
    '😔',
    '😢',
];

const fitnessEmojis = [
    '🏃‍♂️',
    '🏃‍♀️',
    '🚴‍♂️',
    '🚴‍♀️',
    '🏊‍♂️',
    '🏊‍♀️',
    '🏋️‍♂️',
    '🏋️‍♀️',
    '🤸‍♂️',
    '🤸‍♀️',
    '🤾‍♂️',
    '🤾‍♀️',
    '🤽‍♂️',
    '🤽‍♀️',
    '🤼‍♂️',
    '🤼‍♀️',
    '🏌️‍♂️',
    '🏌️‍♀️',
    '🏇',
    '🤺',
    '🏄‍♂️',
    '🏄‍♀️',
    '🏊‍♂️',
    '🏊‍♀️',
    '⛹️‍♂️',
    '⛹️‍♀️',
    '🏋️‍♂️',
    '🏋️‍♀️',
    '🚴‍♂️',
    '🚴‍♀️',
    '🏃‍♂️',
    '🏃‍♀️',
];

const foodEmojis = [
    '🍎',
    '🍊',
    '🍋',
    '🍌',
    '🍉',
    '🍇',
    '🍓',
    '🍈',
    '🍒',
    '🍑',
    '🥭',
    '🍍',
    '🥥',
    '🥝',
    '🍅',
    '🥕',
    '🌽',
    '🌶️',
    '🥒',
    '🥬',
    '🥦',
    '🧄',
    '🧅',
    '🍄',
    '🥜',
    '🌰',
    '🍞',
    '🥐',
    '🥖',
    '🍯',
    '🥛',
    '🧀',
];

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

const customIcon = computed({
    get: () => props.habit.custom_icon,
    set: (value: string | null) => {
        emit('update:habit', { ...props.habit, custom_icon: value });
    },
});

// Get the effective icon (custom or activity type icon)
const effectiveIcon = computed(() => {
    return props.habit.custom_icon || props.habit.activity_type?.icon || '🏃‍♂️';
});

// Helper function to get error message
const getErrorMessage = (field: string): string | undefined => {
    return props.errors[`habits.${props.index}.${field}`];
};

// Emoji picker methods
const toggleEmojiPicker = () => {
    showEmojiPicker.value = !showEmojiPicker.value;
};

const closeEmojiPicker = () => {
    showEmojiPicker.value = false;
    customEmojiInput.value = '';
};

const selectEmoji = (emoji: string) => {
    customIcon.value = emoji;
    closeEmojiPicker();
};

const selectCustomEmoji = () => {
    if (customEmojiInput.value) {
        selectEmoji(customEmojiInput.value);
    }
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
                            <button
                                type="button"
                                @click="toggleEmojiPicker"
                                class="cursor-pointer rounded p-1 text-2xl transition-colors hover:bg-muted"
                                :title="t('habits.change_icon')"
                            >
                                {{ effectiveIcon }}
                            </button>
                            <Badge
                                v-if="habit.activity_type"
                                variant="outline"
                                :class="
                                    getCategoryColor(
                                        habit.activity_type.category,
                                    )
                                "
                            >
                                {{
                                    t(`habits.${habit.activity_type.category}`)
                                }}
                            </Badge>
                            <span
                                v-if="habit.activity_type"
                                class="text-xs text-muted-foreground"
                            >
                                {{ habit.activity_type.base_points }}
                                {{ t('habits.points') }}
                            </span>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <Label :for="`name-${habit.id}`">
                                    {{ t('habits.custom_name') }}
                                </Label>
                                <Input
                                    :id="`name-${habit.id}`"
                                    v-model="customName"
                                    type="text"
                                    :placeholder="t('habits.example_name')"
                                    class="mt-1"
                                />
                                <InputError
                                    :message="getErrorMessage('custom_name')"
                                />
                            </div>
                            <div>
                                <Label :for="`notes-${habit.id}`">
                                    {{ t('habits.notes_optional') }}
                                </Label>
                                <Textarea
                                    :id="`notes-${habit.id}`"
                                    v-model="notes"
                                    :placeholder="t('habits.notes_placeholder')"
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

                <!-- Emoji Picker Popup -->
                <div
                    v-if="showEmojiPicker"
                    class="absolute top-0 right-0 left-0 z-50 rounded-lg border border-border bg-background p-4 shadow-lg"
                    @click.stop
                >
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-medium">
                            {{ t('habits.choose_icon') }}
                        </h3>
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            @click="closeEmojiPicker"
                            class="h-6 w-6 p-0"
                        >
                            <X class="h-4 w-4" />
                        </Button>
                    </div>

                    <div class="space-y-3">
                        <!-- Popular emojis -->
                        <div>
                            <label
                                class="mb-2 block text-xs font-medium text-muted-foreground"
                            >
                                {{ t('habits.popular') }}
                            </label>
                            <div class="grid grid-cols-8 gap-1">
                                <button
                                    v-for="emoji in popularEmojis"
                                    :key="emoji"
                                    @click="selectEmoji(emoji)"
                                    class="flex h-8 w-8 items-center justify-center rounded transition-colors hover:bg-muted"
                                    :class="{
                                        'bg-muted': customIcon === emoji,
                                    }"
                                >
                                    <span class="text-lg">{{ emoji }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Fitness emojis -->
                        <div>
                            <label
                                class="mb-2 block text-xs font-medium text-muted-foreground"
                            >
                                {{ t('habits.fitness') }}
                            </label>
                            <div class="grid grid-cols-8 gap-1">
                                <button
                                    v-for="emoji in fitnessEmojis"
                                    :key="emoji"
                                    @click="selectEmoji(emoji)"
                                    class="flex h-8 w-8 items-center justify-center rounded transition-colors hover:bg-muted"
                                    :class="{
                                        'bg-muted': customIcon === emoji,
                                    }"
                                >
                                    <span class="text-lg">{{ emoji }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Food emojis -->
                        <div>
                            <label
                                class="mb-2 block text-xs font-medium text-muted-foreground"
                            >
                                {{ t('habits.food_health') }}
                            </label>
                            <div class="grid grid-cols-8 gap-1">
                                <button
                                    v-for="emoji in foodEmojis"
                                    :key="emoji"
                                    @click="selectEmoji(emoji)"
                                    class="flex h-8 w-8 items-center justify-center rounded transition-colors hover:bg-muted"
                                    :class="{
                                        'bg-muted': customIcon === emoji,
                                    }"
                                >
                                    <span class="text-lg">{{ emoji }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Custom input -->
                        <div>
                            <label
                                class="mb-2 block text-xs font-medium text-muted-foreground"
                            >
                                {{ t('habits.custom') }}
                            </label>
                            <div class="flex gap-2">
                                <Input
                                    v-model="customEmojiInput"
                                    :placeholder="t('habits.enter_emoji')"
                                    class="flex-1"
                                    @keydown.enter="selectCustomEmoji"
                                />
                                <Button
                                    type="button"
                                    size="sm"
                                    @click="selectCustomEmoji"
                                    :disabled="!customEmojiInput"
                                >
                                    {{ t('habits.add') }}
                                </Button>
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
