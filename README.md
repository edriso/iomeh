# IOMEH - I Owe Me Health

> **Your Personal Health Ranking Organization**

IOMEH (I Owe Me Health) is a global health tracking and ranking platform that gamifies your wellness journey. Track workouts, nutrition, sleep, and mindfulness activities while competing on global leaderboards—just like a professional sports organization for your health.

## 🎯 The Mission

Transform personal health into an engaging competition where consistency is rewarded, progress is visible, and every healthy choice earns you points toward global rankings.

## ✨ Features

### 📊 Multi-Dimensional Health Tracking
- **🏋️ Workout Activities** — Track 20+ exercise types with MET-based points (running, gym, yoga, HIIT, sports)
- **🥗 Nutrition** — Log healthy meals, hydration, meal prep, and dietary goals
- **😴 Wellness** — Monitor sleep quality, recovery, outdoor time, and lifestyle habits
- **🧘 Mindfulness** — Track meditation, journaling, reading, and mental health practices

### 🏆 Global Rankings System
- **Today's Leaders** — Real-time rankings of today's most active users
- **Yesterday's Champions** — See who dominated yesterday
- **Seasonal Rankings (Q1-Q4)** — Compete within each quarter
- **Annual Rankings** — Year-long competition for top performers
- **Personal Stats** — View your rank like "2025 Q1 #11, 2024 #156"

### 🎮 Gamification
- **Points System** — Earn 10-50 points per activity based on intensity and health impact
- **Streaks** — Build and maintain daily activity streaks
- **Interests** — Customize your tracked activities from 45+ options
- **Proof System** — Optional photo/URL proof for activities

## 🎯 Activity Categories

### 💪 Workout (20 Activities)
High to light intensity exercises with MET-based points:
- **High Intensity** (40-50 pts): Fast Running, HIIT, CrossFit, Swimming, Cycling
- **Moderate** (25-35 pts): Gym/Weights, Tennis, Soccer, Basketball, Martial Arts, Hiking
- **Light-Moderate** (10-20 pts): Yoga, Pilates, Brisk Walking, Stretching

### 🥗 Nutrition (9 Activities)
Healthy eating and hydration tracking (10-25 pts):
- Healthy Breakfast/Lunch/Dinner
- Meal Prep, Fruits & Vegetables
- Protein Goals, Hydration
- No Junk Food, Supplements

### 🌟 Wellness (8 Activities)
Recovery and lifestyle habits (15-30 pts):
- Quality Sleep (7-9 hours)
- Early Wake-Up, Sunlight Exposure
- Nature Time, Cold Shower, Sauna
- No Screen Before Bed, Social Connection

### 🧘 Mindfulness (8 Activities)
Mental health and personal growth (15-25 pts):
- Meditation, Breathing Exercises
- Journaling, Reading
- Prayer/Spiritual Practice
- No Social Media, Learning, Creative Activities

## 🪙 Points System

Points are scientifically based on:
- **MET Values** (Metabolic Equivalent of Task) for workouts
- **Health Impact** for nutrition, wellness, and mindfulness activities
- **Difficulty/Effort** required for each activity

**Example Rankings:**
```
🥇 Today's #1: Sarah — 180 pts (HIIT, Healthy meals, Meditation, Quality sleep)
🥈 Today's #2: Mike — 165 pts (Gym, Running, Meal prep, Reading)
🥉 Today's #3: Alex — 155 pts (Cycling, Yoga, Healthy eating, Journaling)

📈 Your Stats: 2025 Q1 #156 | 2024 #892 | Current Streak: 14 days 🔥
```

## 🏗️ Tech Stack

**Backend:** Laravel 12 + PHP 8.3+ + MySQL/SQLite  
**Frontend:** Vue.js 3 + TypeScript + Tailwind CSS + Inertia.js  
**Testing:** Pest (PHP) + Vitest (TypeScript) + Factory Pattern  
**Database:** Relational schema with optimized indexes for rankings

## 📊 Database Architecture

- **Users** — Points, streaks, and ranking data
- **Activity Types** — 45+ predefined activities with MET values and points
- **Interests** — User's personalized activity selections
- **Activities** — Daily activity logs with points and proof
- **Rankings** — Cached rankings for today/yesterday/season/year

## 🚀 Quick Start

**Prerequisites:** PHP 8.3+, Composer, Node.js 18+, npm

```bash
# 1. Clone and setup
git clone <your-repo-url>
cd iomeh
composer install && npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Setup database with comprehensive seed data
php artisan migrate:fresh --seed

# 4. Start development
php artisan serve
npm run dev  # In another terminal
```

Visit `http://localhost:8000` and start tracking your health!

## 🧪 Test Credentials

```
Email: test@example.com
Password: password
```

## 🔧 Development

```bash
# Run all tests
php artisan test
npm run test:frontend

# Run specific test suites
php artisan test --filter=ActivityTest
npm run test:unit

# Code formatting
npm run format
composer format
```

## 📈 Seeded Data

The seeder creates:
- **45 Activity Types** across 4 categories with realistic points
- **25 Users** with varying activity levels and stats
- **30 Days** of activity history for each user
- **Rankings** for current season and year
- **Interests** (3-7 per user) customized from activity types

## 🎮 Key Features in Development

- [ ] Activity proof verification system
- [ ] Social features (follow users, comment on activities)
- [ ] Achievements and badges
- [ ] Weekly challenges
- [ ] Team competitions
- [ ] Custom activity types
- [ ] API for third-party integrations (Strava, MyFitnessPal, etc.)

## 🚀 Production Deployment

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm run build

# Run migrations and seed activity types
php artisan migrate --force
php artisan db:seed --class=ActivityTypeSeeder

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Make your changes and add tests
4. Run tests: `php artisan test && npm run test:frontend`
5. Submit a pull request

## 📊 Example User Journey

1. **Sign Up** — Create your IOMEH account
2. **Select Interests** — Choose 5-10 activities you want to track
3. **Log Activities** — Record your daily workouts, meals, sleep, etc.
4. **Earn Points** — Get 10-50 points per activity based on difficulty
5. **Build Streaks** — Maintain daily consistency for bonus motivation
6. **Climb Rankings** — Compete on today, season, and yearly leaderboards
7. **Track Progress** — See your rank improve: "2025 Q1 #11, 2024 #892"

## 🌟 Why IOMEH?

Unlike fitness apps focused on calories or steps, IOMEH recognizes that true health is multi-dimensional:
- Physical fitness matters, but so does nutrition
- Sleep and recovery are as important as workouts
- Mental health and mindfulness complete the picture
- Consistency beats intensity every time

**IOMEH turns "I should be healthier" into "I compete in health."**

## 📄 License

MIT License - see [LICENSE](https://opensource.org/licenses/MIT) for details.

---

**Track Your Health. Earn Your Rank. Own Your Wellness.** 💪🏆
