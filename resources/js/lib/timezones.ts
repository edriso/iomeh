// Comprehensive timezone data with major cities and regions
// Based on IANA timezone database for accuracy and DST handling

export interface TimezoneOption {
    value: string;
    label: string;
    city: string;
    country: string;
    offset: string;
    group: string;
}

export const timezoneOptions: TimezoneOption[] = [
    // Americas
    {
        value: 'America/New_York',
        label: 'Eastern Time (ET)',
        city: 'New York',
        country: 'United States',
        offset: 'UTC-5/-4',
        group: 'Americas',
    },
    {
        value: 'America/Chicago',
        label: 'Central Time (CT)',
        city: 'Chicago',
        country: 'United States',
        offset: 'UTC-6/-5',
        group: 'Americas',
    },
    {
        value: 'America/Denver',
        label: 'Mountain Time (MT)',
        city: 'Denver',
        country: 'United States',
        offset: 'UTC-7/-6',
        group: 'Americas',
    },
    {
        value: 'America/Los_Angeles',
        label: 'Pacific Time (PT)',
        city: 'Los Angeles',
        country: 'United States',
        offset: 'UTC-8/-7',
        group: 'Americas',
    },
    {
        value: 'America/Toronto',
        label: 'Eastern Time (ET)',
        city: 'Toronto',
        country: 'Canada',
        offset: 'UTC-5/-4',
        group: 'Americas',
    },
    {
        value: 'America/Vancouver',
        label: 'Pacific Time (PT)',
        city: 'Vancouver',
        country: 'Canada',
        offset: 'UTC-8/-7',
        group: 'Americas',
    },
    {
        value: 'America/Mexico_City',
        label: 'Central Time (CT)',
        city: 'Mexico City',
        country: 'Mexico',
        offset: 'UTC-6/-5',
        group: 'Americas',
    },
    {
        value: 'America/Sao_Paulo',
        label: 'Brasília Time (BRT)',
        city: 'São Paulo',
        country: 'Brazil',
        offset: 'UTC-3',
        group: 'Americas',
    },
    {
        value: 'America/Argentina/Buenos_Aires',
        label: 'Argentina Time (ART)',
        city: 'Buenos Aires',
        country: 'Argentina',
        offset: 'UTC-3',
        group: 'Americas',
    },

    // Europe
    {
        value: 'Europe/London',
        label: 'Greenwich Mean Time (GMT)',
        city: 'London',
        country: 'United Kingdom',
        offset: 'UTC+0/+1',
        group: 'Europe',
    },
    {
        value: 'Europe/Paris',
        label: 'Central European Time (CET)',
        city: 'Paris',
        country: 'France',
        offset: 'UTC+1/+2',
        group: 'Europe',
    },
    {
        value: 'Europe/Berlin',
        label: 'Central European Time (CET)',
        city: 'Berlin',
        country: 'Germany',
        offset: 'UTC+1/+2',
        group: 'Europe',
    },
    {
        value: 'Europe/Rome',
        label: 'Central European Time (CET)',
        city: 'Rome',
        country: 'Italy',
        offset: 'UTC+1/+2',
        group: 'Europe',
    },
    {
        value: 'Europe/Madrid',
        label: 'Central European Time (CET)',
        city: 'Madrid',
        country: 'Spain',
        offset: 'UTC+1/+2',
        group: 'Europe',
    },
    {
        value: 'Europe/Amsterdam',
        label: 'Central European Time (CET)',
        city: 'Amsterdam',
        country: 'Netherlands',
        offset: 'UTC+1/+2',
        group: 'Europe',
    },
    {
        value: 'Europe/Stockholm',
        label: 'Central European Time (CET)',
        city: 'Stockholm',
        country: 'Sweden',
        offset: 'UTC+1/+2',
        group: 'Europe',
    },
    {
        value: 'Europe/Moscow',
        label: 'Moscow Time (MSK)',
        city: 'Moscow',
        country: 'Russia',
        offset: 'UTC+3',
        group: 'Europe',
    },
    {
        value: 'Europe/Istanbul',
        label: 'Turkey Time (TRT)',
        city: 'Istanbul',
        country: 'Turkey',
        offset: 'UTC+3',
        group: 'Europe',
    },

    // Asia
    {
        value: 'Asia/Tokyo',
        label: 'Japan Standard Time (JST)',
        city: 'Tokyo',
        country: 'Japan',
        offset: 'UTC+9',
        group: 'Asia',
    },
    {
        value: 'Asia/Shanghai',
        label: 'China Standard Time (CST)',
        city: 'Shanghai',
        country: 'China',
        offset: 'UTC+8',
        group: 'Asia',
    },
    {
        value: 'Asia/Hong_Kong',
        label: 'Hong Kong Time (HKT)',
        city: 'Hong Kong',
        country: 'Hong Kong',
        offset: 'UTC+8',
        group: 'Asia',
    },
    {
        value: 'Asia/Singapore',
        label: 'Singapore Time (SGT)',
        city: 'Singapore',
        country: 'Singapore',
        offset: 'UTC+8',
        group: 'Asia',
    },
    {
        value: 'Asia/Seoul',
        label: 'Korea Standard Time (KST)',
        city: 'Seoul',
        country: 'South Korea',
        offset: 'UTC+9',
        group: 'Asia',
    },
    {
        value: 'Asia/Kolkata',
        label: 'India Standard Time (IST)',
        city: 'Mumbai',
        country: 'India',
        offset: 'UTC+5:30',
        group: 'Asia',
    },
    {
        value: 'Asia/Dubai',
        label: 'Gulf Standard Time (GST)',
        city: 'Dubai',
        country: 'UAE',
        offset: 'UTC+4',
        group: 'Asia',
    },
    {
        value: 'Asia/Bangkok',
        label: 'Indochina Time (ICT)',
        city: 'Bangkok',
        country: 'Thailand',
        offset: 'UTC+7',
        group: 'Asia',
    },
    {
        value: 'Asia/Jakarta',
        label: 'Western Indonesia Time (WIB)',
        city: 'Jakarta',
        country: 'Indonesia',
        offset: 'UTC+7',
        group: 'Asia',
    },

    // Africa
    {
        value: 'Africa/Cairo',
        label: 'Eastern European Time (EET)',
        city: 'Cairo',
        country: 'Egypt',
        offset: 'UTC+2',
        group: 'Africa',
    },
    {
        value: 'Africa/Johannesburg',
        label: 'South Africa Standard Time (SAST)',
        city: 'Johannesburg',
        country: 'South Africa',
        offset: 'UTC+2',
        group: 'Africa',
    },
    {
        value: 'Africa/Lagos',
        label: 'West Africa Time (WAT)',
        city: 'Lagos',
        country: 'Nigeria',
        offset: 'UTC+1',
        group: 'Africa',
    },
    {
        value: 'Africa/Nairobi',
        label: 'East Africa Time (EAT)',
        city: 'Nairobi',
        country: 'Kenya',
        offset: 'UTC+3',
        group: 'Africa',
    },

    // Oceania
    {
        value: 'Australia/Sydney',
        label: 'Australian Eastern Time (AET)',
        city: 'Sydney',
        country: 'Australia',
        offset: 'UTC+10/+11',
        group: 'Oceania',
    },
    {
        value: 'Australia/Melbourne',
        label: 'Australian Eastern Time (AET)',
        city: 'Melbourne',
        country: 'Australia',
        offset: 'UTC+10/+11',
        group: 'Oceania',
    },
    {
        value: 'Australia/Perth',
        label: 'Australian Western Time (AWT)',
        city: 'Perth',
        country: 'Australia',
        offset: 'UTC+8',
        group: 'Oceania',
    },
    {
        value: 'Pacific/Auckland',
        label: 'New Zealand Time (NZST)',
        city: 'Auckland',
        country: 'New Zealand',
        offset: 'UTC+12/+13',
        group: 'Oceania',
    },

    // UTC and others
    {
        value: 'UTC',
        label: 'Coordinated Universal Time (UTC)',
        city: 'UTC',
        country: 'UTC',
        offset: 'UTC+0',
        group: 'UTC',
    },
];

// Helper function to get current time in a timezone
export function getCurrentTimeInTimezone(timezone: string): string {
    try {
        return new Date().toLocaleString('en-US', {
            timeZone: timezone,
            hour12: true,
            hour: 'numeric',
            minute: '2-digit',
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
    } catch {
        return 'Invalid timezone';
    }
}

// Helper function to detect user's timezone
export function detectUserTimezone(): string {
    try {
        return Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch {
        return 'UTC';
    }
}

// Helper function to find timezone option by value
export function findTimezoneOption(value: string): TimezoneOption | undefined {
    return timezoneOptions.find((option) => option.value === value);
}

// Helper function to get timezone groups
export function getTimezoneGroups(): string[] {
    return [...new Set(timezoneOptions.map((option) => option.group))];
}

// Helper function to filter timezones by search term
export function filterTimezones(searchTerm: string): TimezoneOption[] {
    if (!searchTerm.trim()) return timezoneOptions;

    const term = searchTerm.toLowerCase();
    return timezoneOptions.filter(
        (option) =>
            option.city.toLowerCase().includes(term) ||
            option.country.toLowerCase().includes(term) ||
            option.label.toLowerCase().includes(term) ||
            option.value.toLowerCase().includes(term),
    );
}
