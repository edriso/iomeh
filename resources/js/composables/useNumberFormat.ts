/**
 * Composable for formatting numbers with comma separators
 */
export function useNumberFormat() {
    /**
     * Format a number with comma separators (e.g., 1234 -> 1,234)
     */
    const formatNumber = (num: number | string): string => {
        const number = typeof num === 'string' ? parseInt(num, 10) : num;

        if (isNaN(number)) {
            return '0';
        }

        return number.toLocaleString();
    };

    /**
     * Format a number with K, M suffixes for large numbers
     * (e.g., 1234 -> 1.2K, 1234567 -> 1.2M)
     */
    const formatNumberCompact = (num: number | string): string => {
        const number = typeof num === 'string' ? parseInt(num, 10) : num;

        if (isNaN(number)) {
            return '0';
        }

        if (number >= 1000000) {
            return (number / 1000000).toFixed(1) + 'M';
        }
        if (number >= 1000) {
            return (number / 1000).toFixed(1) + 'K';
        }

        return number.toLocaleString();
    };

    return {
        formatNumber,
        formatNumberCompact,
    };
}
