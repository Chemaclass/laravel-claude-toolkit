---
globs: resources/js/**/*.{tsx,ts}
---

# MANDATORY: Frontend Design Consistency

**When modifying ANY frontend file, you MUST follow these rules.**

## Never Use Native HTML When Styled Components Exist

| Instead of | Use | Import from |
|------------|-----|-------------|
| `<button>` | `<Button>` | `@/components/ui/button` |
| `<select>` | `<Select>` | `@/components/ui/select` |
| `window.alert/confirm` | `<Dialog>` | `@/components/ui/dialog` |
| Native tooltip | `<Tooltip>` | `@/components/ui/tooltip` |
| Status pill | `<Badge>` | `@/components/ui/badge` |
| `<div>` card | `<Card>` | `@/components/ui/card` |
| Empty placeholder | `<EmptyState>` | `@/components/ui/empty-state` |
| Loading placeholder | `<Skeleton>` | `@/components/ui/skeleton` |
| Dropdown/action menu | `<DropdownMenu>` | `@/components/ui/dropdown-menu` |
| Notification/message | `<Alert>` | `@/components/ui/alert` |

## Class Merging

- Always use `cn()` from `@/lib/utils` for class merging — never raw string concatenation
- Use CVA (`class-variance-authority`) for components with multiple visual variants
- Use Lucide React for icons (`h-4 w-4` inline, `h-5 w-5` card titles, `h-12 w-12` empty states)

## Always Include Dark Mode Support

- Every `bg-*` needs a `dark:bg-*` variant
- Every `text-*` needs a `dark:text-*` variant
- Every `border-*` needs a `dark:border-*` variant

## Loading State Pattern

When a `<Button>` has a `loading` prop, the spinner **replaces** the original icon — it never appears alongside it. This is handled automatically by the Button component: child SVG icons are hidden via `[&>svg:not(.animate-spin)]:hidden` when `loading` is true.

- Always use `<Button loading={isLoading}>` — never add a manual spinner
- The icon inside children is automatically hidden during loading
- Text labels remain visible next to the spinner
- Example: `<Button loading={saving}><Save className="h-4 w-4" /> Save</Button>` shows `[spinner] Save` while loading

## All Interactive Elements Must Have

- `cursor-pointer` class
- Hover states (`hover:*` classes)
- Focus states (`focus:ring-*` classes)
- Smooth transitions (`transition-colors` or `transition-all`)

## Use Consistent Styling

- Cards: `rounded-lg shadow-sm border border-gray-200 dark:border-gray-700`
- Inputs: `rounded-md` with proper focus rings
- Spacing: Use Tailwind scale (gap-2, gap-3, p-4, p-6)

## Available UI Components

Located in `resources/js/components/ui/`.

| Component | Import | Usage |
|-----------|--------|-------|
| `<Button>` | `@/components/ui/button` | All clickable actions (variants: primary, secondary, danger, ghost, outline, link) |
| `<Card>` | `@/components/ui/card` | Content containers (Card, CardHeader, CardTitle, CardContent, CardFooter) |
| `<Badge>` | `@/components/ui/badge` | Status indicators, tags (variants: default, secondary, success, danger, warning, outline) |
| `<Dialog>` | `@/components/ui/dialog` | Dialogs, confirmations (Dialog, DialogContent, DialogHeader, DialogTitle) |
| `<DropdownMenu>` | `@/components/ui/dropdown-menu` | Action menus |
| `<Select>` | `@/components/ui/select` | Selection menus (Select, SelectTrigger, SelectContent, SelectItem) |
| `<Input>` | `@/components/ui/input` | Text inputs with label, error, hint |
| `<Textarea>` | `@/components/ui/textarea` | Multiline inputs with label, error |
| `<Tooltip>` | `@/components/ui/tooltip` | Hover hints (Tooltip, TooltipTrigger, TooltipContent) |
| `<Alert>` | `@/components/ui/alert` | Notifications, messages |
| `<EmptyState>` | `@/components/ui/empty-state` | No data placeholders (icon, title, description, action) |
| `<Skeleton>` | `@/components/ui/skeleton` | Loading placeholders |

## Color Palette

| Purpose | Light Mode | Dark Mode |
|---------|------------|-----------|
| Primary actions | `bg-blue-600`, `text-blue-600` | `dark:bg-blue-500`, `dark:text-blue-400` |
| Success/positive | `bg-green-600`, `text-green-600` | `dark:bg-green-500`, `dark:text-green-400` |
| Danger/destructive | `bg-red-600`, `text-red-600` | `dark:bg-red-500`, `dark:text-red-400` |
| Warnings | `bg-amber-500`, `text-amber-600` | `dark:bg-amber-400`, `dark:text-amber-400` |
| Neutral/secondary | `bg-gray-100`, `text-gray-700` | `dark:bg-gray-700`, `dark:text-gray-300` |
| Muted text | `text-gray-500` | `dark:text-gray-400` |

## Design Principles

- Keep UI uniform and modern across the entire app
- Use consistent spacing (Tailwind spacing scale)
- Use consistent color palette from Tailwind config
- Ensure dark mode support for all components
- Mobile-first responsive design
