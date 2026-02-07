---
globs: resources/js/**/*.{tsx,ts}
---

# React Conventions

## Page Pattern

Pages live in `resources/js/pages/{Module}/{Page}.tsx`.

- **Default export** for the page component
- Props interface at the top of the file, named `{Page}Props`
- Wrap in `<AppLayout>` (authenticated) or `<GuestLayout>` (public)
- Include `<Head title="Page Title" />`

```tsx
import { Head } from '@inertiajs/react';
import { AppLayout } from '@/layouts/AppLayout';

interface IndexProps {
    items: Item[];
}

export default function Index({ items }: IndexProps) {
    return (
        <AppLayout
            title="Page Title"
            breadcrumbs={[{ label: 'Home', href: '/' }]}
            headerActions={<Button>Action</Button>}
        >
            <Head title="Page Title" />
            {/* Page content */}
        </AppLayout>
    );
}
```

## Subcomponents

Page-specific components live in `resources/js/pages/{Module}/components/`.

- **Named exports** (not default) for subcomponents
- `types.ts` for shared interfaces
- `index.ts` barrel re-exports all components and types

```tsx
// components/ItemCard.tsx
interface ItemCardProps {
    name: string;
    value: number;
}

export function ItemCard({ name, value }: ItemCardProps) {
    return <Card>...</Card>;
}
```

```tsx
// components/index.ts
export { ItemCard } from './ItemCard';
export type { ItemData, ItemCardProps } from './types';
```

## TypeScript

- Strict mode — no `any`, no `@ts-ignore`
- Use `interface` for component props
- Use `type` for unions and utility types
- Use `import type` for type-only imports

## Inertia.js

- `useForm` for form submissions (tracks data, errors, processing state)
- `router.post/put/delete` for non-form actions
- `<Deferred data="propName" fallback={<Skeleton />}>` for async/expensive props
- `preserveScroll: true` on actions that shouldn't reset scroll position
- `<Link>` for Inertia navigation, `<a>` only for external URLs

## Hooks

Custom hooks live in `resources/js/hooks/` with `use-{name}.ts` filename.

## Utilities

- `cn()` from `@/lib/utils` — class merging (clsx + tailwind-merge)
- `@/lib/formatting` — number and date formatting utilities
- `@/lib/badges` — badge variant mappings per entity status

## File Naming

| Type | Pattern | Example |
|------|---------|---------|
| Page | `pages/{Module}/{Page}.tsx` | `pages/Order/Show.tsx` |
| Page test | `pages/{Module}/{Page}.test.tsx` | `pages/Order/Show.test.tsx` |
| Subcomponent | `pages/{Module}/components/{Name}.tsx` | `pages/Order/components/ItemsCard.tsx` |
| Types | `pages/{Module}/components/types.ts` | shared interfaces for a module's components |
| Barrel | `pages/{Module}/components/index.ts` | re-exports all components and types |
| Hook | `hooks/use-{name}.ts` | `hooks/use-theme.ts` |
| Hook test | `hooks/use-{name}.test.ts` | `hooks/use-theme.test.ts` |
| Utility | `lib/{name}.ts` | `lib/formatting.ts` |
| Utility test | `lib/{name}.test.ts` | `lib/formatting.test.ts` |
| UI component | `components/ui/{name}.tsx` | `components/ui/button.tsx` |
| Test factory | `test/factories/{module}.ts` | `test/factories/order.ts` |
