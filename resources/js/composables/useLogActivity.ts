import { usePage } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

// Global state for log activity modal
const showLogActivityModal = ref(false);
const preselectedHabit = ref<any>(null);

export function useLogActivity() {
    const page = usePage();

    const openLogActivityModal = async (habit?: any) => {
        // Check if user has habits before opening modal
        const habits = (page.props.auth?.habits || []) as any[];
        if (habits.length === 0) {
            console.warn('Cannot open log activity modal: no habits available');
            return;
        }

        // Set preselected habit if provided
        preselectedHabit.value = habit || null;

        await nextTick();
        showLogActivityModal.value = true;
    };

    const closeLogActivityModal = () => {
        showLogActivityModal.value = false;
        preselectedHabit.value = null;
    };

    return {
        showLogActivityModal,
        preselectedHabit,
        openLogActivityModal,
        closeLogActivityModal,
    };
}
