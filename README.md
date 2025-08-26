# EatFit — Laravel + Inertia/React (i18n-first)

Rebuild/POC za EatFit.rs sa fokusom na **višejezičnost** (sr-Latn-RS, sr-Cyrl-RS, en), čist routing sa `{locale}` prefiksom, Inertia/React UI i SEO `hreflang`. Ovo je baza za dalje (Order Wizard 1–5 + PDF predračun, Delivery, Admin/Filament).

> Tag: `v0.1.0-i18n` — i18n osnova (rute, middleware, hreflang, demo Page).

---

## Stack

- **Backend:** Laravel 12, PHP 8.2+  
- **Frontend:** Inertia + React 18, Vite  
- **DB:** MySQL 8+ (dev), SQLite opciono  
- **Auth/UI:** Breeze (Inertia/React)  
- **i18n:** custom (`config/i18n.php`) + React i18next  
- **CMS demo:** translatable Page (Spatie Translatable)

---

## 1) Brzi start (Windows/XAMPP)

**Prereq**
- PHP 8.2+, Composer
- Node 18+ (npm/pnpm)
- MySQL (XAMPP) — baza `eatfit_db`

**Setup**
```bash
composer install
cp .env.example .env

# .env – minimalne vrednosti:
# APP_URL=http://127.0.0.1:8088
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3307
# DB_DATABASE=eatfit_db
# DB_USERNAME=root
# DB_PASSWORD=
# CACHE_DRIVER=file
# SESSION_DRIVER=file
# QUEUE_CONNECTION=sync

php artisan key:generate
php artisan migrate
php artisan db:seed --class=PageSeed
Dev serveri (2 terminala)


# 1) PHP dev server (bez artisan serve na Win ako pravi problem)
php -S 127.0.0.1:8088 -t public

# 2) Vite dev
npm install
npm run dev
Otvori:

http://127.0.0.1:8088/sr-Latn-RS/

http://127.0.0.1:8088/sr-Cyrl-RS/

http://127.0.0.1:8088/en/

Demo CMS stranica (čita prevod iz DB):

/sr-Latn-RS/stranica/kako-funkcionise

/sr-Cyrl-RS/stranica/како-функционише

/en/stranica/how-it-works

2) i18n — kako je rešeno
Konfiguracija jezika: config/i18n.php
(default: sr-Latn-RS, fallback: en, supported: sr-Latn-RS, sr-Cyrl-RS, en)

Locale middleware: app/Http/Middleware/SetLocale.php

Rute: routes/web.php → sve javne rute su pod /{locale} prefiksom.

SEO hreflang: resources/views/components/seo/hreflang.blade.php (uključeno u resources/views/app.blade.php)

Inertia share: AppServiceProvider@boot() deli locale, locales, auth

React i18n: resources/js/i18n/index.js + LanguageSwitcher.jsx

3) Struktura fajlova (ključni delovi)

app/
  Http/Middleware/SetLocale.php
  Providers/AppServiceProvider.php  # Inertia::share(...)
  Domain/Cms/Page.php               # translatable (Spatie)

config/i18n.php

database/
  migrations/****_create_pages_table.php
  seeders/PageSeed.php

resources/
  js/
    app.jsx                         # initI18n + Inertia pages glob
    i18n/index.js
    Components/LanguageSwitcher.jsx
    Pages/Welcome.jsx
    Pages/PageView.jsx
  views/app.blade.php               # @vite, @inertia, @include hreflang
  views/components/seo/hreflang.blade.php

routes/web.php                      # {locale} grupa + demo Page ruta
4) Build (bez dev servera)

npm run build
# generiše public/build/** i koristi manifest
5) Najčešće greške & rešenja
ERR_CONNECTION_REFUSED
Dev server nije upaljen. Pokreni:


php -S 127.0.0.1:8088 -t public
Unable to locate file in Vite manifest: resources/js/Pages/PageView.jsx
Pokreni npm run dev ili npm run build; proveri da app.jsx globuje ./Pages/**/*.jsx.

Class Inertia not found ili App\Providers\Inertia not found
Dodaj use Inertia\Inertia; (i use Illuminate\Support\Facades\Vite;) u AppServiceProvider.

SQLite i JSON putanje (JSON_UNQUOTE not found)
Koristi MySQL driver (preporuka) ili u upitu koristi SQLite-friendly json_extract (uz navodnike).

optimize:clear traži tabelu cache
U .env koristi CACHE_DRIVER=file i SESSION_DRIVER=file za dev ili kreiraj tabele:
php artisan cache:table && php artisan session:table && php artisan migrate.

6) Git grane & tagovi
develop — aktivan razvoj

main — stabilno

feature/* — funkcionalnosti (npr. feature/order-wizard)

Tagovi:

v0.1.0-i18n — i18n osnova (ovaj commit)

.gitattributes preporuka (LF normalizacija):



* text=auto
*.php      text eol=lf
*.blade.php text eol=lf
*.js       text eol=lf
*.jsx      text eol=lf
*.css      text eol=lf
*.json     text eol=lf
*.md       text eol=lf
*.yml      text eol=lf
*.yaml     text eol=lf
*.png -text
*.jpg -text
*.jpeg -text
*.gif -text
*.webp -text
*.ico -text
*.pdf -text
*.ttf -text
*.otf -text
*.woff -text
*.woff2 -text
Renormalizacija:

```bash
git add --renormalize .
git commit -m "chore(gitattributes): normalize line endings"

7) Roadmap (sledeće faze)
Order Wizard 1–5 (plan → kcal → trajanje → adresa/zone → potvrda)

obračun iz price_matrix

validacije (zona/prozor)

kreiranje orders

PDF predračun i email potvrda

Delivery (zones/windows/shipping) + export kurirskih ruta

Admin (Filament): Orders Kanban, Menu Builder, Price Matrix

Payments V2: Stripe/PayPal (kasnije)

Mobile (React Native/Expo): MVP klijentska app + “assistant waiter” (stolovi, KDS)

8) Licenca
Interni projekat za EatFit.rs (© 2025).
Za open-source delove v. licencu korišćenih paketa (Laravel, Breeze, itd.).