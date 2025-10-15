import { render, screen } from '@testing-library/vue';
import { describe, it, expect } from 'vitest';
import { Button } from '@/components/ui/button';

describe('Button', () => {
    it('renders button text', () => {
        render(Button, {
            slots: { default: 'Click me' },
        });

        expect(screen.getByText('Click me')).toBeInTheDocument();
    });

    it('can be disabled', () => {
        render(Button, {
            props: { disabled: true },
            slots: { default: 'Disabled' },
        });

        expect(screen.getByRole('button')).toBeDisabled();
    });
});
