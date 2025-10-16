<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useCalendarData } from '@/composables/useCalendarData';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, ref } from 'vue';

export interface Day {
    date: string;
    count: number;
}

interface Props {
    days?: Day[];
    week_starts_on?: number; // 0=Sunday, 1=Monday, 6=Saturday
    enableLazyLoading?: boolean; // Enable lazy loading for month switches
    username?: string; // Username for loading other users' calendar data
    userCreatedAt?: string; // User creation date to prevent requests for earlier months
}

const props = withDefaults(defineProps<Props>(), {
    days: () => [],
    week_starts_on: 0,
    enableLazyLoading: false,
    username: undefined,
    userCreatedAt: undefined,
});

const emit = defineEmits<{
    (e: 'day-click', date: string): void;
}>();

const handleDayClick = (date: string, count: number) => {
    if (count > 0) {
        emit('day-click', date);
    }
};

// Current month and year
const currentDate = ref(new Date());
const currentMonth = ref(currentDate.value.getMonth());
const currentYear = ref(currentDate.value.getFullYear());

// Ensure we start in a valid month (not before user creation)
if (props.userCreatedAt) {
    const userCreatedDate = new Date(props.userCreatedAt);
    const userCreatedMonth = userCreatedDate.getMonth();
    const userCreatedYear = userCreatedDate.getFullYear();

    // If current month is before user creation, move to user creation month
    if (
        currentYear.value < userCreatedYear ||
        (currentYear.value === userCreatedYear &&
            currentMonth.value < userCreatedMonth)
    ) {
        currentMonth.value = userCreatedMonth;
        currentYear.value = userCreatedYear;
    }
}

// Initialize calendar data composable
const { loadMonthData, getDaysForMonth, isMonthLoaded, loading } =
    useCalendarData(
        props.days.map((day) => ({
            date: day.date,
            activities_count: day.count,
            points_earned: 0,
        })),
        props.username,
    );

function intensityClass(count: number) {
    if (count <= 0) return 'bg-muted/30';
    return 'bg-primary text-primary-foreground';
}

const monthNames = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December',
];

const dayNames = computed(() => {
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    if (props.week_starts_on === 1) {
        // Monday start
        return ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    } else if (props.week_starts_on === 6) {
        // Saturday start
        return ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
    }
    return days; // Sunday start
});

const calendarData = computed(() => {
    // Get data for current month (either from props or loaded data)
    let activityMap: Map<string, number>;

    if (props.enableLazyLoading) {
        // Use loaded data for the current month
        const monthDays = getDaysForMonth(
            currentYear.value,
            currentMonth.value,
        );
        activityMap = new Map<string, number>();
        monthDays.forEach((day) => {
            activityMap.set(day.date, day.activities_count);
        });
    } else {
        // Use props data (original behavior)
        activityMap = new Map<string, number>();
        props.days.forEach((day) => {
            activityMap.set(day.date, day.count);
        });
    }

    // Get first day of current month
    const firstDay = new Date(currentYear.value, currentMonth.value, 1);
    const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);

    // Calculate starting day of week (0=Sunday, 1=Monday, etc.)
    let startDay = firstDay.getDay();
    if (props.week_starts_on === 1) {
        // Monday start
        startDay = startDay === 0 ? 6 : startDay - 1;
    } else if (props.week_starts_on === 6) {
        // Saturday start
        startDay = startDay === 6 ? 0 : startDay + 1;
    }

    // Generate calendar grid
    const weeks = [];
    let currentWeek = [];

    // Add empty cells for days before month starts
    for (let i = 0; i < startDay; i++) {
        currentWeek.push({
            date: '',
            count: 0,
            dayNumber: '',
            isCurrentMonth: false,
            isToday: false,
        });
    }

    // Add days of the month
    for (let day = 1; day <= lastDay.getDate(); day++) {
        const date = new Date(currentYear.value, currentMonth.value, day);
        const dateString =
            date.getFullYear() +
            '-' +
            String(date.getMonth() + 1).padStart(2, '0') +
            '-' +
            String(date.getDate()).padStart(2, '0');
        const count = activityMap.get(dateString) || 0;
        const isToday = date.toDateString() === new Date().toDateString();

        currentWeek.push({
            date: dateString,
            count,
            dayNumber: day.toString(),
            isCurrentMonth: true,
            isToday,
        });

        // Start new week if we have 7 days
        if (currentWeek.length === 7) {
            weeks.push([...currentWeek]);
            currentWeek = [];
        }
    }

    // Fill remaining cells in last week
    while (currentWeek.length < 7 && currentWeek.length > 0) {
        currentWeek.push({
            date: '',
            count: 0,
            dayNumber: '',
            isCurrentMonth: false,
            isToday: false,
        });
    }

    if (currentWeek.length > 0) {
        weeks.push(currentWeek);
    }

    return weeks;
});

const monthYearDisplay = computed(() => {
    return `${monthNames[currentMonth.value]} ${currentYear.value}`;
});

