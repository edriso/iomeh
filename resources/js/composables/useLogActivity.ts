import { usePage } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

// Global state for log activity modal
const showLogActivityModal = ref(false);

export function useLogActivity() {
    const page = usePage();

    const openLogActivityModal = async () => {
        // Check if user has habits before opening modal
        const habits = (page.props.auth?.habits || []) as any[];
        if (habits.length === 0) {
            console.warn('Cannot open log activity modal: no habits available');
            return;
        }

        await nextTick();
        showLogActivityModal.value = true;
    };

    const closeLogActivityModal = () => {
        showLogActivityModal.value = false;
    };

    return {
        showLogActivityModal,
        openLogActivityModal,
        closeLogActivityModal,
    };
}
