# Refactor Check

Analyze code for SOLID violations, clean code issues, and refactoring opportunities.

## Arguments
- `$ARGUMENTS` - File path or directory to analyze

## Instructions

1. **Read the specified file(s)** from `$ARGUMENTS`

2. **Analyze for SOLID violations**

> See `solid-principles` skill for detailed patterns. Look for these symptoms:

| Principle | Key Symptoms |
|-----------|--------------|
| SRP | Class has many methods, hard to name without "And"/"Manager" |
| OCP | Switch/if-else chains that grow with features |
| LSP | `instanceof` checks, overridden methods that break behavior |
| ISP | Empty method implementations, "not implemented" exceptions |
| DIP | `new` in business logic, hard to test |

3. **Analyze for Clean Code issues:**

### Naming
- Are names descriptive and intention-revealing?
- Do class names use nouns, method names use verbs?

### Functions
- Are functions small (< 20 lines)?
- Do functions do one thing?
- Are there too many arguments (> 3)?

### Comments & Duplication
- Are there comments explaining "what" instead of "why"?
- Is there commented-out code?
- Is there repeated code (DRY violation)?

### Error Handling
- Are exceptions used instead of error codes?
- Are errors specific and informative?

4. **Check Modular Monolith Architecture compliance:**

> See `laravel-hexagonal` skill for layer details.

| Layer | Check |
|-------|-------|
| Domain | Pure PHP? No framework dependencies? |
| Application | Depends on interfaces, not implementations? |
| Infrastructure | Implements domain interfaces? |

5. **Generate report** with issues found, severity, line numbers, and suggested refactoring.

## Output Format

```markdown
# Refactor Analysis: <file/directory>

## Summary
- **SOLID Violations:** X issues
- **Clean Code Issues:** X issues
- **Architecture Issues:** X issues

## Critical Issues (High Priority)

### [SRP] <Class> has multiple responsibilities
**File:** `modules/User/Domain/Entity/User.php:10-50`
**Problem:** This class handles both user validation and email sending.
**Suggestion:** Extract email sending to a dedicated service.

## Moderate Issues (Medium Priority)
...

## Minor Issues (Low Priority)
...

## Architecture Compliance

| Module | Layer | Status | Notes |
|--------|-------|--------|-------|
| User | Domain | OK | No framework deps |

## Recommended Refactoring Steps
1. ...
```

## Checklist
- [ ] File(s) read and analyzed
- [ ] SOLID violations identified
- [ ] Clean code issues identified
- [ ] Architecture compliance checked
- [ ] Report generated with actionable suggestions
