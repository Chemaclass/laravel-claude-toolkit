# UI/Frontend Design Review

Review React components for design consistency, professional styling, and theme compliance.

## Arguments
- `$ARGUMENTS` - Optional: specific file path or component name to review

## Instructions

1. **Identify target files** to review:
   ```bash
   # If argument provided, review that specific file
   # Otherwise, check recently modified React files
   git diff --name-only HEAD~5 | grep -E '\.(tsx|ts)$'
   ```

2. **Check for native HTML elements** that should use styled components:
   - `<button>` -> Use `<Button>` from `@/components/ui/button`
   - `<select>` -> Use `<Select>` from `@/components/ui/select`
   - `<input>` without `<Input>` wrapper -> Use `<Input>` from `@/components/ui/input`
   - `window.alert/confirm` -> Use `<Dialog>` from `@/components/ui/dialog`
   - Native tooltip -> Use `<Tooltip>` from `@/components/ui/tooltip`

3. **Verify design consistency**:
   - All interactive elements have `cursor-pointer`
   - Hover states are defined with `hover:` classes
   - Transitions are smooth (`transition-colors`, `transition-all`)
   - Focus states include `focus:ring-*` classes
   - Disabled states include `disabled:` modifiers

4. **Check dark mode support**:
   - Every `bg-*` should have a `dark:bg-*` variant
   - Every `text-*` should have a `dark:text-*` variant
   - Every `border-*` should have a `dark:border-*` variant
   - Ring colors should have dark variants

5. **Validate class merging**:
   - Uses `cn()` from `@/lib/utils` — never raw string concatenation
   - Uses CVA for components with multiple visual variants
   - Uses Lucide React for icons (not inline SVG)

6. **Check React patterns**:
   - Pages use default exports, subcomponents use named exports
   - Props interfaces defined before components
   - `import type` used for type-only imports
   - No `any` or `@ts-ignore`

7. **Check test coverage**:
   - Page has co-located `*.test.tsx` file
   - Test imports `@/test/mocks` as first import
   - Factory exists in `test/factories/` for page props

8. **Validate color palette consistency**:
   - Primary actions: blue (`bg-blue-600`, `text-blue-600`)
   - Success/positive: green (`bg-green-600`, `text-green-600`)
   - Danger/destructive: red (`bg-red-600`, `text-red-600`)
   - Neutral/secondary: gray scale
   - Warnings: amber/yellow

9. **Check spacing and layout**:
   - Consistent padding (p-4, p-6 for cards)
   - Consistent gaps (gap-2, gap-3, gap-4)
   - Proper rounded corners (rounded-lg for cards, rounded-md for inputs)
   - Consistent shadow usage (shadow-sm for cards)

10. **Review typography**:
    - Headings use proper font weights (font-medium, font-semibold, font-bold)
    - Text sizes are consistent (text-sm, text-base, text-lg)
    - Muted text uses gray-500/gray-400 dark variants

## Common Issues to Flag

1. **Native HTML elements** — `<button>`, `<select>` without UI component wrappers
2. **Missing `cn()`** — string concatenation for class names
3. **Missing dark mode variants** for colors
4. **Missing co-located test** for page components
5. **Missing `import '@/test/mocks'`** in test files
6. **Missing factory** in `test/factories/` for page props
7. **Default exports** on subcomponents (should be named exports)
8. **`any` or `@ts-ignore`** in TypeScript code
9. **Inline SVG** instead of Lucide React icons
10. **Missing `preserveScroll`** on Inertia router actions

## Output Format

```
## UI Review: <file-or-component>

### Issues Found
1. **[SEVERITY]** <description>
   - Location: line X
   - Current: `<current code>`
   - Suggested: `<fixed code>`

### Summary
- Total issues: X
- Critical: X (native components, missing dark mode, missing tests)
- Minor: X (spacing, transitions)

### Recommendations
- ...
```

## Example Usage

```
/ui-review
/ui-review resources/js/pages/Dashboard/Index.tsx
/ui-review resources/js/pages/Order/components/ItemsCard.tsx
```

## Checklist
- [ ] No native HTML elements without UI component wrappers
- [ ] Uses `cn()` for class merging
- [ ] All elements have dark mode support
- [ ] Interactive elements have cursor-pointer
- [ ] Hover and focus states defined
- [ ] Consistent color palette usage
- [ ] Proper spacing and typography
- [ ] Co-located test file exists
- [ ] Test imports `@/test/mocks` first
- [ ] Factory exists for page props
- [ ] Uses Lucide React for icons
