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
        'Track your health journey with IOMeH (I Owe Me Health) - the gamified health tracking platform that helps you build consistent habits, earn points, compete on global rankings, and achieve your health goals.',
    keywords: [
        'health tracking',
        'fitness platform',
        'health',
        'health gamification',
        'workout tracking',
        'nutrition tracking',
        'health rankings',
        'fitness goals',
        'habit tracking',
    ],
    type: 'website',
    image: '/iomeh.png',
};

// Common SEO configurations for different page types
// Note: The SEO component automatically appends " | IOMeH" to titles
export const seoConfigs = {
    home: {
        title: 'Compete in Health - Earn Your Rank',
        description:
            'Transform your health with IOMeH. Track workouts, nutrition, wellness, and mindfulness. Earn points, build streaks, and compete on global rankings. You owe it to yourself!',
        keywords: [
            'health tracking',
            'fitness platform',
            'health app',
            'workout tracking',
            'nutrition tracking',
            'health gamification',
            'fitness rankings',
            'health goals',
            'habit building',
        ],
        url: '/',
    },

    rankings: {
        title: 'Health Rankings',
        description:
            'See how you rank among health enthusiasts worldwide on IOMeH. Track your progress across daily, seasonal, and yearly rankings. Celebrate your health achievements!',
        keywords: [
            'health rankings',
            'fitness leaderboard',
            'health competition',
            'global rankings',
            'health achievements',
            'fitness progress',
        ],
        url: '/rankings',
    },

    settings: {
        title: 'Account Settings',
        description:
            'Manage your IOMeH account settings, preferences, and profile information.',
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

    foundation: (
        name: string,
        description: string,
        url: string,
        logo: string,
    ) => ({
        '@context': 'https://schema.org',
        '@type': 'Foundation',
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
            name: 'IOMeH',
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
