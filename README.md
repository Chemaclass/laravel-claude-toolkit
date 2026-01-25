# Laravel Claude Toolkit

A Laravel starter kit for building scalable applications using **Modular Monolith**, **Hexagonal Architecture**, and **AI-assisted development**.

[![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel 12](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## Why This Toolkit?

**The Problem:**
> Laravel projects often start clean but grow into tangled monoliths where everything depends on everything. Adding features becomes risky, testing becomes painful, and onboarding new developers takes weeks.

**The Solution:**
> A structured approach combining proven patterns (Modular Monolith, Hexagonal Architecture, DDD) with AI-assisted development that enforces consistency and guides architectural decisions.

**Who is this for?**
- Teams wanting structure without microservices complexity
- Developers learning DDD/Clean Architecture with real examples
- Projects that might need to extract modules to services later

---

## Key Benefits

### Architecture Benefits

| Benefit | How |
|---------|-----|
| **Modularity** | Independent modules with clear boundaries |
| **Testability** | Pure domain logic, interfaces enable easy mocking |
| **Maintainability** | Predictable file locations, consistent patterns |
| **Scalability** | Extract modules to microservices when needed |
| **Team Autonomy** | Teams own modules without conflicts |

### AI-Assisted Benefits

| Benefit | How |
|---------|-----|
| **Consistency** | Commands generate code following established patterns |
| **Guidance** | Agents help with architectural decisions |
| **Speed** | Scaffold complete features with TDD in minutes |
| **Learning** | Skills teach best practices in context |
| **Quality** | Built-in SOLID and clean code checks |

---

## Architecture at a Glance

```
modules/{Module}/
├── Domain/           # Pure PHP: Entities, Value Objects, Repository interfaces
├── Application/      # Use Cases: Command/Query handlers (CQRS)
└── Infrastructure/   # Laravel: Controllers, Eloquent, HTTP layer
```

**Dependency Rule:** Domain → Application → Infrastructure (never the reverse)

---

## Quick Start

```bash
# Clone using GitHub CLI
gh repo create my-app --template Chemaclass/laravel-claude-toolkit --public --clone

# Setup the project
cd my-app && composer setup

# Start development server (Sail provides Docker containers)
APP_PORT=8085 ./vendor/bin/sail up -d

# Optional: Remove template's GitHub Pages files
rm index.html .nojekyll
```

Access: http://localhost:8085

**Stack:** PHP 8.4 | Laravel 12 | SQLite | Tailwind CSS 4 | Sail

---

## AI-Powered Workflow

This toolkit includes Claude Code configurations for AI-assisted development.

### When to Use What

| Tool | Purpose | Example |
|------|---------|---------|
| **Agents** | Architectural questions, code review | "How should I structure the Order module?" |
| **Commands** | Scaffolding entities, use cases, controllers | `/create-entity Order Order` |
| **Skills** | Reference material while coding | Pattern templates and best practices |

### Example Feature Workflow

```
1. /create-module Order              → Module structure
2. /create-entity Order Order        → Domain entity + test
3. /create-repository Order Order    → Repository pattern
4. /create-use-case Order Command CreateOrder → Handler
5. /create-controller Order Order    → HTTP layer
6. /tdd-cycle                        → Guide through TDD
```

### Available Agents

| Agent | Purpose |
|-------|---------|
| `domain-architect` | DDD & hexagonal architecture guidance |
| `tdd-coach` | Red-green-refactor workflow coaching |
| `clean-code-reviewer` | SOLID principles & code smell detection |
| `security-reviewer` | OWASP Top 10 & Laravel security analysis |

### Available Commands

| Command | Generates |
|---------|-----------|
| `/create-module` | Full module structure with all layers |
| `/create-entity` | Domain entity + value objects + test |
| `/create-value-object` | Immutable value object + validation + test |
| `/create-repository` | Interface + Eloquent + InMemory implementations |
| `/create-use-case` | Command/Query DTO + Handler + test |
| `/create-controller` | Thin controller + request + resource |
| `/tdd-cycle` | Interactive red-green-refactor guide |
| `/refactor-check` | SOLID violations & improvement report |
| `/code-review` | Code quality & architecture compliance review |
| `/security-review` | Security analysis with OWASP checks |

### Rules (Always Active)

| Rule | Enforces |
|------|----------|
| `security` | Pre-commit security checks, no hardcoded secrets |
| `testing` | 80% coverage minimum, TDD workflow |
| `coding-style` | Immutability, file limits, naming conventions |
| `git-workflow` | Conventional commits, PR process |
| `performance` | Database optimization, caching patterns |

---

## Development Commands

```bash
# Start/Stop
./vendor/bin/sail up -d       # Start dev environment
./vendor/bin/sail down        # Stop all services

# Development
./vendor/bin/sail shell       # Open shell in container
./vendor/bin/sail artisan     # Run artisan commands
./vendor/bin/sail composer    # Run composer commands
./vendor/bin/sail npm         # Run npm commands

# Testing
./vendor/bin/sail test        # Run all tests
./vendor/bin/sail test tests/Unit      # Unit tests only
./vendor/bin/sail test tests/Feature   # Feature tests only
```

**Tip:** `alias sail='./vendor/bin/sail'`

---

## Learn More

See [CLAUDE.md](CLAUDE.md) for:
- Detailed architecture documentation
- Module structure conventions
- Inter-module communication patterns
- TDD workflow guidelines
- Complete command reference
