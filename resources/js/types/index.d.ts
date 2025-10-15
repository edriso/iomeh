import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id?: number;
    username?: string;
    name?: string;
    email?: string;
    avatar?: string | null;
    bio?: string | null;
    website_url?: string | null;
    current_season_points?: number;
    current_year_points?: number;
    lifetime_points?: number;
    current_streak?: number;
    longest_streak?: number;
    last_activity_date?: string | null;
    is_active?: boolean;
    email_verified_at?: string | null;
    created_at?: string;
    updated_at?: string | null;
}

export type BreadcrumbItemType = BreadcrumbItem;
