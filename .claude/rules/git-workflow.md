# Git Workflow Rules

## Commit Message Format

Follow conventional commits:

```
<type>: <description>

[optional body]
```

### Types

| Type | Use For |
|------|---------|
| `feat` | New features |
| `fix` | Bug fixes |
| `refactor` | Code restructuring (no behavior change) |
| `test` | Adding/updating tests |
| `docs` | Documentation changes |
| `chore` | Maintenance tasks |
| `perf` | Performance improvements |
| `ci` | CI/CD changes |

### Examples

```
feat: add user registration endpoint
fix: prevent duplicate email registration
refactor: extract email validation to value object
test: add integration tests for user repository
```

## Pull Request Process

1. **Review all commits** - not just the latest
2. **Run full diff**: `git diff main...HEAD`
3. **Write thorough PR summary** with context
4. **Include test plan** with verification steps
5. **Push with tracking**: `git push -u origin branch-name`

## Feature Development Cycle

### 1. Planning
- Understand requirements fully
- Identify affected modules
- Plan test strategy

### 2. Test-Driven Development
- Write failing test first
- Implement minimum code to pass
- Refactor while green
- Target 80%+ coverage

### 3. Review
- Self-review before PR
- Check for SOLID violations
- Verify no debug code remains

### 4. Integration
- Write descriptive commit message
- Create PR with summary and test plan
- Address review feedback

## Branch Naming

```
feat/user-registration
fix/duplicate-email-bug
refactor/extract-email-vo
test/user-repository
```

## Before Pushing

- [ ] All tests pass
- [ ] No linting errors (`composer lint`)
- [ ] No static analysis errors (`composer phpstan`)
- [ ] Commit message follows convention
- [ ] Branch is up to date with main
