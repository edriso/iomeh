import { computed, ref } from 'vue';

// Translation data - this would typically come from the backend
const translations = {
    en: {
        // Navigation
        'nav.home': 'Home',
        'nav.rankings': 'Rankings',
        'nav.profile': 'Profile',
        'nav.settings': 'Settings',
        'nav.logout': 'Logout',
        'nav.login': 'Login',
        'nav.register': 'Register',
        'nav.view_profile': 'View profile',

        // Landing Page
        'landing.title': 'Global Fitness Ranking Platform',
        'landing.subtitle':
            'IOMeH (I Owe Me Health) is your personal fitness ranking platform. Track workouts and nutrition activities while competing on global rankings. Turn fitness into competition.',
        'landing.badge':
            'Your Global Health Rank — Shaped by Your Daily Habits',
        'landing.hero_title': 'Track Your Health, Earn Your Rank',
        'landing.get_started': 'Get Started',
        'landing.learn_more': 'Learn More',
        'landing.stats.active_users': 'Active Users',
        'landing.stats.total_activities': 'Total Activities',
        'landing.stats.combined_streak': 'Combined Streak',
        'landing.progress_text': 'Track your progress across seasons',
        'landing.community_title': 'Join the Global Health Community',
        'landing.season_label': 'Season (Q1-Q4)',
        'landing.rank_example': 'Track your rank: "2025 Q1 #11".',
        'landing.quarterly_rank': 'Quarterly rankings',
        'landing.climb_rankings': 'Climb the Rankings',
        'landing.compete_description':
            'Compete on daily, seasonal, and yearly rankings.',

        // Quarters
        'quarter.q1': 'Q1',
        'quarter.q2': 'Q2',
        'quarter.q3': 'Q3',
        'quarter.q4': 'Q4',

        // Points
        'points.short': 'pts',
        'points.long': 'points',

        // Achievement badges
        'badge.quarter_rank': '2025 Q4 #7',
        'badge.year_rank': '2025 #15',
        'badge.streak': '14 Day Streak',

        // Additional landing page sections
        'landing.fitness_journey_title': 'Track Your Fitness Journey',
        'landing.fitness_journey_description':
            'Focus on what matters most for your fitness goals. IOMeH tracks workouts and nutrition with many activities to choose from.',
        'landing.workout_title': '💪 Workout',
        'landing.workout_description':
            'Running, gym, HIIT, swimming, cycling, sports, martial arts, and more. Points based on intensity and duration.',
        'landing.nutrition_title': '🥗 Nutrition',
        'landing.nutrition_description':
            'Healthy meals, vegetables, fruits, hydration, protein goals, supplements, and cooking at home.',
        'landing.workout_stats': '30 activities | 10-60 points',
        'landing.nutrition_stats': '15 activities | 10-30 points',
        'landing.streak_bonus_title': '🔥 Streak Bonus System',
        'landing.streak_bonus_description':
            'Consistency is key. Build daily streaks and multiply your points with progressive rewards up to',
        'landing.how_it_works_title': 'How It Works',
        'landing.build_streaks_title': 'Build Streaks',
        'landing.build_streaks_description':
            'Log activities daily to maintain your streak. Miss a day and it resets to 1.',
        'landing.multiply_points_title': 'Multiply Points',
        'landing.multiply_points_description':
            'Your streak tier multiplies all activity points.',
        'landing.milestone_bonuses_title': 'Milestone Bonuses',
        'landing.milestone_bonuses_description':
            'Hit milestones (7, 30, 100, 365 days) for extra bonus points on top of multipliers!',
        'landing.example_calculation_title': '📊 Example: 30-Day Streak',
        'landing.base_activity_points': 'Base Activity Points',
        'landing.dedicated_tier': 'Dedicated Tier (30 days)',
        'landing.multiplier': 'Multiplier',
        'landing.milestone_bonus': 'Milestone Bonus',
        'landing.total_points_earned': 'Total Points Earned',
        'landing.global_rankings_title': 'Compete on Global Rankings',
        'landing.global_rankings_description':
            'Like a sports platform, but for health. See where you rank today, this season, and all-time.',
        'landing.today_title': 'Today',
        'landing.today_description': 'Real-time daily rankings',
        'landing.yesterday_title': 'Yesterday',
        'landing.yesterday_description': 'Previous day champions',
        'landing.year_title': 'Year',
        'landing.year_description': 'Annual champions',
        'landing.todays_top3_title': "Today's Top 3",
        'landing.how_iomeh_works_title': 'How IOMeH Works',
        'landing.how_iomeh_works_description':
            'Simple 4-step process to start competing',
        'landing.step1_title': 'Sign Up & Select Activities',
        'landing.step1_description':
            'Choose 5-10 activities you want to track from many options across all health categories.',
        'landing.step2_title': "Log Today's Activities",
        'landing.step2_description':
            "Record today's activities in real-time—workouts and nutrition. Add notes and optional proof.",
        'landing.step3_title': 'Earn Points & Build Streaks',
        'landing.step3_description':
            'Get 10-50 points per activity. Build daily streaks. Watch your stats grow.',
        'landing.step4_description':
            'Compete on daily, seasonal, and yearly rankings. Track your rank: "2025 Q1 #11".',
        'landing.ready_to_compete_title': 'Ready to Compete in Wellness?',
        'landing.ready_to_compete_description':
            'Join IOMeH today and turn "I should prioritize health" into "I compete in health." Track, compete, and earn your rank.',
        'landing.start_journey': 'Start Your Journey',

        // Streak tier names
        'landing.tier_newcomer': 'Newcomer',
        'landing.tier_beginner': 'Beginner',
        'landing.tier_regular': 'Regular',
        'landing.tier_committed': 'Committed',
        'landing.tier_dedicated': 'Dedicated',
        'landing.tier_expert': 'Expert',
        'landing.tier_master': 'Master',
        'landing.tier_legend': 'Legend',
        'landing.days_1_2': 'Days 1-2',
        'landing.days_3_6': 'Days 3-6',
        'landing.days_7_13': 'Days 7-13',
        'landing.days_14_29': 'Days 14-29',
        'landing.days_30_59': 'Days 30-59',
        'landing.days_60_99': 'Days 60-99',
        'landing.days_100_199': 'Days 100-199',
        'landing.days_200_plus': 'Days 200+',

        // Home Page
        'home.welcome': 'Welcome back',
        'home.description':
            'Track your health, earn points, and climb the rankings',
        'home.activities_description': "Activities you're tracking",
        'home.today_activities': "Today's Activities",
        'home.recent_activities': 'Recent Activities',
        'home.add_activity': 'Add Activity',
        'home.log_activity': 'Log Activity',
        'home.no_activities_today': 'No activities logged today',
        'home.no_recent_activities': 'No recent activities',
        'home.no_habits_selected': 'No habits selected yet',
        'home.select_activities': 'Select Activities',
        'home.start_tracking': 'Start tracking your health!',
        'home.edit_activities': 'Edit Activities',
        'home.your_streak': 'Your Streak',
        'home.in_a_row': 'in a row',
        'home.multiplier': 'multiplier',
        'home.best': 'Best',
        'home.your_rankings': 'Your Rankings',
        'home.year': 'Year',
        'home.view_all_rankings': 'View All Rankings',
        'home.last_7_days': 'Last 7 days',
        'home.personal_notes': 'Personal notes for this activity',
        'home.activities_count': 'activities',
        'home.points_count': 'points',
        'home.day': 'day',
        'home.days': 'days',
        'home.legend_status_achieved': 'Legend status achieved!',
        'home.days_to': 'days to',
        // Streak Tiers
        'streak.newcomer': 'Newcomer',
        'streak.beginner': 'Beginner',
        'streak.regular': 'Regular',
        'streak.committed': 'Committed',
        'streak.dedicated': 'Dedicated',
        'streak.expert': 'Expert',
        'streak.master': 'Master',
        'streak.legend': 'Legend',

        // Settings
        'settings.title': 'Settings',
        'settings.description': 'Manage your profile and account settings',
        'settings.profile': 'Profile',
        'settings.habits': 'My Habits',
        'settings.password': 'Password',
        'settings.email': 'Email',
        'settings.appearance': 'Appearance',
        'settings.account': 'Account',
        'settings.language': 'Language',

        // Appearance
        'appearance.description':
            'Customize your interface appearance and language preferences.',
        'appearance.theme': 'Theme',
        'appearance.theme_description': 'Choose how the interface looks.',
        'appearance.language_description':
            'Select your preferred language for the interface.',
        'appearance.current_language': 'Current Language',
        'appearance.currently_using': 'Currently using',
        'appearance.language_help_text':
            'The language will be automatically detected based on your location, but you can always change it here. Your preference will be saved and applied across all devices.',
        'appearance.light': 'Light',
        'appearance.dark': 'Dark',
        'appearance.system': 'System',

        // Common
        'common.save': 'Save',
        'common.cancel': 'Cancel',
        'common.edit': 'Edit',
        'common.delete': 'Delete',
        'common.add': 'Add',
        'common.close': 'Close',
        'common.loading': 'Loading...',
        'common.error': 'Error',
        'common.success': 'Success',
        'common.confirm': 'Confirm',
        'common.yes': 'Yes',
        'common.no': 'No',

        // Auth
        'auth.login_description': 'Enter your credentials below to log in',
        'auth.create_account': 'Create an account',
        'auth.create_account_description':
            'Enter your details below to create your account',
        'auth.email_or_username': 'Email address or username',
        'auth.email_address': 'Email address',
        'auth.password': 'Password',
        'auth.confirm_password': 'Confirm password',
        'auth.remember_me': 'Remember me',
        'auth.forgot_password': 'Forgot password?',
        'auth.dont_have_account': "Don't have an account?",
        'auth.already_have_account': 'Already have an account?',
        'auth.continue_with_google': 'Continue with Google',
        'auth.sign_up_with_google': 'Sign up with Google',
        'auth.or_continue_with': 'Or continue with',
        'auth.enter_email_or_username': 'Enter your email or username',
        'auth.enter_password': 'Enter your password',
        'auth.choose_username': 'Choose a username',
        'auth.enter_name': 'Enter your name',
        'auth.enter_email': 'Enter your email',
        'auth.create_password': 'Create a password',
        'auth.confirm_password_placeholder': 'Confirm your password',

        // Validation Errors
        'validation.required': 'This field is required.',
        'validation.email': 'Please enter a valid email address.',
        'validation.min.string': 'This field must be at least :min characters.',
        'validation.max.string': 'This field must not exceed :max characters.',
        'validation.confirmed': 'The confirmation does not match.',
        'validation.unique': 'This value has already been taken.',
        'validation.url': 'Please enter a valid URL.',
        'validation.regex': 'This field format is invalid.',
        'validation.integer': 'This field must be a number.',
        'validation.in': 'The selected value is invalid.',

        // Custom Validation Messages
        'validation.username.min':
            'Username must be at least 3 characters long.',
        'validation.username.regex':
            'Username can only contain letters, numbers, and underscores.',
        'validation.username.unique': 'The username has already been taken.',
        'validation.name.min': 'Name must be at least 2 characters long.',
        'validation.name.max': 'Name must not exceed 100 characters.',
        'validation.email.required': 'Email address is required.',
        'validation.email.valid': 'Please enter a valid email address.',
        'validation.email.unique': 'The email has already been taken.',
        'validation.password.required': 'Password is required.',
        'validation.password.min':
            'Password must be at least 8 characters long.',
        'validation.password.confirmed': 'Passwords do not match.',
        'validation.password_confirmation.required':
            'Please confirm your password.',
        'validation.bio.max': 'Bio must be 255 characters or less.',
        'validation.website_url.url':
            'Please enter a valid URL (e.g., https://example.com).',
        'validation.avatar.url':
            'Please enter a valid URL for your profile picture.',
        'validation.email_or_username.required':
            'Email address or username is required.',
        'validation.auth.failed': 'These credentials do not match our records.',
        'validation.auth.throttle':
            'Too many login attempts. Please try again in :minutes minutes.',
        'validation.habit_name.required': 'Name is required.',
        'validation.habit_name.max': 'Name must be 100 characters or less.',
        'validation.habit_notes.max': 'Notes must be 500 characters or less.',

        // Activity Errors
        'activity.already_logged_today':
            'You have already logged this activity for today.',

        // Social Auth Errors
        'social.unable_retrieve_email':
            'Unable to retrieve email from Google. Please try again.',
        'social.account_exists_login':
            'An account with this Google email already exists. Please log in instead.',
        'social.no_account_found':
            'No account found with that Google email. Please sign up first or try a different account.',
        'social.session_expired':
            'Authentication session expired or invalid. Please try :action again with Google.',
        'social.auth_failed':
            'Authentication failed. Please check your Google account and try again.',
        'social.unable_login':
            'Unable to login with Google. Please try again or contact support.',
        'social.provider_not_supported': 'Provider not supported.',
        'social.account_already_linked':
            'This Google account is already linked to another user.',
        'social.unable_link':
            'Unable to link Google account. Please try again.',
        'social.cannot_unlink_only_method':
            'Cannot unlink your only login method. Please set a password first.',
        'social.no_linked_account':
            'No linked account found for this provider.',

        // Profile Errors
        'profile.not_found': 'Profile Not Found',
        'profile.not_found_message':
            "The profile you're looking for does not exist.",

        // Success Messages
        'success.activity_logged': 'Activity logged successfully!',
        'success.activity_updated': 'Activity updated successfully!',
        'success.activity_deleted': 'Activity deleted successfully!',
        'success.habits_updated': 'Habits updated successfully!',
        'success.social_linked': 'Google account linked successfully.',
        'success.social_unlinked': 'Account unlinked successfully.',

        // Profile Settings
        'profile_settings.title': 'Profile Settings',
        'profile_settings.description':
            "Update your account's profile information",
        'profile.description': "Update your account's profile information",
        'profile.username': 'Username',
        'profile.name': 'Name',
        'profile.personal_website': 'Personal Website',
        'profile.week_start_day': 'Week Start Day',
        'profile.profile_picture_url': 'Profile Picture URL',
        'profile.enter_website_url': 'Enter your website URL',
        'profile.enter_profile_picture_url':
            'Enter URL for your profile picture',
        'profile.enter_direct_url':
            'Enter a direct URL to your profile picture',
        'profile.heatmap_weeks_description':
            'This affects how your activity heatmap displays weeks',
        'profile.bio': 'Bio',
        'profile.website': 'Website',
        'profile.week_starts_on': 'Week starts on',
        'profile.save': 'Save Changes',
        'profile.cancel': 'Cancel',
        'profile.choose_username_placeholder': 'Choose your username',
        'profile.enter_name_placeholder': 'Enter your name',
        'profile.bio_placeholder':
            'Share a little about yourself and your interests...',
        'profile.website_placeholder': 'Enter your website URL',
        'profile.avatar_placeholder': 'Enter URL for your profile picture',
        'profile.avatar_help_text':
            'Enter a direct URL to your profile picture',
        'profile.heatmap_description':
            'This affects how your activity heatmap displays weeks',
        'profile.saturday': 'Saturday',
        'profile.sunday': 'Sunday',
        'profile.monday': 'Monday',
        'profile.saved': 'Saved.',
        'profile.characters_count': 'characters',
        'profile.title': 'Profile',
        'profile.your_achievements': 'Your achievements across seasons',
        'profile.achievements': 'Achievements across seasons',
        'profile.edit_profile': 'Edit Profile',
        'profile.ranking_badges': 'Ranking Badges',
        'profile.activity_streak': 'Activity Streak',
        'profile.health_journey': 'Your health journey - every day counts!',
        'profile.longest_streak': 'Longest Streak',
        'profile.best_consecutive_record': 'Your best consecutive day record',
        'profile.pts': 'pts',

        // Habits
        'habits.title': 'My Habits',
        'habits.description': 'Manage your fitness activities and habits',
        'habits.current_habits': 'Current Habits',
        'habits.maximum_habits_allowed': 'Maximum of 15 habits allowed',
        'habits.add_activities_description':
            'Add the activities that matter most to your health journey.',
        'habits.no_habits_added':
            'No habits added yet. Add your first habit to start tracking activities!',
        'habits.add_activity': 'Add Activity',
        'habits.youve_added_all': "You've added all available activity types!",
        'habits.maximum_reached':
            'Maximum of 15 habits reached. Remove some habits to add new ones.',
        'habits.reset': 'Reset',
        'habits.saved_successfully': 'Habits updated successfully!',
        'habits.add_new_activity': 'Add New Activity',
        'habits.select_activity_type':
            'Select an activity type to add to your activities',
        'habits.all': 'All',
        'habits.no_available_types':
            'No available activity types in this category',
        'habits.points': 'pts',
        'habits.custom_name': 'Name',
        'habits.notes_optional': 'Notes (optional)',
        'habits.notes_placeholder': 'Add any personal notes...',
        'habits.example_name': 'e.g., Morning Workout',
        'habits.workout': 'Workout',
        'habits.nutrition': 'Nutrition',
        'habits.change_icon': 'Change icon',
        'habits.choose_icon': 'Choose Icon',
        'habits.popular': 'Popular',
        'habits.fitness': 'Fitness',
        'habits.food_health': 'Food & Health',
        'habits.custom': 'Custom',
        'habits.enter_emoji': 'Enter emoji...',
        'habits.add': 'Add',

        // Rankings
        'rankings.title': 'Rankings',
        'rankings.today': 'Today',
        'rankings.yesterday': 'Yesterday',
        'rankings.season': 'Season',
        'rankings.year': 'Year',
        'rankings.your_rankings': 'Your Rankings',
        'rankings.see_where_stand':
            'See where you stand across all leaderboards',
        'rankings.todays_leaders': "Today's Leaders",
        'rankings.real_time_rankings': 'Real-time rankings for',
        'rankings.yesterdays_champions': "Yesterday's Champions",
        'rankings.final_rankings': 'Final rankings for',
        'rankings.quarterly_leaderboard':
            'Quarterly leaderboard for the current season',
        'rankings.yearly_leaderboard':
            'Yearly leaderboard - the ultimate test of consistency',
        'rankings.no_activities_today':
            'No activities logged today yet. Be the first!',
        'rankings.no_activities_yesterday': 'No activities logged yesterday',
        'rankings.no_season_rankings':
            'No season rankings yet. Start logging activities!',
        'rankings.no_yearly_rankings': 'No yearly rankings yet',
        'rankings.view_all_rankings': 'View All Rankings',
        'rankings.description':
            'Compete with athletes worldwide across different time periods',
        'rankings.your_rankings_description':
            'See where you stand across all leaderboards',
        'rankings.todays_leaders_description': 'Real-time rankings for',
        'rankings.yesterdays_champions_description': 'Final rankings for',
        'rankings.season_description':
            'Quarterly leaderboard for the current season',
        'rankings.year_description':
            'Yearly leaderboard - the ultimate test of consistency',
        'rankings.no_activities_yet': 'No activities yet',
        'rankings.you': 'You',
        'rankings.activity': 'activity',
        'rankings.activities': 'activities',
        'rankings.rankings': 'Rankings',
        'rankings.annual_rankings': 'Annual Rankings',
        'rankings.season_rankings': 'Season Rankings',
        'rankings.q4_rankings': 'Q4 Rankings',

        // Password Settings
        'password.update_password': 'Update password',
        'password.update_description':
            'Ensure your account is using a long, random password to stay secure',
        'password.current_password': 'Current password',
        'password.new_password': 'New password',
        'password.confirm_password': 'Confirm password',
        'password.enter_current_password': 'Enter current password',
        'password.create_new_password': 'Create a new password',
        'password.confirm_new_password': 'Confirm your new password',
        'password.save_password': 'Save password',
        'password.saved': 'Saved.',

        // Email Settings
        'email.update_email': 'Update email address',
        'email.update_description':
            "Change your email address. You'll need to verify the new email address.",
        'email.new_email': 'New email address',
        'email.confirm_email': 'Confirm email address',
        'email.enter_new_email': 'Enter new email address',
        'email.confirm_new_email': 'Confirm new email address',
        'email.save_email': 'Save email',
        'email.saved': 'Saved.',

        // Account Settings
        'account.actions': 'Account actions',
        'account.actions_description':
            'Manage your account session and security',
        'account.sign_out': 'Sign out',
        'account.sign_out_description':
            'Sign out of your account on this device',

        // Error Pages
        'errors.go_home': 'Go Home',
        'errors.refresh_page': 'Refresh Page',
        'errors.login': 'Login',
        'errors.session_expired': 'Session Expired',
        'errors.session_expired_message':
            'Your session has expired. Please refresh the page to continue.',
        'errors.authentication_required': 'Authentication Required',
        'errors.authentication_required_message':
            'You need to be logged in to access this page.',

        // Activity View
        'activity.loading': 'Loading activities...',
        'activity.view_memory': 'View memory',
        'activity.no_activities': 'No activities found for this date.',

        // Days pluralization
        'days.single': 'day',
        'days.plural': 'days',

        // Modal - Log Activity
        'modal.log_activity.title': 'Log Activity',
        'modal.log_activity.description': 'Record your activity for today',
        'modal.log_activity.activity_type': 'Activity Type',
        'modal.log_activity.select_activity': 'Select an activity',
        'modal.log_activity.notes': 'Notes',
        'modal.log_activity.notes_placeholder':
            'Add any notes about this activity...',
        'modal.log_activity.memory_url': 'Memory URL',
        'modal.log_activity.memory_url_placeholder':
            'https://example.com/photo.jpg',
        'modal.log_activity.memory_url_help':
            'Optional: Share a photo or link to remember this moment',
        'modal.log_activity.cancel': 'Cancel',
        'modal.log_activity.log_activity': 'Log Activity',
        'modal.log_activity.logging': 'Logging...',
        'modal.log_activity.no_activities_subtitle':
            'All activities logged for today!',
        'modal.log_activity.logging_for_today': 'Logging activity for',
        'modal.log_activity.timezone_note': 'Platform timezone is Cairo time',
        'modal.log_activity.today': 'today',
        'modal.log_activity.points_youll_earn': "Points You'll Earn",
        'modal.log_activity.pts': 'pts',
        'modal.log_activity.streak_bonus_applied': 'day streak bonus applied!',
        'modal.log_activity.base_points_info':
            'Base points • Build a streak to multiply your rewards',

        // Edit Activity Modal
        'modal.edit_activity.title': 'Edit Activity',
        'modal.edit_activity.description': 'Update your activity details',
        'modal.edit_activity.notes': 'Notes (Optional)',
        'modal.edit_activity.notes_placeholder':
            'Add any notes about this activity...',
        'modal.edit_activity.memory_url': 'Memory URL (Optional)',
        'modal.edit_activity.memory_url_placeholder': 'https://...',
        'modal.edit_activity.cancel': 'Cancel',
        'modal.edit_activity.saving': 'Saving...',
        'modal.edit_activity.save': 'Save Changes',
    },
    ar: {
        // Navigation
        'nav.home': 'الرئيسية',
        'nav.rankings': 'التصنيفات',
        'nav.profile': 'الملف الشخصي',
        'nav.settings': 'الإعدادات',
        'nav.logout': 'تسجيل الخروج',
        'nav.login': 'تسجيل الدخول',
        'nav.register': 'إنشاء حساب',
        'nav.view_profile': 'عرض الملف الشخصي',

        // Landing Page
        'landing.title': 'منصة التصنيف العالمي لللياقة البدنية',
        'landing.subtitle':
            'IOMeH (أنا مدين لنفسي بالصحة) هي منصتك الشخصية لتصنيف اللياقة البدنية. تتبع تمارينك وأنشطة التغذية وتنافس في التصنيفات العالمية. حول رحلتك الصحية إلى منافسة مثيرة!',
        'landing.badge': 'ترتيبك الصحي العالمي — يتشكل من عاداتك اليومية',
        'landing.hero_title': 'تتبع صحتك، احصل على ترتيبك',
        'landing.get_started': 'ابدأ الآن',
        'landing.learn_more': 'اعرف المزيد',
        'landing.stats.active_users': 'المستخدمون النشطون',
        'landing.stats.total_activities': 'إجمالي الأنشطة',
        'landing.stats.combined_streak': 'السلسلة المجمعة',
        'landing.progress_text': 'تتبع تقدمك عبر المواسم',
        'landing.community_title': 'انضم إلى مجتمع الصحة العالمي',
        'landing.season_label': 'الموسم (Q1-Q4)',
        'landing.rank_example': 'تتبع ترتيبك: "2025 الربع الأول #11".',
        'landing.quarterly_rank': 'ترتيب ربع سنوي',
        'landing.climb_rankings': 'تسلق التصنيفات',
        'landing.compete_description': 'تنافس يومياً وموسمياً وسنوياً.',

        // Quarters
        'quarter.q1': 'الربع الأول',
        'quarter.q2': 'الربع الثاني',
        'quarter.q3': 'الربع الثالث',
        'quarter.q4': 'الربع الرابع',

        // Points
        'points.short': 'نقطة',
        'points.long': 'نقاط',

        // Achievement badges
        'badge.quarter_rank': '2025 الربع الرابع #7',
        'badge.year_rank': '2025 #15',
        'badge.streak': 'سلسلة 14 يوم',

        // Additional landing page sections
        'landing.fitness_journey_title': 'تتبع رحلة اللياقة البدنية',
        'landing.fitness_journey_description':
            'ركز على ما يهم حقاً لأهدافك الصحية. IOMeH يتتبع تمارينك وتغذيتك مع مجموعة واسعة من الأنشطة للاختيار من بينها.',
        'landing.workout_title': '💪 التمرين',
        'landing.workout_description':
            'الجري، الجيم، التدريب المتقطع، السباحة، ركوب الدراجات، الرياضة، الفنون القتالية، وأكثر. النقاط تُحسب حسب الشدة والمدة الزمنية.',
        'landing.nutrition_title': '🥗 التغذية',
        'landing.nutrition_description':
            'الوجبات الصحية، الخضروات، الفواكه، الترطيب، أهداف البروتين، المكملات الغذائية، والطبخ المنزلي.',
        'landing.workout_stats': '30 نشاط | 10-60 نقطة',
        'landing.nutrition_stats': '15 نشاط | 10-30 نقطة',
        'landing.streak_bonus_title': '🔥 نظام مكافآت السلسلة',
        'landing.streak_bonus_description':
            'الاستمرارية هي المفتاح. ابني سلاسل يومية واضرب نقاطك بمكافآت متدرجة تصل إلى',
        'landing.how_it_works_title': 'كيف يعمل',
        'landing.build_streaks_title': 'ابني السلاسل',
        'landing.build_streaks_description':
            'سجل أنشطتك يومياً للحفاظ على سلسلتك. تفويت يوم واحد وستعود إلى 1.',
        'landing.multiply_points_title': 'اضرب النقاط',
        'landing.multiply_points_description':
            'مستوى سلسلتك يضاعف جميع نقاط أنشطتك.',
        'landing.milestone_bonuses_title': 'مكافآت المعالم',
        'landing.milestone_bonuses_description':
            'احقق المعالم (7، 30، 100، 365 يوم) للحصول على نقاط مكافأة إضافية فوق المضاعفات!',
        'landing.example_calculation_title': '📊 مثال: سلسلة 30 يوم',
        'landing.base_activity_points': 'نقاط النشاط الأساسية',
        'landing.dedicated_tier': 'مستوى المتفانين (30 يوم)',
        'landing.multiplier': 'مضاعف',
        'landing.milestone_bonus': 'مكافأة المعلم',
        'landing.total_points_earned': 'إجمالي النقاط المكتسبة',
        'landing.global_rankings_title': 'تنافس في التصنيفات العالمية',
        'landing.global_rankings_description':
            'مثل منصة رياضية، ولكن للصحة. شاهد أين ترتيبك اليوم، هذا الموسم، وعلى الإطلاق.',
        'landing.today_title': 'اليوم',
        'landing.today_description': 'تصنيفات يومية في الوقت الفعلي',
        'landing.yesterday_title': 'أمس',
        'landing.yesterday_description': 'أبطال اليوم السابق',
        'landing.year_title': 'السنة',
        'landing.year_description': 'أبطال سنويون',
        'landing.todays_top3_title': 'أفضل 3 اليوم',
        'landing.how_iomeh_works_title': 'كيف يعمل IOMeH',
        'landing.how_iomeh_works_description':
            'عملية بسيطة من 4 خطوات للبدء في التنافس',
        'landing.step1_title': 'سجل واختر الأنشطة',
        'landing.step1_description':
            'اختر 5-10 أنشطة تريد تتبعها من العديد من الخيارات عبر جميع فئات الصحة.',
        'landing.step2_title': 'سجل أنشطة اليوم',
        'landing.step2_description':
            'سجل أنشطة اليوم في الوقت الفعلي—التمارين والتغذية. أضف ملاحظات وإثبات اختياري.',
        'landing.step3_title': 'اكسب النقاط وابني السلاسل',
        'landing.step3_description':
            'احصل على 10-50 نقطة لكل نشاط. ابني سلاسل يومية. شاهد إحصائياتك تنمو.',
        'landing.step4_description':
            'تنافس في التصنيفات اليومية والموسمية والسنوية. تتبع ترتيبك: "2025 الربع الأول #11".',
        'landing.ready_to_compete_title': 'مستعد للتنافس في الرفاهية؟',
        'landing.ready_to_compete_description':
            'انضم إلى IOMeH اليوم، وحوّل "يجب أن أولي الأولوية للصحة" إلى "أُنافِس في  الصحة". تتبع، تنافس، واكتسب ترتيبك.',
        'landing.start_journey': 'ابدأ رحلتك',

        // Streak tier names
        'landing.tier_newcomer': 'وافد جديد',
        'landing.tier_beginner': 'مبتدئ',
        'landing.tier_regular': 'منتظم',
        'landing.tier_committed': 'ملتزم',
        'landing.tier_dedicated': 'متفان',
        'landing.tier_expert': 'خبير',
        'landing.tier_master': 'سيد',
        'landing.tier_legend': 'أسطورة',
        'landing.days_1_2': 'الأيام 1-2',
        'landing.days_3_6': 'الأيام 3-6',
        'landing.days_7_13': 'الأيام 7-13',
        'landing.days_14_29': 'الأيام 14-29',
        'landing.days_30_59': 'الأيام 30-59',
        'landing.days_60_99': 'الأيام 60-99',
        'landing.days_100_199': 'الأيام 100-199',
        'landing.days_200_plus': 'الأيام 200+',

        // Home Page
        'home.welcome': 'مرحباً بعودتك',
        'home.description': 'تتبع صحتك، اكسب النقاط، وتسلق التصنيفات',
        'home.activities_description': 'الأنشطة التي تتابعها',
        'home.today_activities': 'أنشطة اليوم',
        'home.recent_activities': 'الأنشطة الأخيرة',
        'home.add_activity': 'إضافة نشاط',
        'home.log_activity': 'تسجيل نشاط',
        'home.no_activities_today': 'لم يتم تسجيل أي أنشطة اليوم',
        'home.no_recent_activities': 'لا توجد أنشطة حديثة',
        'home.no_habits_selected': 'لم يتم اختيار عادات بعد',
        'home.select_activities': 'اختر الأنشطة',
        'home.start_tracking': 'ابدأ تتبع صحتك!',
        'home.edit_activities': 'تعديل الأنشطة',
        'home.your_streak': 'سلسلتك',
        'home.in_a_row': 'على التوالي',
        'home.multiplier': 'مضاعف',
        'home.best': 'الأفضل',
        'home.your_rankings': 'تصنيفاتك',
        'home.year': 'سنة',
        'home.view_all_rankings': 'عرض جميع التصنيفات',
        'home.last_7_days': 'آخر 7 أيام',
        'home.personal_notes': 'ملاحظات شخصية لهذا النشاط',
        'home.activities_count': 'أنشطة',
        'home.points_count': 'نقاط',
        'home.day': 'يوم',
        'home.days': 'أيام',
        'home.legend_status_achieved': 'تم تحقيق حالة الأسطورة!',
        'home.days_to': 'أيام للوصول إلى',
        // Streak Tiers
        'streak.newcomer': 'وافد جديد',
        'streak.beginner': 'مبتدئ',
        'streak.regular': 'منتظم',
        'streak.committed': 'ملتزم',
        'streak.dedicated': 'متفان',
        'streak.expert': 'خبير',
        'streak.master': 'سيد',
        'streak.legend': 'أسطورة',

        // Settings
        'settings.title': 'الإعدادات',
        'settings.description': 'إدارة ملفك الشخصي وإعدادات الحساب',
        'settings.profile': 'الملف الشخصي',
        'settings.habits': 'عاداتي',
        'settings.password': 'كلمة المرور',
        'settings.email': 'البريد الإلكتروني',
        'settings.appearance': 'المظهر',
        'settings.account': 'الحساب',
        'settings.language': 'اللغة',

        // Appearance
        'appearance.description': 'خصص مظهر واجهتك وتفضيلات اللغة.',
        'appearance.theme': 'المظهر',
        'appearance.theme_description': 'اختر كيف تبدو الواجهة.',
        'appearance.language_description': 'اختر لغتك المفضلة للواجهة.',
        'appearance.current_language': 'اللغة الحالية',
        'appearance.currently_using': 'تستخدم حالياً',
        'appearance.language_help_text':
            'سيتم اكتشاف اللغة تلقائياً بناءً على موقعك، ولكن يمكنك دائماً تغييرها هنا. سيتم حفظ تفضيلك وتطبيقه عبر جميع الأجهزة.',
        'appearance.light': 'فاتح',
        'appearance.dark': 'داكن',
        'appearance.system': 'النظام',

        // Common
        'common.save': 'حفظ',
        'common.cancel': 'إلغاء',
        'common.edit': 'تعديل',
        'common.delete': 'حذف',
        'common.add': 'إضافة',
        'common.close': 'إغلاق',
        'common.loading': 'جاري التحميل...',
        'common.error': 'خطأ',
        'common.success': 'نجح',
        'common.confirm': 'تأكيد',
        'common.yes': 'نعم',
        'common.no': 'لا',

        // Auth
        'auth.login_description': 'أدخل بيانات اعتمادك أدناه لتسجيل الدخول',
        'auth.create_account': 'إنشاء حساب',
        'auth.create_account_description': 'أدخل تفاصيلك أدناه لإنشاء حسابك',
        'auth.email_or_username': 'البريد الإلكتروني أو اسم المستخدم',
        'auth.email_address': 'عنوان البريد الإلكتروني',
        'auth.password': 'كلمة المرور',
        'auth.confirm_password': 'تأكيد كلمة المرور',
        'auth.remember_me': 'تذكرني',
        'auth.forgot_password': 'نسيت كلمة المرور؟',
        'auth.dont_have_account': 'ليس لديك حساب؟',
        'auth.already_have_account': 'لديك حساب بالفعل؟',
        'auth.continue_with_google': 'المتابعة مع جوجل',
        'auth.sign_up_with_google': 'التسجيل مع جوجل',
        'auth.or_continue_with': 'أو المتابعة مع',
        'auth.enter_email_or_username': 'أدخل بريدك الإلكتروني أو اسم المستخدم',
        'auth.enter_password': 'أدخل كلمة المرور',
        'auth.choose_username': 'اختر اسم مستخدم',
        'auth.enter_name': 'أدخل اسمك',
        'auth.enter_email': 'أدخل بريدك الإلكتروني',
        'auth.create_password': 'إنشاء كلمة مرور',
        'auth.confirm_password_placeholder': 'أكد كلمة المرور',

        // Validation Errors
        'validation.required': 'هذا الحقل مطلوب.',
        'validation.email': 'يرجى إدخال عنوان بريد إلكتروني صحيح.',
        'validation.min.string': 'يجب أن يكون هذا الحقل على الأقل :min أحرف.',
        'validation.max.string': 'يجب ألا يتجاوز هذا الحقل :max حرف.',
        'validation.confirmed': 'التأكيد غير متطابق.',
        'validation.unique': 'هذه القيمة مستخدمة بالفعل.',
        'validation.url': 'يرجى إدخال رابط صحيح.',
        'validation.regex': 'تنسيق هذا الحقل غير صحيح.',
        'validation.integer': 'يجب أن يكون هذا الحقل رقماً.',
        'validation.in': 'القيمة المحددة غير صحيحة.',

        // Custom Validation Messages
        'validation.username.min': 'يجب أن يكون اسم المستخدم 3 أحرف على الأقل.',
        'validation.username.regex':
            'يمكن أن يحتوي اسم المستخدم على أحرف وأرقام وشرطات سفلية فقط.',
        'validation.username.unique': 'اسم المستخدم مستخدم بالفعل.',
        'validation.name.min': 'يجب أن يكون الاسم حرفين على الأقل.',
        'validation.name.max': 'يجب ألا يتجاوز الاسم 100 حرف.',
        'validation.email.required': 'عنوان البريد الإلكتروني مطلوب.',
        'validation.email.valid': 'يرجى إدخال عنوان بريد إلكتروني صحيح.',
        'validation.email.unique': 'البريد الإلكتروني مستخدم بالفعل.',
        'validation.password.required': 'كلمة المرور مطلوبة.',
        'validation.password.min': 'يجب أن تكون كلمة المرور 8 أحرف على الأقل.',
        'validation.password.confirmed': 'كلمات المرور غير متطابقة.',
        'validation.password_confirmation.required': 'يرجى تأكيد كلمة المرور.',
        'validation.bio.max': 'يجب ألا يتجاوز السيرة الذاتية 255 حرف.',
        'validation.website_url.url':
            'يرجى إدخال رابط صحيح (مثل https://example.com).',
        'validation.avatar.url': 'يرجى إدخال رابط صحيح لصورة ملفك الشخصي.',
        'validation.email_or_username.required':
            'عنوان البريد الإلكتروني أو اسم المستخدم مطلوب.',
        'validation.auth.failed': 'هذه البيانات لا تطابق سجلاتنا.',
        'validation.auth.throttle':
            'محاولات تسجيل دخول كثيرة جداً. يرجى المحاولة مرة أخرى خلال :minutes دقائق.',
        'validation.habit_name.required': 'الاسم مطلوب.',
        'validation.habit_name.max': 'يجب ألا يتجاوز الاسم 100 حرف.',
        'validation.habit_notes.max': 'يجب ألا تتجاوز الملاحظات 500 حرف.',

        // Activity Errors
        'activity.already_logged_today': 'لقد سجلت هذا النشاط اليوم بالفعل.',

        // Social Auth Errors
        'social.unable_retrieve_email':
            'غير قادر على استرداد البريد الإلكتروني من جوجل. يرجى المحاولة مرة أخرى.',
        'social.account_exists_login':
            'يوجد حساب بالفعل بهذا البريد الإلكتروني من جوجل. يرجى تسجيل الدخول بدلاً من ذلك.',
        'social.no_account_found':
            'لم يتم العثور على حساب بهذا البريد الإلكتروني من جوجل. يرجى التسجيل أولاً أو تجربة حساب مختلف.',
        'social.session_expired':
            'انتهت صلاحية جلسة المصادقة أو أنها غير صالحة. يرجى المحاولة مرة أخرى مع جوجل.',
        'social.auth_failed':
            'فشلت المصادقة. يرجى التحقق من حساب جوجل والمحاولة مرة أخرى.',
        'social.unable_login':
            'غير قادر على تسجيل الدخول مع جوجل. يرجى المحاولة مرة أخرى أو الاتصال بالدعم.',
        'social.provider_not_supported': 'مزود الخدمة غير مدعوم.',
        'social.account_already_linked':
            'حساب جوجل هذا مربوط بالفعل بمستخدم آخر.',
        'social.unable_link':
            'غير قادر على ربط حساب جوجل. يرجى المحاولة مرة أخرى.',
        'social.cannot_unlink_only_method':
            'لا يمكن إلغاء ربط طريقة تسجيل الدخول الوحيدة. يرجى تعيين كلمة مرور أولاً.',
        'social.no_linked_account': 'لم يتم العثور على حساب مربوط لهذا المزود.',

        // Profile Errors
        'profile.not_found': 'الملف الشخصي غير موجود',
        'profile.not_found_message': 'الملف الشخصي الذي تبحث عنه غير موجود.',

        // Success Messages
        'success.activity_logged': 'تم تسجيل النشاط بنجاح!',
        'success.activity_updated': 'تم تحديث النشاط بنجاح!',
        'success.activity_deleted': 'تم حذف النشاط بنجاح!',
        'success.habits_updated': 'تم تحديث العادات بنجاح!',
        'success.social_linked': 'تم ربط حساب جوجل بنجاح.',
        'success.social_unlinked': 'تم إلغاء ربط الحساب بنجاح.',

        // Profile Settings
        'profile_settings.title': 'إعدادات الملف الشخصي',
        'profile_settings.description': 'تحديث معلومات الملف الشخصي لحسابك',
        'profile.description': 'تحديث معلومات الملف الشخصي لحسابك',
        'profile.username': 'اسم المستخدم',
        'profile.name': 'الاسم',
        'profile.personal_website': 'الموقع الشخصي',
        'profile.week_start_day': 'يوم بداية الأسبوع',
        'profile.profile_picture_url': 'رابط صورة الملف الشخصي',
        'profile.enter_website_url': 'أدخل رابط موقعك',
        'profile.enter_profile_picture_url': 'أدخل رابط صورة ملفك الشخصي',
        'profile.enter_direct_url': 'أدخل رابط مباشر لصورة ملفك الشخصي',
        'profile.heatmap_weeks_description':
            'هذا يؤثر على كيفية عرض خريطة الحرارة للأنشطة للأسبوع',
        'profile.bio': 'السيرة الذاتية',
        'profile.website': 'الموقع الإلكتروني',
        'profile.week_starts_on': 'يبدأ الأسبوع في',
        'profile.save': 'حفظ التغييرات',
        'profile.cancel': 'إلغاء',
        'profile.choose_username_placeholder': 'اختر اسم المستخدم',
        'profile.enter_name_placeholder': 'أدخل اسمك',
        'profile.bio_placeholder': 'شارك قليلاً عن نفسك واهتماماتك...',
        'profile.website_placeholder': 'أدخل رابط موقعك',
        'profile.avatar_placeholder': 'أدخل رابط صورة ملفك الشخصي',
        'profile.avatar_help_text': 'أدخل رابط مباشر لصورة ملفك الشخصي',
        'profile.heatmap_description':
            'هذا يؤثر على كيفية عرض خريطة الحرارة للأنشطة للأسبوع',
        'profile.saturday': 'السبت',
        'profile.sunday': 'الأحد',
        'profile.monday': 'الاثنين',
        'profile.saved': 'تم الحفظ.',
        'profile.characters_count': 'حرف',
        'profile.title': 'الملف الشخصي',
        'profile.your_achievements': 'إنجازاتك عبر المواسم',
        'profile.achievements': 'الإنجازات عبر المواسم',
        'profile.edit_profile': 'تعديل الملف الشخصي',
        'profile.ranking_badges': 'شارات التصنيف',
        'profile.activity_streak': 'سلسلة الأنشطة',
        'profile.health_journey': 'رحلتك الصحية - كل يوم مهم!',
        'profile.longest_streak': 'أطول سلسلة',
        'profile.best_consecutive_record': 'أفضل سجل أيام متتالية لك',
        'profile.pts': 'نقطة',

        // Habits
        'habits.title': 'عاداتي',
        'habits.description': 'إدارة أنشطة اللياقة البدنية والعادات',
        'habits.current_habits': 'العادات الحالية',
        'habits.maximum_habits_allowed': 'الحد الأقصى 15 عادة مسموح',
        'habits.add_activities_description':
            'أضف الأنشطة الأكثر أهمية لرحلتك الصحية.',
        'habits.no_habits_added':
            'لم يتم إضافة عادات بعد. أضف عادتك الأولى لبدء تتبع الأنشطة!',
        'habits.add_activity': 'إضافة نشاط',
        'habits.youve_added_all': 'لقد أضفت جميع أنواع الأنشطة المتاحة!',
        'habits.maximum_reached':
            'تم الوصول للحد الأقصى من العادات. احذف بعض العادات لإضافة عادات جديدة.',
        'habits.reset': 'إعادة تعيين',
        'habits.saved_successfully': 'تم تحديث العادات بنجاح!',
        'habits.add_new_activity': 'إضافة نشاط جديد',
        'habits.select_activity_type': 'اختر نوع النشاط لإضافته إلى أنشطتك',
        'habits.all': 'الكل',
        'habits.no_available_types': 'لا توجد أنواع أنشطة متاحة في هذه الفئة',
        'habits.points': 'نقطة',
        'habits.custom_name': 'الاسم',
        'habits.notes_optional': 'ملاحظات (اختياري)',
        'habits.notes_placeholder': 'أضف أي ملاحظات شخصية...',
        'habits.example_name': 'مثال: تمرين الصباح',
        'habits.workout': 'تمرين',
        'habits.nutrition': 'تغذية',
        'habits.change_icon': 'تغيير الأيقونة',
        'habits.choose_icon': 'اختر الأيقونة',
        'habits.popular': 'شائع',
        'habits.fitness': 'اللياقة البدنية',
        'habits.food_health': 'الطعام والصحة',
        'habits.custom': 'مخصص',
        'habits.enter_emoji': 'أدخل الرمز التعبيري...',
        'habits.add': 'إضافة',

        // Rankings
        'rankings.title': 'التصنيفات',
        'rankings.today': 'اليوم',
        'rankings.yesterday': 'أمس',
        'rankings.season': 'الموسم',
        'rankings.year': 'السنة',
        'rankings.your_rankings': 'تصنيفاتك',
        'rankings.see_where_stand': 'شاهد أين تقف عبر جميع لوحات المتصدرين',
        'rankings.todays_leaders': 'قادة اليوم',
        'rankings.real_time_rankings': 'تصنيفات في الوقت الفعلي لـ',
        'rankings.yesterdays_champions': 'أبطال أمس',
        'rankings.final_rankings': 'التصنيفات النهائية لـ',
        'rankings.quarterly_leaderboard':
            'لوحة المتصدرين الربعية للموسم الحالي',
        'rankings.yearly_leaderboard':
            'لوحة المتصدرين السنوية - الاختبار النهائي للاتساق',
        'rankings.no_activities_today':
            'لم يتم تسجيل أي أنشطة اليوم بعد. كن الأول!',
        'rankings.no_activities_yesterday': 'لم يتم تسجيل أي أنشطة أمس',
        'rankings.no_season_rankings':
            'لا توجد تصنيفات موسمية بعد. ابدأ في تسجيل الأنشطة!',
        'rankings.no_yearly_rankings': 'لا توجد تصنيفات سنوية بعد',
        'rankings.view_all_rankings': 'عرض جميع التصنيفات',
        'rankings.description':
            'تنافس مع الرياضيين حول العالم عبر فترات زمنية مختلفة',
        'rankings.your_rankings_description':
            'شاهد مكانك عبر جميع لوحات المتصدرين',
        'rankings.todays_leaders_description': 'تصنيفات فورية لـ',
        'rankings.yesterdays_champions_description': 'التصنيفات النهائية لـ',
        'rankings.season_description': 'لوحة المتصدرين الربعية للموسم الحالي',
        'rankings.year_description':
            'لوحة المتصدرين السنوية - الاختبار الأقصى للثبات',
        'rankings.no_activities_yet': 'لا توجد أنشطة بعد',
        'rankings.you': 'أنت',
        'rankings.activity': 'نشاط',
        'rankings.activities': 'أنشطة',
        'rankings.rankings': 'التصنيفات',
        'rankings.annual_rankings': 'التصنيفات السنوية',
        'rankings.season_rankings': 'تصنيفات الموسم',
        'rankings.q4_rankings': 'تصنيفات الموسم الرابع',

        // Password Settings
        'password.update_password': 'تحديث كلمة المرور',
        'password.update_description':
            'تأكد من أن حسابك يستخدم كلمة مرور طويلة وعشوائية للبقاء آمناً',
        'password.current_password': 'كلمة المرور الحالية',
        'password.new_password': 'كلمة المرور الجديدة',
        'password.confirm_password': 'تأكيد كلمة المرور',
        'password.enter_current_password': 'أدخل كلمة المرور الحالية',
        'password.create_new_password': 'إنشاء كلمة مرور جديدة',
        'password.confirm_new_password': 'تأكيد كلمة المرور الجديدة',
        'password.save_password': 'حفظ كلمة المرور',
        'password.saved': 'تم الحفظ.',

        // Email Settings
        'email.update_email': 'تحديث عنوان البريد الإلكتروني',
        'email.update_description':
            'غير عنوان بريدك الإلكتروني. ستحتاج إلى التحقق من عنوان البريد الإلكتروني الجديد.',
        'email.new_email': 'عنوان البريد الإلكتروني الجديد',
        'email.confirm_email': 'تأكيد عنوان البريد الإلكتروني',
        'email.enter_new_email': 'أدخل عنوان البريد الإلكتروني الجديد',
        'email.confirm_new_email': 'تأكيد عنوان البريد الإلكتروني الجديد',
        'email.save_email': 'حفظ البريد الإلكتروني',
        'email.saved': 'تم الحفظ.',

        // Account Settings
        'account.actions': 'إجراءات الحساب',
        'account.actions_description': 'إدارة جلسة حسابك وأمانه',
        'account.sign_out': 'تسجيل الخروج',
        'account.sign_out_description': 'تسجيل الخروج من حسابك على هذا الجهاز',

        // Error Pages
        'errors.go_home': 'الذهاب للصفحة الرئيسية',
        'errors.refresh_page': 'تحديث الصفحة',
        'errors.login': 'تسجيل الدخول',
        'errors.session_expired': 'انتهت صلاحية الجلسة',
        'errors.session_expired_message':
            'انتهت صلاحية جلستك. يرجى تحديث الصفحة للمتابعة.',
        'errors.authentication_required': 'مطلوب المصادقة',
        'errors.authentication_required_message':
            'تحتاج إلى تسجيل الدخول للوصول إلى هذه الصفحة.',

        // Activity View
        'activity.loading': 'جاري تحميل الأنشطة...',
        'activity.view_memory': 'عرض الذاكرة',
        'activity.no_activities': 'لم يتم العثور على أنشطة لهذا التاريخ.',

        // Days pluralization
        'days.single': 'يوم',
        'days.plural': 'أيام',

        // Modal - Log Activity
        'modal.log_activity.title': 'تسجيل النشاط',
        'modal.log_activity.description': 'سجل نشاطك لهذا اليوم',
        'modal.log_activity.activity_type': 'نوع النشاط',
        'modal.log_activity.select_activity': 'اختر نشاطاً',
        'modal.log_activity.notes': 'ملاحظات',
        'modal.log_activity.notes_placeholder':
            'أضف أي ملاحظات حول هذا النشاط...',
        'modal.log_activity.memory_url': 'رابط الذاكرة',
        'modal.log_activity.memory_url_placeholder':
            'https://example.com/photo.jpg',
        'modal.log_activity.memory_url_help':
            'اختياري: شارك صورة أو رابط لتذكر هذه اللحظة',
        'modal.log_activity.cancel': 'إلغاء',
        'modal.log_activity.log_activity': 'تسجيل النشاط',
        'modal.log_activity.logging': 'جاري التسجيل...',
        'modal.log_activity.no_activities_subtitle':
            'تم تسجيل جميع الأنشطة لهذا اليوم!',
        'modal.log_activity.logging_for_today': 'تسجيل النشاط لـ',
        'modal.log_activity.timezone_note': 'توقيت المنصة هو توقيت القاهرة',
        'modal.log_activity.today': 'اليوم',
        'modal.log_activity.points_youll_earn': 'النقاط التي ستحصل عليها',
        'modal.log_activity.pts': 'نقطة',
        'modal.log_activity.streak_bonus_applied': 'يوم مكافأة السلسلة مطبقة!',
        'modal.log_activity.base_points_info':
            'النقاط الأساسية • أنشئ سلسلة لضرب مكافآتك',

        // Edit Activity Modal
        'modal.edit_activity.title': 'تعديل النشاط',
        'modal.edit_activity.description': 'تحديث تفاصيل نشاطك',
        'modal.edit_activity.notes': 'ملاحظات (اختياري)',
        'modal.edit_activity.notes_placeholder':
            'أضف أي ملاحظات حول هذا النشاط...',
        'modal.edit_activity.memory_url': 'رابط الذاكرة (اختياري)',
        'modal.edit_activity.memory_url_placeholder': 'https://...',
        'modal.edit_activity.cancel': 'إلغاء',
        'modal.edit_activity.saving': 'جاري الحفظ...',
        'modal.edit_activity.save': 'حفظ التغييرات',
    },
};

