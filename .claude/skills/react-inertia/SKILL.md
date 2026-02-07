# React + Inertia.js Skill

## Activation Triggers
- Creating or modifying files in `resources/js/`
- Questions about React, Inertia, or frontend architecture
- Scaffolding new pages, components, or hooks

## Architecture Map

```
resources/js/
├── app.tsx                              # Inertia app entry point
├── types/
│   └── index.d.ts                       # Global TypeScript types (PageProps, User, etc.)
├── lib/                                 # Utility functions
│   ├── utils.ts                         # cn() — clsx + tailwind-merge
│   ├── formatting.ts                    # Number, currency, date formatting
│   └── badges.ts                        # Badge variant mappings per entity status
├── hooks/                               # Custom React hooks
│   ├── use-theme.ts                     # Dark mode toggle (isDark, toggle)
│   └── use-persistent-state.ts          # localStorage-backed state
├── components/
│   ├── ui/                              # shadcn/ui + Radix UI primitives
│   │   ├── button.tsx                   # CVA variants: primary, secondary, danger, ghost, outline, link
│   │   ├── card.tsx                     # Card, CardHeader, CardTitle, CardContent, CardFooter
│   │   ├── badge.tsx                    # Variants: default, secondary, success, danger, warning, outline
│   │   ├── dialog.tsx                   # Radix Dialog primitives
│   │   ├── dropdown-menu.tsx            # Radix DropdownMenu
│   │   ├── select.tsx                   # Radix Select
│   │   ├── input.tsx                    # Input with label, error, hint
│   │   ├── textarea.tsx                 # Textarea with label, error
│   │   ├── tooltip.tsx                  # Radix Tooltip
│   │   ├── alert.tsx                    # Alert, AlertTitle, AlertDescription
│   │   ├── skeleton.tsx                 # Loading placeholder
│   │   └── empty-state.tsx              # Icon + title + description + action
│   └── ...                              # Shared components across modules
├── layouts/
│   ├── AppLayout.tsx                    # Authenticated: title, breadcrumbs, headerActions
│   ├── GuestLayout.tsx                  # Public/login pages
│   └── Sidebar.tsx                      # Main navigation
├── pages/                               # Inertia page components
│   ├── {Module}/
│   │   ├── {Page}.tsx                   # Default export, Props interface
│   │   ├── {Page}.test.tsx              # Co-located smoke test
│   │   └── components/
│   │       ├── types.ts                 # Shared interfaces
│   │       ├── index.ts                 # Barrel exports
│   │       └── {Component}.tsx          # Named exports
│   └── ...
└── test/
    ├── setup.ts                         # Vitest global setup (browser API polyfills)
    ├── mocks.tsx                         # Inertia, recharts, axios mocks
    └── factories/
        ├── index.ts                     # Re-exports all factories
        └── {module}.ts                  # create{Page}Props() per module
```

## Component Patterns

### Page Component

```tsx
import { Head, useForm, router } from '@inertiajs/react';
import { AppLayout } from '@/layouts/AppLayout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ItemCard, StatusBadge } from './components';
import type { ItemData } from './components';

interface ShowProps {
    item: ItemData;
    relatedItems: RelatedItem[];
}

export default function Show({ item, relatedItems }: ShowProps) {
    return (
        <AppLayout
            title={item.name}
            breadcrumbs={[
                { label: 'Items', href: '/items' },
                { label: item.name },
            ]}
            headerActions={<Button variant="primary">Edit</Button>}
        >
            <Head title={item.name} />
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <ItemCard item={item} />
            </div>
        </AppLayout>
    );
}
```

### Subcomponent (Named Export)

```tsx
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import type { ItemData } from './types';

interface ItemCardProps {
    item: ItemData;
    className?: string;
}

export function ItemCard({ item, className }: ItemCardProps) {
    return (
        <Card className={cn(className)}>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    {item.name}
                    <Badge variant="success">{item.status}</Badge>
                </CardTitle>
            </CardHeader>
            <CardContent>
                {/* Content */}
            </CardContent>
        </Card>
    );
}
```

### Dialog Pattern

```tsx
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

interface ConfirmDialogProps {
    open: boolean;
    onClose: () => void;
    onConfirm: () => void;
}

export function ConfirmDialog({ open, onClose, onConfirm }: ConfirmDialogProps) {
    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Confirm Action</DialogTitle>
                    <DialogDescription>Are you sure?</DialogDescription>
                </DialogHeader>
                <div className="flex justify-end gap-2">
                    <Button variant="ghost" onClick={onClose}>Cancel</Button>
                    <Button variant="danger" onClick={onConfirm}>Confirm</Button>
                </div>
            </DialogContent>
        </Dialog>
    );
}
```

### Deferred Loading Pattern

```tsx
import { Deferred } from '@inertiajs/react';
import { Skeleton } from '@/components/ui/skeleton';

interface PageProps {
    summary: SummaryData;           // Loaded immediately
    forecast?: ForecastData[];      // Loaded via Deferred
}

export default function Page({ summary, forecast }: PageProps) {
    return (
        <AppLayout title="Dashboard">
            <SummaryCard data={summary} />
            <Deferred data="forecast" fallback={<Skeleton className="h-64 w-full" />}>
                <ForecastChart data={forecast!} />
            </Deferred>
        </AppLayout>
    );
}
```

## Inertia.js Patterns

### useForm (Forms)

```tsx
const form = useForm({
    name: '',
    email: '',
});

function submit(e: FormEvent) {
    e.preventDefault();
    form.post('/users', { preserveScroll: true });
}

// form.data, form.setData, form.processing, form.errors, form.reset()
```

### router (Non-form Actions)

```tsx
import { router } from '@inertiajs/react';

// Navigation
router.visit('/path');

// Actions
router.post('/endpoint', data, {
    preserveScroll: true,
    onSuccess: () => { /* callback */ },
});

router.delete(`/items/${id}`, {
    preserveScroll: true,
});
```

### usePage (Auth/Flash Data)

```tsx
import { usePage } from '@inertiajs/react';
import type { PageProps } from '@/types';

const { props } = usePage<PageProps>();
const user = props.auth.user;
```

## Testing Patterns

### Smoke Test

```tsx
import '@/test/mocks';
import { render } from '@testing-library/react';
import { createShowProps } from '@/test/factories';
import Show from './Show';

describe('Module/Show', () => {
    it('renders without crashing', () => {
        render(<Show {...createShowProps()} />);
    });
});
```

### Factory

```tsx
export function createShowProps(overrides: Record<string, unknown> = {}) {
    return {
        item: { id: '1', name: 'Test', status: 'active' },
        relatedItems: [],
        ...overrides,
    };
}
```

## Common Mistakes to Avoid

| Mistake | Correct Approach |
|---------|-----------------|
| Native `<button>` | Use `<Button>` from `@/components/ui/button` |
| Native `<select>` | Use `<Select>` from `@/components/ui/select` |
| `className={a + " " + b}` | Use `cn(a, b)` from `@/lib/utils` |
| `useState` for form data | Use `useForm` from `@inertiajs/react` |
| Inline SVG icons | Use Lucide React (`import { Icon } from 'lucide-react'`) |
| Missing test mocks | Always `import '@/test/mocks'` first in tests |
| `export default` on subcomponents | Use named exports; default is only for pages |
| Missing dark mode | Every `bg-*`/`text-*` needs `dark:` variant |
| Missing `preserveScroll` | Add `preserveScroll: true` to router actions |
| `any` type | Use proper interfaces or `unknown` |
| Manual loading spinner next to icon | Use `<Button loading={bool}>` — spinner auto-replaces child icon |
