import { render, screen } from '@testing-library/vue';
import { describe, it, expect } from 'vitest';
import InputError from '@/components/InputError.vue';

describe('InputError', () => {
    it('shows error message', () => {
        render(InputError, {
            props: { message: 'Error here' },
        });

        expect(screen.getByText('Error here')).toBeInTheDocument();
    });

    it('hides when no message', () => {
        render(InputError, {
            props: { message: '' },
        });

        expect(screen.queryByText('Error here')).not.toBeInTheDocument();
    });
});
