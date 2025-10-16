<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useLogActivity } from '@/composables/useLogActivity';
import { usePage } from '@inertiajs/vue3';
import { ChevronUp, Plus } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const { openLogActivityModal } = useLogActivity();

const isVisible = ref(false);
const isScrolling = ref(false);

const checkScroll = () => {
    isVisible.value = window.scrollY > 300;
};

const scrollToTop = () => {
    isScrolling.value = true;
    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    });

    // Reset scrolling state after animation completes
    setTimeout(() => {
        isScrolling.value = false;
    }, 500);
};

const handleLogActivity = () => {
    openLogActivityModal();
};

onMounted(() => {
    window.addEventListener('scroll', checkScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', checkScroll);
});
</script>

<template>
    <div
        v-if="isAuthenticated"
        class="fixed right-6 bottom-6 z-50 flex flex-col gap-3"
    >
        <!-- Back to Top Button (appears above) -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform scale-90 opacity-0 translate-y-2"
            enter-to-class="transform scale-100 opacity-100 translate-y-0"
            leave-active-class="transition duration-300 ease-in"
            leave-from-class="transform scale-100 opacity-100 translate-y-0"
            leave-to-class="transform scale-90 opacity-0 translate-y-2"
        >
            <Button
                v-if="isVisible"
                @click="scrollToTop"
                size="icon"
                :class="[
                    'h-12 w-12 rounded-full shadow-lg transition-all duration-300 hover:shadow-xl',
                    'bg-primary hover:bg-primary hover:text-primary-foreground',
                    isScrolling ? 'scale-95' : 'scale-100',
                ]"
                aria-label="Back to top"
                title="Back to top"
            >
                <ChevronUp
                    :class="[
                        'h-5 w-5 transition-transform duration-200',
                        isScrolling ? 'scale-110' : 'scale-100',
                    ]"
                />
            </Button>
        </Transition>

        <!-- Log Activity Button (always visible) -->
        <Button
            @click="handleLogActivity"
            size="icon"
            class="h-14 w-14 rounded-full bg-primary shadow-lg transition-all duration-300 hover:scale-105 hover:bg-primary hover:text-primary-foreground hover:shadow-xl"
            aria-label="Log activity"
            title="Log activity"
        >
            <Plus class="h-6 w-6" />
        </Button>
    </div>

    <!-- Non-authenticated: Just show back to top when scrolled -->
    <Transition
        v-else
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform scale-90 opacity-0 translate-y-2"
        enter-to-class="transform scale-100 opacity-100 translate-y-0"
        leave-active-class="transition duration-300 ease-in"
        leave-from-class="transform scale-100 opacity-100 translate-y-0"
        leave-to-class="transform scale-90 opacity-0 translate-y-2"
    >
        <Button
            v-if="isVisible"
            @click="scrollToTop"
            size="icon"
            :class="[
                'fixed right-6 bottom-6 z-50 h-12 w-12 rounded-full shadow-lg transition-all duration-300 hover:shadow-xl',
                'bg-primary hover:bg-primary hover:text-primary-foreground',
                isScrolling ? 'scale-95' : 'scale-100',
            ]"
            aria-label="Back to top"
            title="Back to top"
        >
            <ChevronUp
                :class="[
                    'h-5 w-5 transition-transform duration-200',
                    isScrolling ? 'scale-110' : 'scale-100',
                ]"
            />
        </Button>
    </Transition>
</template>
