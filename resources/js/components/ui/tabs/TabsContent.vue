<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed, inject } from 'vue';

interface Props {
  value: string;
  class?: string;
}

const props = defineProps<Props>();

const tabs = inject('tabs');

const isActive = computed(() => tabs?.activeTab.value === props.value);

const contentClass = computed(() => 
  cn(
    'mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2',
    props.class
  )
);
</script>

<template>
  <div v-if="isActive" :class="contentClass">
    <slot />
  </div>
</template>
