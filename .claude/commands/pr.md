# Create Pull Request

Create a PR with auto-generated description and changelog update.

## Arguments
- `$ARGUMENTS` - PR title (optional, will generate from branch if empty)

## Instructions

1. Get current branch:
```bash
   git branch --show-current
```

2. Get commits since main:
```bash
   git log main..HEAD --oneline
```

3. Check if CHANGELOG.md needs updating for these changes. If yes, update it.

4. If changelog was modified, commit it:
```bash
   git add CHANGELOG.md
   git commit -m "docs: update changelog"
```

5. Push branch:
```bash
   git push -u origin HEAD
```

6. Create PR with generated description:
```bash
   gh pr create --title "$ARGUMENTS" --body "## Changes\n\n$(git log main..HEAD --oneline)\n\n## Testing\n\n- [ ] Tests pass\n- [ ] Code reviewed"
```

7. Report the PR URL.
