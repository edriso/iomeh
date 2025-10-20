<template>
  <div
    v-if="open"
    ref="contentRef"
    :class="contentClass"
    :style="contentStyle"
    role="listbox"
    @keydown="handleKeydown"
    @click.stop
  >
    <slot />
  </div>
</template>

<script setup lang="ts">
import { computed, inject, onMounted, onUnmounted, ref, watch } from 'vue'
import { cva } from 'class-variance-authority'

const props = defineProps<{
  class?: string
  position?: 'item-aligned' | 'popper'
}>()

const selectContext = inject<{
  value: any
  setValue: (value: any) => void
  open: boolean
  setOpen: (open: boolean) => void
}>('select')

if (!selectContext) {
  throw new Error('SelectContent must be used within a Select component')
}

const { open, setOpen } = selectContext

const contentRef = ref<HTMLDivElement>()
const contentStyle = ref<Record<string, string>>({})

// Calculate and set dropdown width based on trigger
const updatePosition = () => {
  if (contentRef.value) {
    // Find the trigger button - it's a sibling in the parent container
    const parent = contentRef.value.parentElement
    const trigger = parent?.querySelector('button') || parent?.querySelector('[role="combobox"]')
    
    if (trigger) {
      const triggerRect = trigger.getBoundingClientRect()
      const viewportHeight = window.innerHeight
      const dropdownHeight = 384 // max-h-96 = 24rem = 384px
      const spaceBelow = viewportHeight - triggerRect.bottom
      const spaceAbove = triggerRect.top
      
      // If there's not enough space below, position above the trigger
      let topPosition = 'auto'
      let bottomPosition = 'auto'
      
      if (spaceBelow < dropdownHeight && spaceAbove > spaceBelow) {
        // Position above the trigger
        bottomPosition = `${viewportHeight - triggerRect.top + 4}px`
        topPosition = 'auto'
      } else {
        // Position below the trigger (default)
        topPosition = 'auto'
        bottomPosition = 'auto'
      }
      
      contentStyle.value = {
        width: `${triggerRect.width}px`,
        top: topPosition,
        bottom: bottomPosition,
      }
    } else {
      // Fallback: use parent width
      contentStyle.value = {
        width: '100%',
      }
    }
  }
}

watch(() => open, (isOpen) => {
  if (isOpen) {
    // Use nextTick to ensure DOM is updated
    setTimeout(updatePosition, 10)
    
    // Add resize listener to recalculate position
    const handleResize = () => updatePosition()
    window.addEventListener('resize', handleResize)
    
    // Clean up listener when dropdown closes
    return () => window.removeEventListener('resize', handleResize)
  }
})

const contentClass = computed(() => {
  return cva(
    'absolute left-0 right-0 z-[9999] mt-1 max-h-96 w-full overflow-y-auto overflow-x-hidden rounded-md border bg-popover text-popover-foreground shadow-lg data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2',
    {
      variants: {
        position: {
          'item-aligned': '',
          popper: 'data-[side=bottom]:translate-y-1 data-[side=left]:-translate-x-1 data-[side=right]:translate-x-1 data-[side=top]:-translate-y-1',
        },
      },
      defaultVariants: {
        position: 'item-aligned',
      },
    }
  )({
    class: props.class,
    position: props.position,
  })
})

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Escape') {
    setOpen(false)
  }
}

// Close on outside click
watch(() => open, (isOpen) => {
  if (isOpen) {
    const handleClickOutside = (event: MouseEvent) => {
      if (contentRef.value && !contentRef.value.contains(event.target as Node)) {
        setOpen(false)
      }
    }
    document.addEventListener('click', handleClickOutside)
    return () => document.removeEventListener('click', handleClickOutside)
  }
})
</script>
