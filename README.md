# IOMeH - I Owe Me Health

> **Your Global Fitness Rank — Shaped by Your Daily Workouts and Nutrition.**

A global fitness tracking and ranking platform that gamifies your workout and nutrition journey. Track your daily exercises and healthy eating while competing on seasonal leaderboards. Think of it as a sports foundation, but for your fitness.

---

## ✨ Key Features

- 🏋️ **Many Activities** — Track across 2 categories: Workout, Nutrition
- 🏆 **Global Rankings** — Compete on daily, seasonal (Q1-Q4), and annual leaderboards
- 🎮 **Smart Points** — Earn 10-50 points per activity based on MET values and health impact
- 🔥 **Streak Multipliers** — Build daily streaks to multiply points up to 5.0×
- 💎 **Milestone Rewards** — Get bonus points at 7, 30, 100, 365+ day streaks
- 📅 **Today-Only Logging** — Activities logged in real-time for accurate streaks
- 📊 **Progress Tracking** — View your rank history and activity calendar
- 🔐 **Secure Auth** — Standard registration + OAuth (Google)

---

## 🎯 How It Works

### 1. Track Your Activities Daily

Log activities for today only - ensuring accurate streak calculations:

| Category | Examples | Points Range |
|----------|----------|--------------|
| 💪 **Workout** | Running, HIIT, Gym, Swimming, Cycling, Sports | 10-60 pts |
| 🥗 **Nutrition** | Healthy Meals, Vegetables, Fruits, Hydration, Supplements | 10-30 pts |

**Important:** Activities can only be logged for today to maintain streak integrity and prevent backdating.

### 2. Build Your Streak

Maintain daily consistency to unlock progressive multipliers:

| Days | Tier | Multiplier | Icon |
|------|------|------------|------|
| 1-2 | Newcomer | 1.0× | 🌱 |
| 3-6 | Beginner | 1.2× | 🔥 |
| 7-13 | Regular | 1.5× | ⚡ |
| 14-29 | Committed | 2.0× | 💪 |
| 30-59 | Dedicated | 2.5× | 🚀 |
| 60-99 | Expert | 3.0× | ⭐ |
| 100-199 | Master | 4.0× | 👑 |
| 200+ | Legend | 5.0× | 🏆 |

### 3. Unlock Milestone Bonuses

Reach major milestones for one-time point rewards:

- **7 days** → +50 pts 🎉
- **30 days** → +200 pts 🎊
- **60 days** → +500 pts 🌟
- **100 days** → +1,000 pts 💎
- **365 days** → +5,000 pts 🏆

**Example Calculation:**
```
Activity: Morning Run (20 base points)
Current Streak: 30 days (Dedicated tier)
Milestone: Just hit 30 days!

= 20 pts × 2.5× + 200 bonus
= 250 points! 🎉
```

### 4. Compete by Season

Rankings are organized quarterly for fair competition:

| Season | Months | Period |
|--------|--------|--------|
| 🌸 **Q1** | Jan-Mar | January 1 - March 31 |
| ☀️ **Q2** | Apr-Jun | April 1 - June 30 |
| 🍂 **Q3** | Jul-Sep | July 1 - September 30 |
| ❄️ **Q4** | Oct-Dec | October 1 - December 31 |

**Why Today-Only Logging?**
- ✅ Accurate streaks — Prevents backdating that would break streak calculations
- ✅ Fair competition — Everyone logs in real-time
- ✅ True accountability — Build genuine daily habits
- ✅ Data integrity — Clean, honest activity history

**How It Works:**
- Activities can only be logged for today's date
- Backend validation prevents any date manipulation
- Streaks are calculated accurately based on consecutive days
- Your season points accumulate throughout the quarter

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL or SQLite

### Installation

```bash
# Clone and enter directory
git clone git@github.com:edriso/iomeh.git
cd iomeh

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup (development)
php artisan migrate:fresh --seed

# Start servers
php artisan serve          # Terminal 1
npm run dev                # Terminal 2
```

Visit `http://localhost:8000`

**Test Account:** `test@example.com` / `password`

---

## 🧪 Testing

```bash
# Run all backend tests
php artisan test

# Run frontend tests
npm run test:frontend

# Test activity date validation feature
./vendor/bin/pest tests/Feature/ActivitySeasonValidationTest.php
```

**Test Coverage:** 61+ passing tests

---

## 🏗️ Tech Stack

### Backend
- **Framework:** Laravel 12
- **Language:** PHP 8.2+
- **Database:** MySQL / SQLite
- **Testing:** Pest PHP
- **Auth:** Laravel Fortify + Socialite

### Frontend
- **Framework:** Vue 3 + TypeScript
- **Styling:** Tailwind CSS
- **Components:** Shadcn/ui
- **SSR:** Inertia.js
- **Testing:** Vitest

