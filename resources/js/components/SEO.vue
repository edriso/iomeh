<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

interface SEOProps {
    title: string;
    description?: string;
    keywords?: readonly string[];
    image?: string;
    imageAlt?: string;
    url?: string;
    type?: 'website' | 'article' | 'profile' | 'book';
    author?: string;
    publishedTime?: string;
    modifiedTime?: string;
    locale?: string;
    siteName?: string;
    twitterCard?: 'summary' | 'summary_large_image' | 'app' | 'player';
    twitterSite?: string;
    twitterCreator?: string;
    noIndex?: boolean;
    noFollow?: boolean;
    canonical?: string;
}

const props = withDefaults(defineProps<SEOProps>(), {
    description:
        'Track your health and fitness journey with IOMEH (I Owe Me Health) - the gamified health tracking platform. Build consistent habits, earn points, and compete on global rankings.',
    keywords: () => [
        'health tracking',
        'fitness platform',
        'wellness',
        'workout tracking',
        'nutrition tracking',
        'health gamification',
        'fitness rankings',
    ],
    type: 'website',
    locale: 'en_US',
    siteName: 'IOMEH',
    twitterCard: 'summary_large_image',
    twitterSite: '@iomeh',
    noIndex: false,
    noFollow: false,
});

// Get app configuration
const appName = 'IOMEH';
const appUrl = typeof window !== 'undefined' ? window.location.origin : '';

// Computed properties for meta tags
// Note: Inertia's title callback already appends " | IOMEH" to the browser title
// But we need the full title for Open Graph and Twitter meta tags
const fullTitle = computed(() => {
    return props.title === appName
        ? props.title
        : `${props.title} | ${appName}`;
});

const fullUrl = computed(() => {
    if (props.url) {
        return props.url.startsWith('http')
            ? props.url
            : `${appUrl}${props.url}`;
    }
    return typeof window !== 'undefined' ? window.location.href : appUrl;
});

const defaultImage = computed(() => {
    return props.image || `${appUrl}/iomeh.png`;
});

const robotsContent = computed(() => {
    const robots = [];
    if (props.noIndex) robots.push('noindex');
    if (props.noFollow) robots.push('nofollow');
    if (robots.length === 0) robots.push('index', 'follow');
    return robots.join(', ');
});

const keywordsString = computed(() => {
    return props.keywords?.join(', ') || '';
});
</script>

<template>
    <Head>
        <!-- Basic Meta Tags -->
        <title>{{ props.title }}</title>
        <meta name="description" :content="props.description" />
        <meta name="keywords" :content="keywordsString" v-if="keywordsString" />
        <meta name="robots" :content="robotsContent" />
        <meta name="author" :content="props.author" v-if="props.author" />

        <!-- Canonical URL -->
        <link rel="canonical" :href="props.canonical || fullUrl" />

        <!-- Open Graph Meta Tags -->
        <meta property="og:title" :content="fullTitle" />
        <meta property="og:description" :content="props.description" />
        <meta property="og:image" :content="defaultImage" />
        <meta
            property="og:image:alt"
            :content="props.imageAlt || props.title"
        />
        <meta property="og:url" :content="fullUrl" />
        <meta property="og:type" :content="props.type" />
        <meta property="og:locale" :content="props.locale" />
        <meta property="og:site_name" :content="props.siteName" />

        <!-- Article specific Open Graph tags -->
        <template v-if="props.type === 'article'">
            <meta
                property="article:author"
                :content="props.author"
                v-if="props.author"
            />
            <meta
                property="article:published_time"
                :content="props.publishedTime"
                v-if="props.publishedTime"
            />
            <meta
                property="article:modified_time"
                :content="props.modifiedTime"
                v-if="props.modifiedTime"
            />
        </template>

        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" :content="props.twitterCard" />
        <meta
            name="twitter:site"
            :content="props.twitterSite"
            v-if="props.twitterSite"
        />
        <meta
            name="twitter:creator"
            :content="props.twitterCreator"
            v-if="props.twitterCreator"
        />
        <meta name="twitter:title" :content="fullTitle" />
        <meta name="twitter:description" :content="props.description" />
        <meta name="twitter:image" :content="defaultImage" />
        <meta
            name="twitter:image:alt"
            :content="props.imageAlt || props.title"
        />

        <!-- Additional SEO Meta Tags -->
        <meta name="theme-color" content="#c4e456" />
        <meta name="msapplication-TileColor" content="#c4e456" />
        <meta name="application-name" :content="appName" />
    </Head>
</template>
