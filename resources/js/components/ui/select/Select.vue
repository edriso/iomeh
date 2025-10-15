<template>
  <div class="relative">
    <slot />
  </div>
</template>

<script setup lang="ts">
import { provide, ref, watch } from 'vue'

interface SelectContext {
  value: any
  setValue: (value: any) => void
  open: any
  setOpen: (open: boolean) => void
  disabled: any
}

const props = defineProps<{
  modelValue?: any
  disabled?: boolean
  name?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: any]
}>()

const value = ref(props.modelValue)
const open = ref(false)
const disabled = ref(props.disabled || false)

const setValue = (newValue: any) => {
  if (!disabled.value && !props.disabled) {
    value.value = newValue
    emit('update:modelValue', newValue)
    open.value = false
  }
}

const setOpen = (newOpen: boolean) => {
  if (!disabled.value && !props.disabled) {
    open.value = newOpen
  }
}

// Watch for prop changes
watch(() => props.modelValue, (newValue) => {
  value.value = newValue
})

watch(() => props.disabled, (newDisabled) => {
  disabled.value = newDisabled || false
})

const context: SelectContext = {
  value,
  setValue,
  open,
  setOpen,
  disabled
}

provide('select', context)
</script>
