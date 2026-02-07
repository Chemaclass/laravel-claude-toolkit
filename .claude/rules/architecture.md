---
globs: modules/**/*.php
---

# Architecture Overview

This project follows a **Modular Monolith Architecture** where each module is self-contained with **Hexagonal Architecture** (Ports & Adapters) and **Domain-Driven Design** tactical patterns.

```
┌───────────────────────────────────────────────────────────────────────┐
│                              modules/                                 │
│  ┌─────────────────────────┐    ┌─────────────────────────┐           │
│  │        User Module      │    │       Order Module      │           │
│  │  ┌───────────────────┐  │    │  ┌───────────────────┐  │           │
│  │  │  Infrastructure   │  │    │  │  Infrastructure   │  │           │
│  │  │  ┌─────────────┐  │  │    │  │  ┌─────────────┐  │  │           │
│  │  │  │ Application │  │  │    │  │  │ Application │  │  │           │
│  │  │  │ ┌─────────┐ │  │  │    │  │  │ ┌─────────┐ │  │  │           │
│  │  │  │ │ Domain  │ │  │  │    │  │  │ │ Domain  │ │  │  │           │
│  │  │  │ └─────────┘ │  │  │    │  │  │ └─────────┘ │  │  │           │
│  │  │  └─────────────┘  │  │    │  │  └─────────────┘  │  │           │
│  │  └───────────────────┘  │    │  └───────────────────┘  │           │
│  └─────────────────────────┘    └─────────────────────────┘           │
└───────────────────────────────────────────────────────────────────────┘
```

## Directory Structure

```
modules/                           # Each module is self-contained
├── {Module}/                      # e.g., User, Order, Product
│   ├── Domain/                    # Pure PHP, no Laravel deps
│   │   ├── Entity/                # Aggregates with identity
│   │   ├── ValueObject/           # Immutable value types
│   │   ├── Repository/            # Interfaces only
│   │   ├── Service/               # Domain services
│   │   └── Exception/             # Domain exceptions
│   ├── Application/               # Use cases
│   │   ├── Command/               # Write ops: DTO + Handler
│   │   └── Query/                 # Read ops: DTO + Handler
│   └── Infrastructure/            # Laravel implementations
│       ├── Persistence/
│       │   ├── Eloquent/
│       │   │   ├── Model/
│       │   │   └── Repository/
│       │   └── InMemory/          # For tests
│       ├── Http/
│       │   ├── Controller/
│       │   ├── Request/
│       │   └── Resource/
│       └── Provider/              # Module service provider

app/                               # Laravel app (global stuff only)

tests/
├── Unit/{Module}/                 # Per-module unit tests
│   ├── Domain/
│   └── Application/
├── Integration/{Module}/          # Per-module integration tests
└── Feature/{Module}/              # Per-module feature tests
```
