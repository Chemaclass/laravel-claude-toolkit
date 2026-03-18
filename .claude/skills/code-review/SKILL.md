# Code Review Command

Review code changes for quality, architecture compliance, and potential issues.

## Arguments

- `$ARGUMENTS` - Optional: specific files or directories to review

## Workflow

1. **Gather Changes**
   ```bash
   git diff --name-only HEAD~1  # Recent changes
   git diff main...HEAD         # All branch changes
   ```

2. **Review Checklist**

   ### Architecture Compliance
   - [ ] Domain layer has no infrastructure dependencies
   - [ ] Application layer only depends on Domain
   - [ ] No cross-module infrastructure imports
   - [ ] Interfaces defined in Domain, implemented in Infrastructure

   ### Code Quality
   - [ ] Meaningful names for classes, methods, variables
   - [ ] Methods under 20 lines
   - [ ] Files under 500 lines
   - [ ] No code smells (feature envy, primitive obsession, etc.)

   ### Testing
   - [ ] Tests exist for new/changed code
   - [ ] Tests follow naming conventions
   - [ ] Tests are isolated and repeatable

   ### Security
   - [ ] No hardcoded secrets
   - [ ] User input validated
   - [ ] Proper error handling (no sensitive data leaks)

3. **Output Format**

   ```markdown
   ## Code Review: [Branch/Commit]

   ### Summary
   [Brief description of changes]

   ### Issues Found

   #### Critical
   - [file:line] Description

   #### Suggestions
   - [file:line] Description

   ### Positive Notes
   - [What was done well]
   ```

4. **If issues found**, provide specific remediation examples
