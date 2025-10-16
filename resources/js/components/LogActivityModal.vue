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
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Interest {
    id: number;
    name: string;
    icon?: string;
    activity_type_id: number;
    base_points: number;
}

interface Props {
    open: boolean;
    interests: Interest[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const form = useForm({
    interest_id: '',
    date: new Date().toISOString().split('T')[0],
    notes: '',
    proof_url: '',
});

const selectedInterest = computed(() => {
    return props.interests.find((i) => i.id === Number(form.interest_id));
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
                <!-- Interest Selection -->
                <div class="space-y-2">
                    <Label for="interest_id">Activity Type</Label>
                    <Select v-model="form.interest_id" required>
                        <SelectTrigger id="interest_id">
                            <SelectValue placeholder="Select an activity">
                                <span
                                    v-if="selectedInterest"
                                    class="flex items-center gap-2"
                                >
                                    <span v-if="selectedInterest.icon">{{
                                        selectedInterest.icon
                                    }}</span>
                                    {{ selectedInterest.name }}
                                </span>
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="interest in interests"
                                :key="interest.id"
                                :value="String(interest.id)"
                            >
                                <span class="flex items-center gap-2">
                                    <span v-if="interest.icon">{{
                                        interest.icon
                                    }}</span>
                                    {{ interest.name }}
                                </span>
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.interest_id" />
                </div>

                <!-- Date -->
                <div class="space-y-2">
                    <Label for="date">Date</Label>
                    <Input
                        id="date"
                        v-model="form.date"
                        type="date"
                        :max="new Date().toISOString().split('T')[0]"
                        required
                    />
                    <InputError :message="form.errors.date" />
                </div>

                <!-- Points Display (Read-only) -->
                <div v-if="selectedInterest" class="space-y-2">
                    <Label>Points You'll Earn</Label>
                    <div
                        class="flex items-center gap-2 rounded-md border border-border bg-muted/50 px-3 py-2"
                    >
                        <span class="text-2xl font-bold text-primary">{{
                            selectedInterest.base_points
                        }}</span>
                        <span class="text-sm text-muted-foreground"
                            >points</span
                        >
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Points are automatically assigned based on activity type
                    </p>
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

                <!-- Proof URL -->
                <div class="space-y-2">
                    <Label for="proof_url">Proof URL (Optional)</Label>
                    <Input
                        id="proof_url"
                        v-model="form.proof_url"
                        type="url"
                        placeholder="https://..."
                    />
                    <InputError :message="form.errors.proof_url" />
                    <p class="text-xs text-muted-foreground">
                        Link to proof (workout screenshot, meal photo, etc.)
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
                        :disabled="form.processing || !form.interest_id"
                    >
                        {{ form.processing ? 'Logging...' : 'Log Activity' }}
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>
