<script setup lang="ts">
import { Alert, AlertDescription } from '@/components/ui/alert';
import {
    AlertCircle,
    AlertTriangle,
    CheckCircle2,
    Info,
} from 'lucide-vue-next';

interface Props {
    type: 'success' | 'error' | 'warning' | 'info';
    message: string;
    show?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    show: true,
});

const getIcon = () => {
    switch (props.type) {
        case 'success':
            return CheckCircle2;
        case 'error':
            return AlertCircle;
        case 'warning':
            return AlertTriangle;
        case 'info':
            return Info;
        default:
            return AlertCircle;
    }
};

const getVariant = () => {
    switch (props.type) {
        case 'error':
            return 'destructive';
        case 'warning':
            return 'default';
        default:
            return 'default';
    }
};

const getClasses = () => {
    switch (props.type) {
        case 'success':
            return 'border-primary/20 bg-primary/5 text-primary-foreground';
        case 'error':
            return ''; // Uses destructive variant
        case 'warning':
            return 'border-secondary/20 bg-secondary/5 text-secondary-foreground';
        case 'info':
            return 'border-primary/20 bg-primary/5 text-primary-foreground';
        default:
            return '';
    }
};
</script>

<template>
    <Alert
        v-if="show && message"
        :variant="getVariant()"
        :class="getClasses()"
        class="mb-4"
    >
        <component :is="getIcon()" class="h-4 w-4 flex-shrink-0" />
        <AlertDescription class="break-words">{{ message }}</AlertDescription>
    </Alert>
</template>