// Global reactive locale state
const globalLocale = ref('en');

export function useTranslations(currentLocale?: string) {
    // Update global locale if provided
    if (currentLocale) {
        globalLocale.value = currentLocale;
    }

    const t = (key: string, fallback?: string): string => {
        const locale = globalLocale.value || 'en';
        const localeTranslations =
            translations[locale as keyof typeof translations];
        if (localeTranslations && key in localeTranslations) {
            return localeTranslations[key as keyof typeof localeTranslations];
        }
        return fallback || key;
    };

    const isRTL = computed(() => globalLocale.value === 'ar');

    return {
        t,
        isRTL,
        currentLocale: globalLocale,
    };
}

// Function to update locale globally
export function setLocale(locale: string) {
    globalLocale.value = locale;
}

// Function to get translated quarter name
export function getQuarterName(quarterNumber: number, locale?: string): string {
    const currentLocale = locale || globalLocale.value;

    if (currentLocale === 'ar') {
        const quarterKey =
            `quarter.q${quarterNumber}` as keyof typeof translations.ar;
        return translations.ar[quarterKey] || `Q${quarterNumber}`;
    }

    const quarterKey =
        `quarter.q${quarterNumber}` as keyof typeof translations.en;
    return translations.en[quarterKey] || `Q${quarterNumber}`;
}
