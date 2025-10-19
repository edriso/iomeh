import { ref } from 'vue';

const csrfToken = ref<string>('');

export function useCsrfToken() {
    const getCsrfToken = async (): Promise<string> => {
        // First try to get from meta tag
        let token =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content') || '';

        if (token) {
            csrfToken.value = token;
            return token;
        }

        // If no token in meta tag, try to get fresh one
        try {
            const response = await fetch('/csrf-token', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.ok) {
                const data = await response.json();
                token = data.csrf_token;
                csrfToken.value = token;
                return token;
            }
        } catch {
            // Silent fail for CSRF token refresh
        }

        return token;
    };

    const getHeaders = async (): Promise<Record<string, string>> => {
        const token = await getCsrfToken();
        return {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token,
        };
    };

    return {
        csrfToken,
        getCsrfToken,
        getHeaders,
    };
}
