# Update Changelog

Update the CHANGELOG.md with recent changes from the current work session.

## Arguments
- `$ARGUMENTS` - Optional: Description of changes (e.g., `feat: add user authentication`)

## Instructions

1. **Read current CHANGELOG.md** to understand existing format and entries

2. **Check today's date section**:
   - If today's section exists, add to it
   - If not, create a new section for today at the top (after the header)

3. **Categorize the change** using these sections:
   - `### Features` - New functionality
   - `### Improvements` - Enhancements to existing features
   - `### Fixes` - Bug fixes
   - `### Breaking Changes` - Changes that break backward compatibility

4. **Write a concise entry**:
   - Use imperative mood ("Add" not "Added")
   - Start with the module/area in bold: `**Module**: Description`
   - Keep it under 100 characters
   - Focus on user impact, not implementation details

5. **Update the file** using Edit tool

## Format Template

```markdown
## YYYY-MM-DD

### Features
- **ModuleName**: Brief description of new feature

### Improvements
- **ModuleName**: Brief description of improvement

### Fixes
- **ModuleName**: Brief description of fix
```

## Examples

Good entries:
- `**User**: Add user registration endpoint`
- `**Order**: Auto-generate order number on creation`
- `**Dashboard**: Fix chart rendering on mobile`

Bad entries:
- `Added stuff to the user module` (too vague)
- `Refactored the CreateUserHandler to use dependency injection...` (too technical)

## Quick Usage

After completing any feature, fix, or improvement:
```
/update-changelog feat: add user profile page
```

Or invoke without arguments to be prompted for details.

## Checklist
- [ ] Entry added under correct date
- [ ] Entry categorized correctly (Feature/Improvement/Fix)
- [ ] Entry is concise and user-focused
- [ ] Module/area is clearly identified
