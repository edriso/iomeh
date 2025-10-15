<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed } from 'vue';

interface Props {
  value?: number;
  modelValue?: number;
  max?: number;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  value: 0,
  modelValue: 0,
  max: 100,
});

const progressClass = computed(() => 
  cn(
    'relative h-2 w-full overflow-hidden rounded-full bg-secondary',
    props.class
  )
);

const percentage = computed(() => {
  const currentValue = props.modelValue ?? props.value ?? 0;
  return Math.min(Math.max((currentValue / props.max) * 100, 0), 100);
});
</script>

<template>
  <div :class="progressClass">
    <div 
      class="h-full w-full flex-1 bg-primary transition-all"
      :style="{ transform: `translateX(-${100 - percentage}%)` }"
    />
  </div>
</template>
