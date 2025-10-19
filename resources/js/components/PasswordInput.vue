<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    id: string;
    name: string;
    placeholder?: string;
    required?: boolean;
    autocomplete?: string;
    tabindex?: number;
    modelValue?: string;
}

withDefaults(defineProps<Props>(), {
    required: false,
    tabindex: 0,
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <div class="relative">
        <Input
            :id="id"
            :name="name"
            :type="showPassword ? 'text' : 'password'"
            :placeholder="placeholder"
            :required="required"
            :autocomplete="autocomplete"
            :tabindex="tabindex"
            :model-value="modelValue"
            class="pr-10"
            @update:model-value="emit('update:modelValue', $event)"
        />
        <button
            type="button"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
            @click="togglePasswordVisibility"
        >
            <Eye v-if="!showPassword" class="h-4 w-4" />
            <EyeOff v-else class="h-4 w-4" />
        </button>
    </div>
</template>
