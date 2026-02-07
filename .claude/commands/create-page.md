# Create React Page

Scaffold a new Inertia.js React page with co-located test and factory.

## Arguments
- `$ARGUMENTS` - `<Module> <PageName>` (e.g., `Order Index`)

## Instructions

Parse `$ARGUMENTS` into `{Module}` and `{PageName}`.

### 1. Create test first (TDD)

Create `resources/js/pages/{Module}/{PageName}.test.tsx`:

```tsx
import '@/test/mocks';
import { render } from '@testing-library/react';
import { create{PageName}Props } from '@/test/factories';
import {PageName} from './{PageName}';

describe('{Module}/{PageName}', () => {
    it('renders without crashing', () => {
        render(<{PageName} {...create{PageName}Props()} />);
    });
});
```

### 2. Create page component

Create `resources/js/pages/{Module}/{PageName}.tsx`:

```tsx
import { Head } from '@inertiajs/react';
import { AppLayout } from '@/layouts/AppLayout';

interface {PageName}Props {
    // Add props from controller
}

export default function {PageName}(props: {PageName}Props) {
    return (
        <AppLayout title="{Module} - {PageName}">
            <Head title="{Module} {PageName}" />
            {/* Page content */}
        </AppLayout>
    );
}
```

### 3. Create subcomponents directory (if complex page)

If the page will have multiple sections, create:
- `resources/js/pages/{Module}/components/types.ts` — shared interfaces
- `resources/js/pages/{Module}/components/index.ts` — barrel exports

### 4. Create factory

Create or update `resources/js/test/factories/{module}.ts` (lowercase module name):

```tsx
export function create{PageName}Props(overrides: Record<string, unknown> = {}) {
    return {
        // Default prop values
        ...overrides,
    };
}
```

Update `resources/js/test/factories/index.ts` to re-export the new factory.

### 5. Verify

Run `npm run test:fast` and ensure the smoke test passes.

Provide a one-liner conventional commit message.
