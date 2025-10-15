<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
  checked?: boolean;
  disabled?: boolean;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  checked: false,
  disabled: false,
});

const emit = defineEmits<{
  'update:checked': [checked: boolean];
  change: [checked: boolean];
}>();

const switchClass = computed(() => 
  cn(
    'peer inline-flex h-5 w-9 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50',
    props.checked ? 'bg-primary' : 'bg-input dark:bg-muted-foreground',
    props.class
  )
);

const handleChange = () => {
  if (!props.disabled) {
    emit('update:checked', !props.checked);
    emit('change', !props.checked);
  }
};
</script>

<template>
  <button
    type="button"
    role="switch"
    :aria-checked="checked"
    :disabled="disabled"
    :class="switchClass"
    @click="handleChange"
  >
    <span 
      class="pointer-events-none block h-4 w-4 rounded-full bg-background shadow-lg ring-0 transition-transform"
      :class="checked ? 'translate-x-4' : 'translate-x-0'"
    />
  </button>
</template>
