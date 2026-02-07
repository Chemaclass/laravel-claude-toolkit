---
globs: resources/js/**/*.test.{tsx,ts}
---

# Frontend Testing Conventions

## Commands

```bash
npm run test          # Run all frontend tests
npm run test:fast     # Run tests without type-checking
npm run test:watch    # Watch mode for development
npm run test:e2e      # End-to-end tests
```

## File Location

Tests are **co-located** next to the file they test:

```
pages/Order/Show.tsx
pages/Order/Show.test.tsx
hooks/use-theme.ts
hooks/use-theme.test.ts
lib/formatting.ts
lib/formatting.test.ts
```

## Smoke Test Pattern

Every page component **MUST** have at minimum a smoke test:

```tsx
import '@/test/mocks';
import { render } from '@testing-library/react';
import { createShowProps } from '@/test/factories';
import Show from './Show';

describe('Order/Show', () => {
    it('renders without crashing', () => {
        render(<Show {...createShowProps()} />);
    });
});
```

## Mock Setup

- **Always** `import '@/test/mocks'` as the **first import** in component tests
- Browser API polyfills are in `resources/js/test/setup.ts` (configured in Vitest)
- Never re-mock `@inertiajs/react` — the shared mock handles `router`, `useForm`, `usePage`, `Head`, `Link`, `Deferred`
- `recharts` and `axios` are also mocked globally in `test/mocks.tsx`

## Factories

Factories live in `resources/js/test/factories/` and provide realistic default props:

```tsx
// test/factories/order.ts
export function createShowProps(overrides: Record<string, unknown> = {}) {
    return {
        id: '1',
        number: 'ORD-001',
        customerName: 'Test Customer',
        status: 'draft',
        ...overrides,
    };
}
```

- One factory file per module (e.g., `order.ts`, `user.ts`, `product.ts`)
- Re-export all factories from `test/factories/index.ts`
- Factory functions follow `create{Type}Props(overrides?)` naming

## What to Test

| Target | Minimum | When to add more |
|--------|---------|------------------|
| Pages | Smoke test (render with factory props) | Always required |
| Hooks | Behavior tests with `renderHook` | Always required |
| Utilities | Pure logic with expected inputs/outputs | Always required |
| Subcomponents | Optional | When component has complex conditional logic |

## Hook Test Pattern

```tsx
import { renderHook, act } from '@testing-library/react';
import { useTheme } from './use-theme';

describe('useTheme', () => {
    it('toggles theme', () => {
        const { result } = renderHook(() => useTheme());

        act(() => {
            result.current.toggle();
        });

        expect(result.current.isDark).toBe(true);
    });
});
```

## Utility Test Pattern

```tsx
import { formatCurrency } from './formatting';

describe('formatCurrency', () => {
    it('formats zero value', () => {
        expect(formatCurrency(0)).toBe('$0.00');
    });
});
```
