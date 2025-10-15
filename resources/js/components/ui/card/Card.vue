<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <div
    data-slot="card"
    :class="
      cn(
        'bg-background text-foreground flex flex-col justify-between gap-6 rounded-xl border py-6 shadow-sm w-full min-w-0',
        props.class,
      )
    "
  >
    <slot />
  </div>
</template>