// Navigation state computed properties
const canGoToPreviousMonth = computed(() => {
    if (!props.userCreatedAt) {
        return true;
    }

    const userCreatedDate = new Date(props.userCreatedAt);
    const userCreatedMonth = userCreatedDate.getMonth();
    const userCreatedYear = userCreatedDate.getFullYear();

    // Calculate what the previous month would be
    const previousMonth =
        currentMonth.value === 0 ? 11 : currentMonth.value - 1;
    const previousYear =
        currentMonth.value === 0 ? currentYear.value - 1 : currentYear.value;

    // Check if the previous month would be before the user creation month
    const isBeforeCreation =
        previousYear < userCreatedYear ||
        (previousYear === userCreatedYear && previousMonth < userCreatedMonth);

    return !isBeforeCreation;
});

const canGoToNextMonth = computed(() => {
    const now = new Date();
    const nextMonth = currentMonth.value === 11 ? 0 : currentMonth.value + 1;
    const nextYear =
        currentMonth.value === 11 ? currentYear.value + 1 : currentYear.value;
    const nextMonthDate = new Date(nextYear, nextMonth, 1);

    return nextMonthDate <= now;
});

const goToPreviousMonth = async () => {
    if (!canGoToPreviousMonth.value) return;

    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }

    // Load data for the new month if lazy loading is enabled
    if (
        props.enableLazyLoading &&
        !isMonthLoaded(currentYear.value, currentMonth.value)
    ) {
        await loadMonthData(currentYear.value, currentMonth.value);
    }
};

const goToNextMonth = async () => {
    if (!canGoToNextMonth.value) return;

    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }

    // Load data for the new month if lazy loading is enabled
    if (
        props.enableLazyLoading &&
        !isMonthLoaded(currentYear.value, currentMonth.value)
    ) {
        await loadMonthData(currentYear.value, currentMonth.value);
    }
};

// const goToCurrentMonth = () => {
//     const today = new Date();
//     currentMonth.value = today.getMonth();
//     currentYear.value = today.getFullYear();
// };
</script>

<template>
    <div class="space-y-4">
        <!-- Month Navigation -->
        <div class="mb-6 flex items-center justify-between">
            <!-- Previous Month Button -->
            <div class="flex items-center">
                <Button
                    variant="outline"
                    size="sm"
                    @click="goToPreviousMonth"
                    :disabled="loading || !canGoToPreviousMonth"
                    :title="
                        !canGoToPreviousMonth
                            ? 'Cannot navigate before user creation date'
                            : 'Previous month'
                    "
                    class="h-8 w-8 p-0 transition-colors hover:bg-primary/10 disabled:opacity-50"
                >
                    <ChevronLeft class="h-4 w-4" />
                </Button>
            </div>

            <!-- Month/Year Display -->
            <div class="flex items-center gap-3">
                <h3 class="text-xl text-foreground">
                    {{ monthYearDisplay }}
                </h3>
                <!-- <Button
                    variant="outline"
                    size="sm"
                    @click="goToCurrentMonth"
                    class="text-xs px-3 py-1.5 hover:bg-primary/10 transition-colors"
                >
                    Today
                </Button> -->
            </div>

            <!-- Next Month Button -->
            <div class="flex items-center">
                <Button
                    variant="outline"
                    size="sm"
                    @click="goToNextMonth"
                    :disabled="loading || !canGoToNextMonth"
                    :title="
                        !canGoToNextMonth
                            ? 'Cannot navigate to future months'
                            : 'Next month'
                    "
                    class="h-8 w-8 p-0 transition-colors hover:bg-primary/10 disabled:opacity-50"
                >
                    <ChevronRight class="h-4 w-4" />
                </Button>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="cursor-default space-y-2">
            <!-- Day Names Header -->
            <div class="grid grid-cols-7 justify-items-center gap-2">
                <div
                    v-for="dayName in dayNames"
                    :key="dayName"
                    class="flex h-10 w-10 items-center justify-center text-center text-sm text-muted-foreground"
                >
                    {{ dayName }}
                </div>
            </div>

            <!-- Calendar Days -->
            <div class="grid grid-cols-7 justify-items-center gap-2">
                <div
                    v-for="(week, weekIndex) in calendarData"
                    :key="`week-${weekIndex}`"
                    class="contents"
                >
                    <div
                        v-for="(day, dayIndex) in week"
                        :key="`${weekIndex}-${dayIndex}`"
                        :class="[
                            'flex h-10 w-10 items-center justify-center rounded-lg border-2 text-sm transition-all',
                            intensityClass(day.count),
                            {
                                'border-muted/30 text-muted-foreground':
                                    !day.isCurrentMonth,
                                'border-border text-foreground':
                                    day.isCurrentMonth && !day.isToday,
                                'border-primary ring-2 ring-primary ring-offset-2':
                                    day.isToday,
                                'cursor-pointer hover:scale-110 hover:ring-2 hover:ring-primary/50':
                                    day.count > 0,
                            },
                        ]"
                        @click="handleDayClick(day.date, day.count)"
                        :title="
                            day.count > 0
                                ? `${day.count} ${day.count === 1 ? 'activity' : 'activities'}`
                                : ''
                        "
                    >
                        {{ day.dayNumber }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
