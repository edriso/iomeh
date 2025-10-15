<script setup lang="ts">
import { computed, type HTMLAttributes } from 'vue';
import {
    ScrollAreaCorner,
    ScrollAreaRoot,
    type ScrollAreaRootProps,
    ScrollAreaScrollbar,
    ScrollAreaThumb,
    ScrollAreaViewport,
} from 'reka-ui';
import { cn } from '@/lib/utils';

const props = defineProps<
    ScrollAreaRootProps & { class?: HTMLAttributes['class'] }
>();

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <ScrollAreaRoot
        v-bind="delegatedProps"
        :class="cn('relative overflow-hidden', props.class)"
    >
        <ScrollAreaViewport class="h-full w-full rounded-[inherit]">
            <slot />
        </ScrollAreaViewport>
        <ScrollAreaScrollbar
            class="flex touch-none select-none transition-colors ease-out data-[orientation=horizontal]:h-2.5 data-[orientation=vertical]:w-2.5 data-[orientation=horizontal]:flex-col"
            orientation="vertical"
        >
            <ScrollAreaThumb
                class="relative flex-1 rounded-full bg-border"
            />
        </ScrollAreaScrollbar>
        <ScrollAreaScrollbar
            class="flex touch-none select-none transition-colors ease-out data-[orientation=horizontal]:h-2.5 data-[orientation=vertical]:w-2.5 data-[orientation=horizontal]:flex-col"
            orientation="horizontal"
        >
            <ScrollAreaThumb
                class="relative flex-1 rounded-full bg-border"
            />
        </ScrollAreaScrollbar>
        <ScrollAreaCorner />
    </ScrollAreaRoot>
</template>

