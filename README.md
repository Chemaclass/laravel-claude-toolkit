# Laravel Claude Toolkit

Laravel 12 project with Docker-based development environment.

## Stack

PHP 8.5 | Laravel 12 | SQLite | Vite + Tailwind CSS 4

## Quick Start

```bash
./vendor/bin/sail up -d    # Start
./vendor/bin/sail down     # Stop
```

Access: http://localhost

## Commands

```bash
sail up -d       # Start dev environment
sail down        # Stop all services
sail shell       # Open shell in container
sail test        # Run PHPUnit tests
sail artisan     # Run artisan commands
sail composer    # Run composer commands
sail npm         # Run npm commands
```

Tip: `alias sail='./vendor/bin/sail'`
