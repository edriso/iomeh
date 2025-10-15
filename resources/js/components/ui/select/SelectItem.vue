<template>
  <div
    :class="itemClass"
    role="option"
    :aria-selected="isSelected"
    @click="handleClick"
    @keydown="handleKeydown"
  >
    <slot />
  </div>
</template>

<script setup lang="ts">
import { computed, inject } from 'vue'
import { cva } from 'class-variance-authority'

const props = defineProps<{
  value: any
  disabled?: boolean
  class?: string
}>()

const selectContext = inject<{
  value: any
  setValue: (value: any) => void
  open: boolean
  setOpen: (open: boolean) => void
}>('select')

if (!selectContext) {
  throw new Error('SelectItem must be used within a Select component')
}

const { value, setValue } = selectContext

const isSelected = computed(() => value.value === props.value)

const itemClass = computed(() => {
  return cva(
    'relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-2 pr-8 text-sm outline-none focus:bg-primary focus:text-primary-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
    {
      variants: {
        selected: {
          true: 'bg-primary text-primary-foreground',
          false: '',
        },
      },
      defaultVariants: {
        selected: isSelected.value,
      },
    }
  )({
    class: props.class,
  })
})

const handleClick = () => {
  if (!props.disabled) {
    setValue(props.value)
  }
}

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Enter' || event.key === ' ') {
    event.preventDefault()
    handleClick()
  }
}
</script>
