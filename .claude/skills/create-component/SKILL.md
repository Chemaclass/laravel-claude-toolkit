# Create React Subcomponent

Scaffold a page-level subcomponent with proper exports.

## Arguments
- `$ARGUMENTS` - `<Module> <ComponentName>` (e.g., `Order ItemSummaryCard`)

## Instructions

Parse `$ARGUMENTS` into `{Module}` and `{ComponentName}`.

### 1. Create component

Create `resources/js/pages/{Module}/components/{ComponentName}.tsx`:

```tsx
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface {ComponentName}Props {
    // Add props
}

export function {ComponentName}({ }: {ComponentName}Props) {
    return (
        <Card>
            <CardHeader>
                <CardTitle>{ComponentName}</CardTitle>
            </CardHeader>
            <CardContent>
                {/* Component content */}
            </CardContent>
        </Card>
    );
}
```

### 2. Update barrel export

Add the named export to `resources/js/pages/{Module}/components/index.ts`:

```tsx
export { {ComponentName} } from './{ComponentName}';
```

### 3. Add shared types

If the component introduces types used by other components, add them to `resources/js/pages/{Module}/components/types.ts` and re-export from `index.ts`.

### 4. Verify

Run `npm run test:fast` to ensure nothing breaks.

Provide a one-liner conventional commit message.
