# Restaurant POS — Setup Guide

This project builds on your uploaded Laravel skeleton. The `vendor/`, `node_modules/`,
and `.git/` folders were removed from this zip to keep it small — reinstall them with
the commands below.

## Requirements
- PHP 8.4+ (this Laravel 13 app requires it)
- Composer
- Node.js + npm (only needed if you want to run `npm run dev`/`build`; the app UI
  uses Bootstrap via CDN, so npm is optional for now)

## Setup

```bash
composer install

cp .env.example .env      # if .env is missing; otherwise keep your existing .env
php artisan key:generate

touch database/database.sqlite   # SQLite is already configured in .env

php artisan migrate --seed
php artisan serve
```

Visit `http://127.0.0.1:8000` and you'll be redirected to `/login`.

## Demo accounts (seeded)
All use the password `password`.

| Role  | Email            |
|-------|------------------|
| Admin | admin@pos.test   |
| Staff | staff@pos.test   |
| Chef  | chef@pos.test    |

The seeder also creates 8 tables and a starter menu (Starters, Main Course, Drinks, Desserts).

## What was built

**Admin** (`/admin/...`)
- Dashboard with quick stats and recent orders
- Manage staff & chef accounts (create/edit/delete)
- Manage categories (name + station: kitchen or bar)
- Manage menu items (create/edit/delete, toggle availability)
- View all orders with status filter and detail page

**Staff** (`/staff/...`)
- Login → table grid (green = available, red = occupied)
- Tap a table to open/resume its order
- Add/remove menu items while the order is `open`
- Send order to kitchen (locks editing, order enters the kitchen queue)
- View order status while the kitchen works on it
- Mark order as served once the kitchen finishes it
- Bill page: record a single payment or split the bill evenly across guests
  (cash/card/mobile); order is marked `paid` once the balance reaches $0 and the
  table is freed up automatically

**Chef** (`/chef/...`)
- Kitchen board with three columns: Pending → In Progress → Recently Finished
- Each ticket shows item name, quantity, and station (kitchen/bar) so it can be
  routed as a kitchen ticket or bar ticket
- Accept a pending order, move it to Preparing, then mark it Finished — staff
  can then serve it

## Order lifecycle
`open → sent_to_kitchen → accepted → preparing → finished → served → paid`
(or `cancelled` at any point, if you wire up a cancel action later)

## Notes / next steps you may want
- Admin currently can also access the Staff and Chef screens (useful for testing);
  remove `admin` from the `role:` middleware on those route groups if you want stricter separation.
- There's no user-facing "cancel order" action yet — add one in `Staff\OrderController`
  if needed.
- Money is stored with 2-decimal precision (`decimal:2`); switch to integer cents
  if you want to avoid floating-point rounding edge cases in split payments.