### Infrastructure
- **Queue:** Database driver (configurable)
- **Cache:** File/Redis (configurable)
- **Storage:** Local/S3 (configurable)
- **No cron jobs required** — All updates happen on activity creation

---

## 📊 Database Design

### Core Tables

```
users
├─ id, username, email, name
├─ current_streak, longest_streak
├─ last_activity_date
└─ current_season_id (FK)

activity_types
├─ name, category, icon
├─ base_points, description
└─ display_order, is_active

habits
├─ user_id (FK)
├─ activity_type_id (FK)
├─ custom_name, notes
└─ display_order

activities
├─ user_id (FK)
├─ habit_id (FK)
├─ date, points_earned
└─ notes, memory_url
```

### Ranking System

```
seasons (current year only)
├─ user_id (FK)
├─ name (1-4 for Q1-Q4), year
├─ points (season total)
└─ season_year_points (year cumulative)

ranking_history (immutable)
├─ user_id (FK)
├─ ranking_type (season/year)
├─ rank, points
└─ season_name, year
```

**Key Features:**
- ✅ Dynamic rank calculation (no stored ranks for current data)
- ✅ Date-aware point allocation to correct season
- ✅ Automatic quarter transitions
- ✅ Historical rank preservation
- ✅ Season-based activity validation

---

## 🚀 Production Deployment

### Complete Production Setup

```bash
# 1. Install production dependencies
composer install --optimize-autoloader --no-dev
npm ci --production
npm run build

# 2. Configure environment
cp .env.example .env
# Edit .env with your production settings:
# - Set APP_ENV=production
# - Configure database
# - Set APP_URL
# - Add OAuth credentials (optional)

php artisan key:generate

# 3. Setup database (CRITICAL STEPS)
php artisan migrate --force
php artisan db:seed --class=ActivityTypeSeeder  # REQUIRED! Creates 45 activity types

# 4. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# 5. Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # Adjust user/group as needed

# 6. Clear any development caches
php artisan cache:clear
php artisan queue:restart  # If using queues
```

### 🔥 Critical Production Commands

#### **Database Setup (MUST RUN)**
```bash
# Run migrations first
php artisan migrate --force

# Then seed the required activity types
php artisan db:seed --class=ActivityTypeSeeder
```

#### **Performance Optimization**
```bash
# Cache everything for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear development caches
php artisan cache:clear
php artisan queue:restart
```

#### **File Permissions**
```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# If using different web server user, adjust accordingly:
# chown -R nginx:nginx storage bootstrap/cache
# chown -R apache:apache storage bootstrap/cache
```

### ⚠️ Important: Seeder Rules

**✅ ALWAYS RUN IN PRODUCTION:**
- `ActivityTypeSeeder` — Creates the 45 required activity types (MANDATORY!)

**❌ NEVER RUN IN PRODUCTION:**
- `php artisan migrate:fresh` — Destroys all data
- `php artisan db:seed` — Creates fake test data
- `UserSeeder`, `HabitSeeder`, `ActivitySeeder` — Development only

### 🚨 Production Checklist

#### **Before Going Live:**
- [ ] Environment variables configured
- [ ] Database migrations run
- [ ] ActivityTypeSeeder executed
- [ ] File permissions set correctly
- [ ] Caches optimized
- [ ] SSL certificate installed
- [ ] OAuth credentials configured (if using)

#### **Post-Deployment:**
- [ ] Test user registration
- [ ] Test activity logging
- [ ] Verify streak calculations
- [ ] Check rankings display
- [ ] Test OAuth login (if enabled)

### Environment Variables

```env
# Essential
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=iomeh
DB_USERNAME=your_user
DB_PASSWORD=your_password

# OAuth (Optional)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
```

### 🛠️ Production Troubleshooting

#### **Common Issues & Solutions**

**Issue: "No activity types available"**
```bash
# Solution: Run the ActivityTypeSeeder
php artisan db:seed --class=ActivityTypeSeeder
```

**Issue: "Permission denied" errors**
```bash
# Solution: Fix file permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Issue: "Class not found" errors**
```bash
# Solution: Clear caches and regenerate autoloader
php artisan config:clear
php artisan cache:clear
composer dump-autoload --optimize
```

**Issue: Frontend assets not loading**
```bash
# Solution: Rebuild assets and link storage
npm run build
php artisan storage:link
```

**Issue: Database connection errors**
```bash
# Solution: Check database configuration
php artisan config:clear
php artisan config:cache
```

#### **Health Check Commands**

```bash
# Verify application is working
php artisan route:list
php artisan config:show

# Check database connectivity
php artisan tinker
# Then run: DB::connection()->getPdo();

