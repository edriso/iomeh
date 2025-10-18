/**
 * Streak Tier Configuration
 * 
 * Defines the streak tiers and their corresponding point multipliers.
 * Each tier rewards users for maintaining consecutive days of activity.
 */

export interface StreakTier {
    min: number;
    max: number;
    name: string;
    next: number | null;
    multiplier: number;
    icon: string;
}

export const STREAK_TIERS: readonly StreakTier[] = [
    { min: 1, max: 2, name: 'Newcomer', next: 3, multiplier: 1.0, icon: '🌱' },
    { min: 3, max: 6, name: 'Beginner', next: 7, multiplier: 1.2, icon: '🔥' },
    { min: 7, max: 13, name: 'Regular', next: 14, multiplier: 1.5, icon: '⚡' },
    { min: 14, max: 29, name: 'Committed', next: 30, multiplier: 2.0, icon: '💪' },
    { min: 30, max: 59, name: 'Dedicated', next: 60, multiplier: 2.5, icon: '🚀' },
    { min: 60, max: 99, name: 'Expert', next: 100, multiplier: 3.0, icon: '⭐' },
    { min: 100, max: 199, name: 'Master', next: 200, multiplier: 4.0, icon: '👑' },
    { min: 200, max: Infinity, name: 'Legend', next: null, multiplier: 5.0, icon: '🏆' },
] as const;

/**
 * Get the current tier for a given streak
 */
export const getCurrentTier = (streak: number): StreakTier => {
    return STREAK_TIERS.find(tier => streak >= tier.min && streak <= tier.max) || STREAK_TIERS[0];
};

/**
 * Calculate progress toward the next tier
 */
export const getTierProgress = (streak: number): number => {
    const currentTier = getCurrentTier(streak);
    
    if (!currentTier.next) return 100;
    
    // Calculate progress toward the next tier
    // For 1-day streak in Newcomer (1-2), we want 33% (1 out of 3 total days)
    const tierStart = currentTier.min;
    const tierEnd = currentTier.next;
    const progress = ((streak - tierStart + 1) / (tierEnd - tierStart + 1)) * 100;
    
    return Math.min(Math.max(progress, 0), 100);
};

/**
 * Get the next tier message with days remaining and multiplier
 */
export const getNextTierMessage = (streak: number): string => {
    const currentTier = getCurrentTier(streak);
    
    if (!currentTier.next) return 'Legend status achieved!';
    
    const daysRemaining = currentTier.next - streak;
    const nextTier = STREAK_TIERS.find(tier => tier.min === currentTier.next);
    
    if (!nextTier) return 'Legend status achieved!';
    
    return `${daysRemaining} days to ${nextTier.name} (${nextTier.multiplier}×)`;
};

/**
 * Get streak display text (singular/plural)
 */
export const getStreakDisplayText = (streak: number): string => {
    return streak === 1 ? 'day' : 'days';
};

/**
 * Get all tier names for display purposes
 */
export const getTierNames = (): string[] => {
    return STREAK_TIERS.map(tier => tier.name);
};

/**
 * Get tier by name
 */
export const getTierByName = (name: string): StreakTier | undefined => {
    return STREAK_TIERS.find(tier => tier.name === name);
};

/**
 * Get the highest tier
 */
export const getHighestTier = (): StreakTier => {
    return STREAK_TIERS[STREAK_TIERS.length - 1];
};

/**
 * Check if a streak has reached the highest tier
 */
export const isHighestTier = (streak: number): boolean => {
    const currentTier = getCurrentTier(streak);
    return currentTier.name === getHighestTier().name;
};
