<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
  class?: string;
  placeholder?: string;
  rows?: number;
  disabled?: boolean;
  readonly?: boolean;
  modelValue?: string;
  defaultValue?: string;
}

const props = withDefaults(defineProps<Props>(), {
  rows: 3,
  disabled: false,
  readonly: false,
  modelValue: '',
  defaultValue: '',
});

const emit = defineEmits<{
  'update:modelValue': [value: string];
  input: [event: Event];
  change: [event: Event];
  focus: [event: FocusEvent];
  blur: [event: FocusEvent];
}>();

const textareaClass = computed(() => 
  cn(
    'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
    props.class
  )
);

const handleInput = (event: Event) => {
  const value = (event.target as HTMLTextAreaElement).value;
  emit('update:modelValue', value);
  emit('input', event);
};
</script>

<template>
  <textarea
    :class="textareaClass"
    :placeholder="placeholder"
    :rows="rows"
    :disabled="disabled"
    :readonly="readonly"
    :value="modelValue"
    @input="handleInput"
    @change="emit('change', $event)"
    @focus="emit('focus', $event)"
    @blur="emit('blur', $event)"
  />
</template>
