<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed, provide, ref } from 'vue';

interface Props {
  defaultValue?: string;
  value?: string;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  defaultValue: '',
});

const emit = defineEmits<{
  'update:value': [value: string];
}>();

const activeTab = ref(props.value || props.defaultValue);

const handleTabChange = (value: string) => {
  activeTab.value = value;
  emit('update:value', value);
};

provide('tabs', {
  activeTab: computed(() => activeTab.value),
  setActiveTab: handleTabChange,
});

const tabsClass = computed(() => 
  cn('w-full', props.class)
);
</script>

<template>
  <div :class="tabsClass">
    <slot />
  </div>
</template>
