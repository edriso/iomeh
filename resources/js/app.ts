import '../css/app.css';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'IOMeH';

// Configure global session timeout handling
const handleSessionTimeout = () => {
    // Check if we're not already on an auth page to avoid redirect loops
    const currentPath = window.location.pathname;
    const authPaths = [
        '/login',
        '/register',
        '/forgot-password',
        '/reset-password',
        '/errors/401',
        '/errors/419',
    ];

    if (!authPaths.some((path) => currentPath.includes(path))) {
        // Clear any cached data
        if (typeof window !== 'undefined') {
            window.sessionStorage?.clear();
            window.localStorage?.removeItem('sidebar_state');
        }

        // Use direct navigation for better mobile compatibility
        window.location.href = '/login';
    }
};

// Set up Inertia router hooks for better session handling
router.on('exception', (event) => {
    const exception = event.detail.exception;

    // Handle specific Inertia response errors
    if (exception && exception.message) {
        const message = exception.message.toLowerCase();
        if (
            message.includes(
                'all inertia requests must receive a valid inertia response',
            ) ||
            message.includes('plain json response was received') ||
            message.includes('401') ||
            message.includes('419') ||
            message.includes('unauthenticated') ||
            message.includes('csrf') ||
            message.includes('token mismatch')
        ) {
            event.preventDefault();
            handleSessionTimeout();
        }
    }
});

router.on('error', (event) => {
    // Handle validation/form errors with auth issues
    const errors = event.detail.errors;
    if (errors) {
        const errorMessages = Object.values(errors).flat();
        const hasAuthError = errorMessages.some(
            (error: any) =>
                typeof error === 'string' &&
                (error.toLowerCase().includes('unauthenticated') ||
                    error.toLowerCase().includes('csrf') ||
                    error.toLowerCase().includes('session expired') ||
                    error.toLowerCase().includes('token mismatch')),
        );

        if (hasAuthError) {
            handleSessionTimeout();
        }
    }
});

// Handle Inertia response errors
router.on('invalid', (event) => {
    const response = event.detail.response;
    if (response && response.status) {
        if (response.status === 401 || response.status === 419) {
            event.preventDefault();
            handleSessionTimeout();
        }
    }
});

// Override fetch for comprehensive session timeout coverage
const originalFetch = window.fetch;
window.fetch = async (...args) => {
    try {
        const response = await originalFetch(...args);

        // Handle authentication errors for fetch requests
        if (response.status === 401 || response.status === 419) {
            handleSessionTimeout();
        }

        return response;
    } catch (error) {
        throw error;
    }
};

createInertiaApp({
    title: (title) => (title ? `${title} | ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) }).use(plugin);

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// Global error handling for unhandled promise rejections
window.addEventListener('unhandledrejection', (event) => {
    const error = event.reason;
    if (error && typeof error === 'object') {
        const message = error.message || error.toString();
        if (
            message.toLowerCase().includes('401') ||
            message.toLowerCase().includes('419') ||
            message.toLowerCase().includes('unauthenticated') ||
            message.toLowerCase().includes('csrf') ||
            message.toLowerCase().includes('token mismatch') ||
            message
                .toLowerCase()
                .includes(
                    'all inertia requests must receive a valid inertia response',
                )
        ) {
            event.preventDefault();
            handleSessionTimeout();
        }
    }
});

// Global error handling for JavaScript errors
window.addEventListener('error', (event) => {
    const error = event.error;
    if (error && error.message) {
        const message = error.message.toLowerCase();
        if (
            message.includes('401') ||
            message.includes('419') ||
            message.includes('unauthenticated') ||
            message.includes('csrf') ||
            message.includes('token mismatch') ||
            message.includes(
                'all inertia requests must receive a valid inertia response',
            )
        ) {
            event.preventDefault();
            handleSessionTimeout();
        }
    }
});
