import { nextTick, ref } from 'vue';

// Global state for log activity modal
const showLogActivityModal = ref(false);

export function useLogActivity() {
    const openLogActivityModal = async () => {
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
