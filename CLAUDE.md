# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Nexus5ive is a Laravel 12 BBS (Bulletin Board System) that has been running since 2001. It uses PHP 8.2+, Livewire 3.5 for real-time components, Bootstrap 5.3 for styling, and Vite for asset bundling.

## Development Commands

This project uses Laravel Sail (Docker). Prefix commands with `./vendor/bin/sail`:

```bash
# Testing
sail test                       # Unit and feature tests
sail dusk                       # Browser tests (Selenium)

# Code quality
sail pint app                  # Format PHP code (Laravel Pint)
sail npm run larastan          # Static analysis (PHPStan level 5)

# Frontend
sail npm run dev                    # Vite watch mode
sail npm run build                  # Production build
```

Without Sail:
```bash
php artisan test
vendor/bin/pint app
vendor/bin/phpstan analyse
```

## Architecture

**Core Domain Models** (in `app/Models/`):
- **Section** - Forum categories (hierarchical)
- **Topic** - Discussion threads within sections
- **Post** - Posts within a Topic
- **User** - Users of the BBS
- **Comment** - Comments on a user's profile page
- **View** - Tracks which posts users have read

**Key Patterns**:
- Form Request classes handle validation AND authorization (`app/Http/Requests/Nexus/`)
- Policies provide gate-based authorization (`app/Policies/`)
- Helper classes extract business logic (`app/Helpers/`) - ViewHelper, ActivityHelper, BreadcrumbHelper, NxCodeHelper
- Events trigger cache invalidation (`TreeCacheBecameDirty`, `MostRecentPostForSectionBecameDirty`)
- Livewire components power real-time features: Chat, Mentions, Settings, Search (`app/Livewire/`)

**Controllers** are RESTful and located in `app/Http/Controllers/Nexus/`.

## Configuration

BBS-specific settings are in `config/nexus.php`. Key env vars:
- `NEXUS_NAME` - Site name
- `NEXUS_ADMIN_EMAIL` - Admin contact
- `NEXUS_ALLOW_REGISTRATIONS` - Enable/disable signups
- `NEXUS_PAGINATION` - Items per page

## Artisan Commands

```bash
php artisan nexus:install      # Initialize BBS
php artisan nexus:theme add --name=Excelsior --path='/css/excelsior.css'
php artisan nexus:theme remove --name=minty
php artisan nexus:unverified   # List unverified users
```

## Git Workflow

- `master` branch is production (deployed)
- Create feature branches from `develop`
- Run `sail artisan test && sail artisan dusk` before PRs
- License: GNU GPL v2