# Verify activity types are seeded
php artisan tinker
# Then run: App\Models\ActivityType::count();
```

#### **Performance Monitoring**

```bash
# Check application performance
php artisan about

# Monitor queue jobs (if using queues)
php artisan queue:work --verbose

# Check cache status
php artisan cache:table  # If using database cache
```

### 🚀 Quick Deployment Script

Create a `deploy.sh` script for easy production deployment:

```bash
#!/bin/bash
# deploy.sh - Production deployment script

echo "🚀 Starting IOMeH Production Deployment..."

# 1. Install dependencies
echo "📦 Installing dependencies..."
composer install --optimize-autoloader --no-dev
npm ci --production
npm run build

# 2. Configure environment
echo "⚙️ Configuring environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "⚠️  Please configure your .env file before continuing!"
    exit 1
fi

# 3. Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# 4. Setup database
echo "🗄️ Setting up database..."
php artisan migrate --force
php artisan db:seed --class=ActivityTypeSeeder

# 5. Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan storage:link

# 6. Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 7. Clear development caches
echo "🧹 Clearing development caches..."
php artisan cache:clear
php artisan queue:restart

echo "✅ Deployment complete!"
echo "🔍 Run health checks:"
echo "   php artisan about"
echo "   php artisan route:list"
```

Make it executable:
```bash
chmod +x deploy.sh
./deploy.sh
```

---

## 🎮 User Journey

### Getting Started
1. **Sign Up** → Create account or use OAuth
2. **Pick Activities** → Select 5-10 habits to track
3. **Log Daily** → Record today's activities in real-time
4. **Earn Points** → Get base points + streak multiplier

### Building Consistency
5. **Grow Streak** → Log daily to increase multiplier (1.0× → 5.0×)
6. **Hit Milestones** → Unlock bonus points at key streak days
7. **Watch Rank** → See your position improve on leaderboards

### Long-term Competition
8. **Track History** → View past season rankings and badges
9. **New Quarter** → Fresh competition every 3 months
10. **Annual Glory** → Compete for top annual ranking

---

## 💡 Tips for Success

### Maximize Points
- Build a daily streak — multipliers make a huge difference
- Diversify activities — high-point activities matter
- Hit milestones — bonus points add up quickly
- Log consistently — even on busy days, log something small

### Season Strategy
- Start strong at beginning of quarter
- Maintain consistency throughout
- Log activities daily - they can only be recorded for today
- Each season is a fresh chance to improve your rank

### Streak Maintenance
- Set daily reminders
- Have backup activities for busy days
- Missing a day resets your streak to 1
- Same-day activities don't break streaks

---

## 🛠️ Development

### Project Structure

```
iomeh/
├── app/
│   ├── Http/Controllers/     # Request handlers
│   ├── Models/               # Eloquent models
│   └── Rules/                # Custom validations (e.g., CurrentSeasonDate)
├── database/
│   ├── migrations/           # Database schema
│   └── seeders/              # Data seeders
├── resources/
│   ├── js/
│   │   ├── components/       # Vue components
│   │   ├── pages/            # Inertia pages
│   │   └── layouts/          # Page layouts
│   └── css/                  # Tailwind styles
├── routes/
│   ├── web.php               # Web routes
│   └── auth.php              # Auth routes
└── tests/
    ├── Feature/              # Integration tests
    └── Unit/                 # Unit tests
```

### Adding New Activity Types

Activity types are seeded from `database/seeders/ActivityTypeSeeder.php`. To add new types:

1. Edit the seeder file
2. Add to appropriate category array
3. Run: `php artisan db:seed --class=ActivityTypeSeeder`

### Customizing Point Values

Points are based on MET (Metabolic Equivalent of Task) values and health impact. Modify in `ActivityTypeSeeder.php`.

---

## 🤝 Contributing

We welcome contributions! Here's how:

1. **Fork** the repository
2. **Create** a feature branch: `git checkout -b feature/amazing-feature`
3. **Make** your changes
4. **Add tests** for new features
5. **Run tests**: `php artisan test`
6. **Commit**: `git commit -m 'Add amazing feature'`
7. **Push**: `git push origin feature/amazing-feature`
8. **Open** a Pull Request

### Contribution Guidelines
- Write tests for new features
- Follow existing code style
- Update README if needed
- Keep commits focused and descriptive

---

## 📝 License

MIT License - see LICENSE file for details

---

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI powered by [Tailwind CSS](https://tailwindcss.com)
- Components from [Shadcn/ui](https://ui.shadcn.com)
- Icons from [Lucide](https://lucide.dev)

---

## 📧 Support

- **Issues:** Open an issue on GitHub
- **Questions:** Check existing issues or start a discussion

---

**Your Global Health Rank — Shaped by Your Daily Habits.** 💪🏆

*Track. Compete. Thrive.*
