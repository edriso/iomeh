<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed, inject } from 'vue';

interface Props {
  class?: string;
}

const props = defineProps<Props>();

const tabs = inject('tabs');

const tabsListClass = computed(() => 
  cn(
    'inline-flex h-9 items-center justify-center rounded-lg bg-muted p-1 text-muted-foreground',
    props.class
  )
);
</script>

<template>
  <div :class="tabsListClass">
    <slot />
  </div>
</template>
