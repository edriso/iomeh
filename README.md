# IOMeW - I Owe Me Wellness

> **Your Global Wellness Rank — Built by Everyday Choices.**

A global wellness tracking and ranking platform that gamifies your wellness journey. Track workouts, nutrition, sleep, and mindfulness activities while competing on global leaderboards.

## ✨ Features

- 🏋️ **Multi-Dimensional Tracking** — 45+ activities across workout, nutrition, wellness, and mindfulness
- 🏆 **Global Rankings** — Real-time daily, seasonal (Q1-Q4), and annual leaderboards
- 🎮 **Points System** — Earn 10-50 points per activity based on MET values and health impact
- 🔥 **Streak Tracking** — Build and maintain daily activity consistency
- 📊 **Historical Stats** — View your rank progression: "2025 Q1 #11, 2024 #156"

## 🪙 Activity Categories

| Category | Activities | Points |
|----------|------------|--------|
| 💪 **Workout** | Running, HIIT, Gym, Swimming, Yoga, Cycling, Sports... (20 types) | 10-50 pts |
| 🥗 **Nutrition** | Healthy Meals, Meal Prep, Hydration, Supplements... (9 types) | 10-25 pts |
| 🌟 **Wellness** | Quality Sleep, Sunlight, Nature Time, Cold Shower... (8 types) | 15-30 pts |
| 🧘 **Mindfulness** | Meditation, Journaling, Reading, Learning... (8 types) | 15-25 pts |

## 🏗️ Tech Stack

- **Backend:** Laravel 12 • PHP 8.2+ • MySQL/SQLite
- **Frontend:** Vue 3 • TypeScript • Tailwind CSS • Inertia.js
- **Testing:** Pest • 57 passing tests
- **Auth:** Laravel Fortify • OAuth (Google, GitHub)

## 🚀 Quick Start

```bash
# Setup
git clone git@github.com:edriso/iomew.git && cd iomew
composer install && npm install
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate:fresh --seed

# Run
php artisan serve
npm run dev  # New terminal
```

Visit `http://localhost:8000`

**Test Login:** `test@example.com` / `password`

## 🧪 Testing

```bash
php artisan test              # Backend tests
npm run test:frontend         # Frontend tests
```

## 📊 Database Architecture

**Core Tables:**
- `users` — Streaks and current season reference
- `activity_types` — 45 predefined activities with points
- `activities` — Daily activity logs with optional proof
- `interests` — User's customized activity selections

**Ranking System:**
- `seasons` — Current year quarterly records (Q1-Q4)
  - Stores season points and year aggregates
  - Ranks calculated dynamically
- `ranking_history` — Historical performance records
  - Immutable past season/year rankings
  - Used for profile badges

**Key Features:**
- ✅ No cron jobs required — updates automatically on activity log
- ✅ Dynamic rank calculation for current seasons
- ✅ Date-aware processing — backdate activities to correct season
- ✅ Automatic year/quarter transitions

## 🚀 Production Deployment

```bash
# Install
composer install --optimize-autoloader --no-dev
npm run build

# Configure
cp .env.example .env  # Edit with production settings
php artisan key:generate

# Database
php artisan migrate --force
php artisan db:seed --class=ActivityTypeSeeder  # REQUIRED

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### ⚠️ Production Seeder Rules

**ONLY RUN:**
- ✅ `ActivityTypeSeeder` — Creates the 45 required activity types

**NEVER RUN:**
- ❌ `php artisan migrate:fresh --seed` — Deletes all data
- ❌ `UserSeeder`, `InterestSeeder`, `ActivitySeeder` — Creates fake data

See [PRODUCTION_DEPLOYMENT.md](PRODUCTION_DEPLOYMENT.md) for detailed guide.

## 🎯 Example User Journey

1. **Sign Up** → Create account
2. **Select Interests** → Choose 5-10 activities to track
3. **Log Activities** → Record daily workouts, meals, sleep
4. **Earn Points** → Get 10-50 points based on difficulty
5. **Build Streaks** → Maintain daily consistency
6. **Climb Rankings** → Compete on daily, seasonal, yearly leaderboards
7. **Track Progress** → See rank improvements over time

## 🤝 Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature/name`
3. Make changes and add tests
4. Run tests: `php artisan test`
5. Submit pull request

## 📄 License

MIT License

---

**Your Global Wellness Rank — Built by Everyday Choices.** 💪🏆
