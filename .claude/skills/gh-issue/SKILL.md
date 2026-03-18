# GitHub Issue Workflow

Fetch a GitHub issue and enter plan mode to implement it.

## Arguments
- `$ARGUMENTS` - Issue number (e.g., `2` or `#2`)

## Instructions

1. **Parse the issue number** from `$ARGUMENTS` (strip `#` if present)

2. **Fetch issue details** using GitHub CLI:
   ```bash
   gh issue view <number> --json title,body,labels,assignees,milestone,state
   ```

3. **Assign yourself if unassigned**:
   ```bash
   gh issue edit <number> --add-assignee @me
   ```

4. **Add appropriate labels** based on issue scope/context:
   ```bash
   gh issue edit <number> --add-label "<label>"
   ```

   **Available labels:**
   - `bug` - Something isn't working
   - `enhancement` - New feature or request
   - `documentation` - Improvements or additions to documentation
   - `help wanted` - Extra attention is needed
   - `question` - Further information is requested

   **Note:** Only add labels if the issue doesn't already have appropriate ones.

5. **Move issue to "In Progress"** in GitHub Project (if configured):
   ```bash
   # Read project config from .claude/github-project.json (if exists)
   # Find the issue's item ID in the project
   ITEM_ID=$(gh project item-list PROJECT_NUMBER --owner OWNER --format json \
     | jq -r '.items[] | select(.content.number == ISSUE_NUMBER) | .id')

   # Move to "In Progress" status
   gh project item-edit \
     --id "$ITEM_ID" \
     --project-id "PROJECT_ID" \
     --field-id "STATUS_FIELD_ID" \
     --single-select-option-id "IN_PROGRESS_OPTION_ID"
   ```

   **Note:** Requires `project` scope. Run `gh auth refresh -s project` if needed.

6. **Analyze the issue**:
   - Understand the requirements from title and body
   - Note any labels (bug, feature, enhancement, etc.)
   - Check if it references other issues or PRs

7. **Enter Plan Mode** to design the implementation:
   - Explore the codebase to understand affected areas
   - Identify files that need changes
   - Consider the architecture (hexagonal, DDD patterns)
   - Plan the TDD approach (what tests to write first)

8. **Create implementation plan** with:
   - Summary of what the issue requires
   - List of files to create/modify
   - Test strategy (unit, integration, feature tests)
   - Step-by-step implementation order

9. **After plan approval**, implement following TDD:
   - Write failing tests first
   - Implement minimum code to pass
   - Refactor while keeping tests green
   - Run `composer test` before completion

10. **Update changelog** after implementation:
    - Add entry under today's date
    - Categorize correctly (Feature/Fix/Improvement)

11. **Verify test coverage** (minimum 95%):
    ```bash
    sail composer test:coverage
    ```

12. **Create commit and push**:
    ```bash
    git add .
    git commit -m "<type>(<scope>): <description>

    Related to #<issue-number>"
    git push
    ```

    **Commit guidelines:**
    - Use conventional commit format (feat, fix, refactor, etc.)
    - Reference the issue number with "Related to #X" (not "Closes")

13. **Move issue to "In Review"** in GitHub Project (if configured):
    ```bash
    # Same pattern as step 5 but with "In Review" status option
    ```

## Example Usage

```
/gh-issue 2
/gh-issue #15
```

## Output Format

After fetching, present the issue like this:

```
## Issue #<number>: <title>

**Labels:** <labels>
**State:** <state>

### Description
<body content>

### Implementation Plan
1. ...
2. ...
```

## Checklist
- [ ] Issue fetched and understood
- [ ] Self-assigned if unassigned
- [ ] Appropriate labels added
- [ ] Issue moved to "In Progress" in GitHub Project (if configured)
- [ ] Codebase explored for context
- [ ] Plan created and approved
- [ ] Tests written first (TDD)
- [ ] Implementation complete
- [ ] `sail composer test` passes
- [ ] Changelog updated
- [ ] Test coverage >= 95%
- [ ] Commit created with issue reference (Related to #X)
- [ ] Changes pushed to remote
- [ ] Issue moved to "In Review" in GitHub Project (if configured)
