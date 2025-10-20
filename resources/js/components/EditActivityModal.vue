<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
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
import { Textarea } from '@/components/ui/textarea';
import { useTranslations } from '@/composables/useTranslations';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

interface RecentActivity {
    id: number;
    activity_type_name: string;
    activity_type_icon: string;
    custom_name: string;
    date: string;
    points_earned: number;
    notes?: string;
    memory_url?: string;
    is_inactive?: boolean;
}

interface Props {
    open: boolean;
    activity: RecentActivity | null;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'activity-updated', activity: RecentActivity): void;
}>();

const { t } = useTranslations();

const form = useForm({
    notes: '',
    memory_url: '',
});

// Watch for activity changes to populate form
watch(
    () => props.activity,
    (newActivity) => {
        if (newActivity) {
            form.notes = newActivity.notes || '';
            form.memory_url = newActivity.memory_url || '';
        }
    },
    { immediate: true },
);

const handleSubmit = () => {
    if (!props.activity) return;

    form.patch(`/activities/${props.activity.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            if (props.activity) {
                // Create updated activity object with new data
                const updatedActivity: RecentActivity = {
                    ...props.activity,
                    notes: form.notes,
                    memory_url: form.memory_url,
                };

                emit('activity-updated', updatedActivity);
            }
            emit('update:open', false);
            form.reset();
        },
        onError: (errors) => {
            console.error('Activity update failed:', errors);
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
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>
                    {{ t('modal.edit_activity.title') }}
                </DialogTitle>
                <DialogDescription>
                    {{ t('modal.edit_activity.description') }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="handleSubmit" class="space-y-4">
                <!-- Activity Info Display -->
                <div v-if="activity" class="rounded-lg border bg-muted/50 p-3">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">{{
                            activity.activity_type_icon
                        }}</span>
                        <div>
                            <p class="font-medium">
                                {{ activity.custom_name }}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                {{ activity.date }}
                            </p>
                        </div>
                        <Badge variant="secondary" class="ml-auto">
                            +{{ activity.points_earned }} {{ t('profile.pts') }}
                        </Badge>
                    </div>
                </div>

                <!-- Notes -->
                <div class="space-y-2">
                    <Label for="notes">{{
                        t('modal.edit_activity.notes')
                    }}</Label>
                    <Textarea
                        id="notes"
                        v-model="form.notes"
                        :placeholder="
                            t('modal.edit_activity.notes_placeholder')
                        "
                        :rows="3"
                        :maxlength="2000"
                    />
                    <InputError :message="form.errors.notes" />
                </div>

                <!-- Memory URL -->
                <div class="space-y-2">
                    <Label for="memory_url">{{
                        t('modal.edit_activity.memory_url')
                    }}</Label>
                    <Input
                        id="memory_url"
                        v-model="form.memory_url"
                        type="url"
                        :placeholder="
                            t('modal.edit_activity.memory_url_placeholder')
                        "
                    />
                    <InputError :message="form.errors.memory_url" />
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-4">
                    <Button
                        type="button"
                        variant="outline"
                        @click="handleClose"
                        :disabled="form.processing"
                    >
                        {{ t('modal.edit_activity.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{
                            form.processing
                                ? t('modal.edit_activity.saving')
                                : t('modal.edit_activity.save')
                        }}
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>
