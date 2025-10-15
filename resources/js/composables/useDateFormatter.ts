/**
 * Composable for consistent date formatting across the application
 */
export function useDateFormatter() {
    /**
     * Format a date to a long readable format
     * @param dateString - ISO date string
     * @returns Formatted date string (e.g., "January 1, 2024")
     */
    const formatLongDate = (dateString: string): string => {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
        });
    };

    /**
     * Format a date to a short format with time
     * @param dateString - ISO date string
     * @returns Formatted date string (e.g., "Jan 1, 10:30 AM")
     */
    const formatShortDateTime = (dateString: string): string => {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    /**
     * Format a date to a relative time format
     * @param dateString - ISO date string
     * @returns Relative date string (e.g., "Yesterday", "2 days ago", "3 weeks ago")
     */
    const formatRelativeDate = (dateString: string): string => {
        const date = new Date(dateString);
        const now = new Date();

        // Set both dates to start of day for accurate day comparison
        const today = new Date(
            now.getFullYear(),
            now.getMonth(),
            now.getDate(),
        );
        const targetDate = new Date(
            date.getFullYear(),
            date.getMonth(),
            date.getDate(),
        );

        const diffTime = today.getTime() - targetDate.getTime();
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return 'Today';
        if (diffDays === 1) return 'Yesterday';
        if (diffDays < 7) return `${diffDays} days ago`;
        if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;

        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
        });
    };

    return {
        formatLongDate,
        formatShortDateTime,
        formatRelativeDate,
    };
}
