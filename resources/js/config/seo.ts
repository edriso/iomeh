export interface SEOConfig {
    title: string;
    description: string;
    keywords: string[];
    url?: string;
    image?: string;
    imageAlt?: string;
    type?: 'website' | 'article' | 'profile' | 'book';
    noIndex?: boolean;
}

export const defaultSEO: Partial<SEOConfig> = {
    description:
        'Track your wellness journey with IOMeW (I Owe Me Wellness) - the gamified wellness tracking platform that helps you build consistent habits, earn points, compete on global rankings, and achieve your wellness goals.',
    keywords: [
        'wellness tracking',
        'fitness platform',
        'wellness',
        'wellness gamification',
        'workout tracking',
        'nutrition tracking',
        'wellness rankings',
        'fitness goals',
        'habit tracking',
    ],
    type: 'website',
    image: '/iomew.png',
};

// Common SEO configurations for different page types
// Note: The SEO component automatically appends " | IOMeW" to titles
export const seoConfigs = {
    home: {
        title: 'Compete in Wellness - Earn Your Rank',
        description:
            'Transform your wellness with IOMeW. Track workouts, nutrition, wellness, and mindfulness. Earn points, build streaks, and compete on global rankings. You owe it to yourself!',
        keywords: [
            'wellness tracking',
            'fitness platform',
            'wellness app',
            'workout tracking',
            'nutrition tracking',
            'wellness gamification',
            'fitness rankings',
            'wellness goals',
            'habit building',
        ],
        url: '/',
    },

    rankings: {
        title: 'Wellness Rankings',
        description:
            'See how you rank among wellness enthusiasts worldwide on IOMeW. Track your progress across daily, seasonal, and yearly rankings. Celebrate your wellness achievements!',
        keywords: [
            'wellness rankings',
            'fitness leaderboard',
            'wellness competition',
            'global rankings',
            'wellness achievements',
            'fitness progress',
        ],
        url: '/rankings',
    },

    settings: {
        title: 'Account Settings',
        description:
            'Manage your IOMeW account settings, preferences, and profile information.',
        keywords: [
            'account settings',
            'profile',
            'preferences',
            'user settings',
        ],
        url: '/settings',
        noIndex: true, // Private page
    },
};

// Helper function to create structured data for different content types
export const createStructuredData = {
    website: (name: string, description: string, url: string) => ({
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name,
        description,
        url,
        potentialAction: {
            '@type': 'SearchAction',
            target: {
                '@type': 'EntryPoint',
                urlTemplate: `${url}/search?q={search_term_string}`,
            },
            'query-input': 'required name=search_term_string',
        },
    }),

    organization: (
        name: string,
        description: string,
        url: string,
        logo: string,
    ) => ({
        '@context': 'https://schema.org',
        '@type': 'Organization',
        name,
        description,
        url,
        logo: {
            '@type': 'ImageObject',
            url: logo,
        },
        sameAs: [
            // Add social media profiles here
        ],
    }),

    webpage: (name: string, description: string, url: string) => ({
        '@context': 'https://schema.org',
        '@type': 'WebPage',
        name,
        description,
        url,
        isPartOf: {
            '@type': 'WebSite',
            name: 'IOMeW',
        },
    }),

    healthActivity: (
        name: string,
        description: string,
        points: number,
        category: string,
    ) => ({
        '@context': 'https://schema.org',
        '@type': 'ExerciseAction',
        name,
        description,
        exercisePlan: {
            '@type': 'ExercisePlan',
            name: category,
        },
        additionalProperty: {
            '@type': 'PropertyValue',
            name: 'Points',
            value: points,
        },
    }),
};
