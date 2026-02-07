# Create Custom React Hook

Scaffold a custom hook with co-located test (TDD).

## Arguments
- `$ARGUMENTS` - `<HookName>` in kebab-case (e.g., `use-debounce`)

## Instructions

Parse `$ARGUMENTS` as `{hookName}` (kebab-case). Derive `{HookFn}` as camelCase (e.g., `useDebounce`).

### 1. Create test first (TDD)

Create `resources/js/hooks/{hookName}.test.ts`:

```tsx
import { renderHook, act } from '@testing-library/react';
import { {HookFn} } from './{hookName}';

describe('{HookFn}', () => {
    it('returns initial value', () => {
        const { result } = renderHook(() => {HookFn}());

        // Assert initial state
    });
});
```

### 2. Create hook

Create `resources/js/hooks/{hookName}.ts`:

```tsx
import { useState } from 'react';

export function {HookFn}() {
    // Hook implementation
}
```

### 3. Verify

Run `npm run test:fast` and ensure the test passes.

Provide a one-liner conventional commit message.
