<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'
import { useTranslations } from '@/composables/useTranslations'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()

const { isRTL } = useTranslations()
</script>

<template>
  <div
    data-slot="dialog-header"
    :class="cn('flex flex-col gap-2 text-center w-full min-w-0', isRTL ? 'sm:text-right' : 'sm:text-left', props.class)"
  >
    <slot />
  </div>
</template>
