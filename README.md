# Loyalty Program Assessment (Mid-Level Full-Stack)

A full-stack loyalty feature for an e-commerce store where customers unlock achievements and earn badge-based cashback rewards.

## What Makes This Submission Different

- Config-driven achievement and badge rules (`backend_laravel/config/achievements.php`)
- Event-driven backend flow (`PurchaseMade` -> `AchievementUnlocked` / `BadgeUnlocked`)
- Queued listeners for scalable processing
- Cashback handled through a dedicated mock `PaymentService`
- Rich API response with progress insights (`progress_percentage`)
- Product-style React dashboard with animation, loading states, and micro-interactions
- Unit + feature tests for core behavior

## Tech Stack

- Backend: Laravel (PHP)
- Frontend: React + TypeScript + Vite + Tailwind CSS + Framer Motion
- Testing: PHPUnit

## Architecture Overview

### Backend flow

1. A purchase is recorded.
2. `PurchaseMade` event is dispatched.
3. `ProcessAchievements` listener evaluates config rules and unlocks achievements.
4. `AchievementUnlocked` event is dispatched per newly unlocked achievement.
5. If badge tier changes, `BadgeUnlocked` is dispatched.
6. `ProcessCashback` listener calls `PaymentService` to simulate sending `₦300` cashback.

### Key backend files

- `backend_laravel/config/achievements.php`: achievement + badge definitions
- `backend_laravel/app/Services/AchievementService.php`: core unlock/progression logic
- `backend_laravel/app/Services/PaymentService.php`: mock cashback provider
- `backend_laravel/app/Events/*.php`: domain events
- `backend_laravel/app/Listeners/*.php`: listeners for achievements and cashback
- `backend_laravel/app/Http/Controllers/Api/AchievementController.php`: required API endpoint
- `backend_laravel/routes/api.php`: API routes

### Frontend structure

- `frontend/src/App.tsx`: dashboard orchestration
- `frontend/src/components/*`: UI sections (badge, achievements, progress, stats, controls, skeleton)
- `frontend/src/services/api.ts`: API client
- `frontend/src/types/index.ts`: response typing

## API Endpoint

### Get user achievements

- Method: `GET`
- Route: `/api/users/{user}/achievements`

Returns:

- `unlocked_achievements` (array of strings)
- `next_available_achievements` (array of strings)
- `current_badge` (string)
- `next_badge` (string|null)
- `remaining_to_unlock_next_badge` (integer)

Additional value-added fields:

- `progress_percentage` (integer)
- detailed `achievements` and `badges` objects
- `stats`

Example response (trimmed):

```json
{
  "success": true,
  "data": {
    "unlocked_achievements": ["First Purchase", "Returning Customer"],
    "next_available_achievements": ["Loyal Shopper", "Shopaholic"],
    "current_badge": "Silver",
    "next_badge": "Gold",
    "remaining_to_unlock_next_badge": 2,
    "progress_percentage": 33
  }
}
```

## Setup Instructions

## Backend (Laravel)

Prerequisites:

- PHP 8.1+
- Composer
- SQLite (default), or MySQL/PostgreSQL

Then continue with these steps:

1. Open terminal in `backend_laravel`
2. Install dependencies:

```bash
composer install
```

3. Create environment file:

```bash
cp .env.example .env
```

4. Generate app key:

```bash
php artisan key:generate
```

5. Confirm local defaults in `.env`:

```env
DB_CONNECTION=sqlite
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
CACHE_STORE=file
```

6. Configure DB connection only if you are not using SQLite
7. Run migrations and seeders:

```bash
php artisan migrate --seed
```

8. Start backend server:

```bash
php artisan serve
```

Optional (for queued listeners):

```bash
php artisan queue:work
```

Use `queue:work` only if you switch `QUEUE_CONNECTION` away from `sync`.

## Frontend (React)

Prerequisites:

- Node.js 18+
- npm

Steps:

1. Open terminal in `frontend`
2. Install dependencies:

```bash
npm install
```

3. Start dev server:

```bash
npm run dev
```

The frontend runs on `http://localhost:3000` and proxies `/api` to the Laravel server (`http://localhost:8000`).

## Tests

From `backend_laravel`:

```bash
php artisan test
```

Included tests:

- `tests/Unit/AchievementServiceTest.php`
- `tests/Feature/AchievementApiTest.php`

## Demo Notes

- Use the dashboard button "Simulate Purchase" to trigger purchase events.
- New badges trigger cashback simulation and logs.
- Achievement and badge progression is driven by config, not hardcoded controller logic.

## Suggested Commit Style

- `feat: implement config-driven achievement unlock engine`
- `feat: add badge unlock cashback listener`
- `feat: build responsive loyalty dashboard`
- `test: cover achievement service and API contract`
- `docs: add architecture and setup guide`
