<template>
  <span :class="valueClass">
    <slot v-if="hasValue">{{ displayValue }}</slot>
    <template v-if="!hasValue || !$slots.default">{{ displayValue }}</template>
  </span>
</template>

<script setup lang="ts">
import { computed, inject, useSlots } from 'vue'
import { cva } from 'class-variance-authority'

const props = defineProps<{
  placeholder?: string
  class?: string
}>()

const slots = useSlots()

const selectContext = inject<{
  value: any
  setValue: (value: any) => void
  open: boolean
  setOpen: (open: boolean) => void
}>('select')

if (!selectContext) {
  throw new Error('SelectValue must be used within a Select component')
}

const { value } = selectContext

const hasValue = computed(() => {
  return value.value !== undefined && value.value !== null && value.value !== ''
})

const displayValue = computed(() => {
  if (!hasValue.value) {
    return props.placeholder || 'Select an option'
  }
  return value.value
})

const valueClass = computed(() => {
  return cva('', {
    variants: {
      variant: {
        default: '',
        placeholder: 'text-muted-foreground',
      },
    },
    defaultVariants: {
      variant: hasValue.value ? 'default' : 'placeholder',
    },
  })({
    class: props.class,
  })
})
</script>
