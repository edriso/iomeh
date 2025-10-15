<template>
  <button
    ref="triggerRef"
    type="button"
    :class="triggerClass"
    :disabled="isDisabled"
    :aria-expanded="open"
    :aria-haspopup="true"
    @click="handleClick"
    @keydown="handleKeydown"
  >
    <slot />
    <svg
      class="ml-2 h-4 w-4 shrink-0 opacity-50"
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <path d="m6 9 6 6 6-6" />
    </svg>
  </button>
</template>

<script setup lang="ts">
import { computed, inject, onMounted, ref, watch, type Ref } from 'vue'
import { cva } from 'class-variance-authority'

const props = defineProps<{
  class?: string
  disabled?: boolean
}>()

const selectContext = inject<{
  value: any
  setValue: (value: any) => void
  open: Ref<boolean>
  setOpen: (open: boolean) => void
  disabled: Ref<boolean>
}>('select')

if (!selectContext) {
  throw new Error('SelectTrigger must be used within a Select component')
}

const { open, setOpen, disabled } = selectContext

const triggerRef = ref<HTMLButtonElement>()

const isDisabled = computed(() => props.disabled || disabled.value)

const triggerClass = computed(() => {
  return cva(
    'flex h-9 w-full items-center justify-between whitespace-nowrap rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50 [&>span]:line-clamp-1',
    {
      variants: {
        variant: {
          default: '',
          destructive: 'border-destructive text-destructive focus:ring-destructive',
        },
        disabled: {
          true: 'cursor-not-allowed opacity-50 bg-muted',
          false: '',
        },
      },
      defaultVariants: {
        variant: 'default',
        disabled: isDisabled.value,
      },
    }
  )({
    class: props.class,
  })
})

const handleClick = (event: MouseEvent) => {
  event.preventDefault()
  event.stopPropagation()
  
  if (!isDisabled.value) {
    setOpen(!open.value)
  }
}

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Enter' || event.key === ' ') {
    event.preventDefault()
    if (!isDisabled.value) {
      setOpen(!open.value)
    }
  } else if (event.key === 'Escape') {
    setOpen(false)
  }
}

// Close on outside click
watch(() => open, (isOpen) => {
  if (isOpen) {
    const handleClickOutside = (event: MouseEvent) => {
      if (triggerRef.value && !triggerRef.value.contains(event.target as Node)) {
        setOpen(false)
      }
    }
    document.addEventListener('click', handleClickOutside)
    return () => document.removeEventListener('click', handleClickOutside)
  }
})
</script>
