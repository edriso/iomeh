import { computed, ref } from 'vue';

export interface CalendarDay {
    date: string;
    activities_count: number;
    points_earned: number;
}

export interface CalendarDataResponse {
    data: CalendarDay[];
    month: number;
    year: number;
    has_more_data: boolean;
}

export function useCalendarData(
    initialData: CalendarDay[] = [],
    username?: string,
) {
    const calendarData = ref<Map<string, CalendarDay>>(new Map());
    const loading = ref(false);
    const loadedMonths = ref<Set<string>>(new Set());

    // Initialize with provided data
    initialData.forEach((day) => {
        calendarData.value.set(day.date, day);
    });

    // Mark initial months as loaded
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();
    loadedMonths.value.add(`${currentYear}-${currentMonth}`);

    const getMonthKey = (year: number, month: number): string => {
        return `${year}-${month}`;
    };

    const isMonthLoaded = (year: number, month: number): boolean => {
        return loadedMonths.value.has(getMonthKey(year, month));
    };

    const loadMonthData = async (
        year: number,
        month: number,
    ): Promise<void> => {
        if (isMonthLoaded(year, month)) {
            return; // Already loaded
        }

        // Note: Navigation validation is now handled at the component level
        // This composable focuses on data loading only

        loading.value = true;

        try {
            const url = new URL('/api/calendar-data', window.location.origin);
            url.searchParams.set('year', year.toString());
            url.searchParams.set('month', month.toString());
            if (username) {
                url.searchParams.set('username', username);
            }

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.ok) {
                const data: CalendarDataResponse = await response.json();

                // Merge new data with existing data
                data.data.forEach((day) => {
                    calendarData.value.set(day.date, day);
                });

                // Mark month as loaded
                loadedMonths.value.add(getMonthKey(year, month));
            }
        } catch {
            // Silent fail for calendar data loading
        } finally {
            loading.value = false;
        }
    };

    const getDaysForMonth = (year: number, month: number): CalendarDay[] => {
        const days: CalendarDay[] = [];
        const startDate = new Date(year, month, 1);
        const endDate = new Date(year, month + 1, 0);

        for (
            let date = new Date(startDate);
            date <= endDate;
            date.setDate(date.getDate() + 1)
        ) {
            const dateString =
                date.getFullYear() +
                '-' +
                String(date.getMonth() + 1).padStart(2, '0') +
                '-' +
                String(date.getDate()).padStart(2, '0');
            const dayData = calendarData.value.get(dateString);

            days.push(
                dayData || {
                    date: dateString,
                    activities_count: 0,
                    points_earned: 0,
                },
            );
        }

        return days;
    };

    const hasDataForMonth = (year: number, month: number): boolean => {
        const days = getDaysForMonth(year, month);
        return days.some((day) => day.activities_count > 0);
    };

    return {
        calendarData: computed(() => Array.from(calendarData.value.values())),
        loading,
        loadedMonths,
        loadMonthData,
        getDaysForMonth,
        hasDataForMonth,
        isMonthLoaded,
    };
}
