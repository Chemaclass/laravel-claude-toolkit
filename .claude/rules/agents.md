# Agent Delegation Rules

## When to Delegate to Agents

| Situation | Agent to Use |
|-----------|--------------|
| Planning new features | `domain-architect` |
| Writing tests first | `tdd-coach` |
| Reviewing code quality | `clean-code-reviewer` |
| Security assessment | `security-reviewer` |
| Changelog maintenance | `changelog-keeper` |
| Fast codebase search | `explorer` |
| React frontend review | `react-reviewer` |

## Agent Delegation Protocol

1. **Identify the need** - What specialized knowledge is required?
2. **Select appropriate agent** - Match task to agent expertise
3. **Provide clear context** - What files, requirements, constraints?
4. **Review agent output** - Validate recommendations before applying

## Agent Capabilities

### domain-architect
- DDD tactical patterns guidance
- Module boundary decisions
- Inter-module communication strategies
- Hexagonal architecture compliance

### tdd-coach
- Red-green-refactor workflow
- Test structure and naming
- Coverage analysis
- Test isolation guidance

### clean-code-reviewer
- SOLID principle violations
- Code smell detection
- Refactoring suggestions
- Naming improvements

### security-reviewer
- OWASP Top 10 analysis
- Laravel security best practices
- Dependency vulnerability scanning
- Credential/secret detection

### changelog-keeper
- Changelog format maintenance
- User-focused entry writing
- Change categorization (Feature/Improvement/Fix/Breaking)
- Date section management

### explorer
- Fast read-only codebase search
- File pattern matching
- Dependency mapping between modules
- Directory structure analysis

### react-reviewer
- React pattern compliance
- TypeScript quality checks
- UI component consistency
- Dark mode and accessibility
- Performance patterns

## Usage Examples

```
# For architecture decisions
"Use domain-architect to review this module boundary"

# For test-driven development
"Follow tdd-coach guidance for this feature"

# For code quality
"Have clean-code-reviewer analyze this PR"

# For security concerns
"Run security-reviewer on the auth module"

# For changelog updates
"Have changelog-keeper update the changelog"

# For fast codebase search
"Use explorer to find all repository implementations"

# For React review
"Have react-reviewer check the dashboard components"
```
