<template>
  <span :class="valueClass">
    {{ displayValue }}
  </span>
</template>

<script setup lang="ts">
import { computed, inject } from 'vue'
import { cva } from 'class-variance-authority'

const props = defineProps<{
  placeholder?: string
  class?: string
}>()

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

const displayValue = computed(() => {
  if (value.value === undefined || value.value === null || value.value === '') {
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
      variant: value.value === undefined || value.value === null || value.value === '' ? 'placeholder' : 'default',
    },
  })({
    class: props.class,
  })
})
</script>
