# Refactor Check

Analyze code for SOLID violations, clean code issues, and refactoring opportunities.

## Arguments
- `$ARGUMENTS` - File path or directory to analyze

## Instructions

1. **Read the specified file(s)** from `$ARGUMENTS`

2. **Analyze for SOLID violations:**

### Single Responsibility Principle (SRP)
- Does the class have more than one reason to change?
- Are there methods that don't relate to the class's main purpose?
- Is the class doing too much?

**Symptoms:**
- Class has many methods
- Methods operate on different data
- Hard to name the class without using "And" or "Manager"

### Open/Closed Principle (OCP)
- Can new behavior be added without modifying existing code?
- Are there switch/if-else chains that grow with new features?

**Symptoms:**
- Modifying existing code to add features
- Large switch statements on type
- Frequent changes to the same files

### Liskov Substitution Principle (LSP)
- Can derived classes be used interchangeably with base classes?
- Do subclasses throw unexpected exceptions?
- Do subclasses have different preconditions?

**Symptoms:**
- Type checking with instanceof
- Overridden methods that do nothing
- Subclasses that break parent behavior

### Interface Segregation Principle (ISP)
- Are interfaces small and focused?
- Do implementers use all interface methods?

**Symptoms:**
- Classes with empty method implementations
- "Fat" interfaces with many methods
- Methods that throw "not implemented"

### Dependency Inversion Principle (DIP)
- Do high-level modules depend on abstractions?
- Are concrete classes injected directly?

**Symptoms:**
- `new` keyword in business logic
- Direct database/HTTP calls in domain
- Hard to test due to dependencies

3. **Analyze for Clean Code issues:**

### Naming
- Are names descriptive and intention-revealing?
- Do names avoid abbreviations?
- Do class names use nouns, method names use verbs?

### Functions
- Are functions small (< 20 lines)?
- Do functions do one thing?
- Are there too many arguments (> 3)?
- Is there deep nesting?

### Comments
- Are there comments explaining "what" instead of "why"?
- Is there commented-out code?
- Are there outdated comments?

### Duplication
- Is there repeated code (DRY violation)?
- Are there similar methods that could be extracted?

### Error Handling
- Are exceptions used instead of error codes?
- Is error handling separated from business logic?
- Are errors specific and informative?

4. **Check Modular Monolith Architecture compliance:**

### Module Structure
- Is each module self-contained with Domain, Application, Infrastructure?
- Are inter-module dependencies explicit?

### Domain Layer (`modules/<Module>/Domain/`)
- Contains only pure PHP?
- No framework dependencies?
- No database/HTTP concerns?

### Application Layer (`modules/<Module>/Application/`)
- Only orchestrates domain objects?
- Depends on interfaces, not implementations?
- No framework concerns except DTO mapping?

### Infrastructure Layer (`modules/<Module>/Infrastructure/`)
- Implements domain interfaces?
- Contains all framework code?
- Properly maps between layers?

5. **Generate report** with:
- Issues found (categorized by type)
- Severity (high/medium/low)
- Specific line numbers
- Suggested refactoring

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
**Suggestion:** Extract email sending to a dedicated `EmailService` class.

## Moderate Issues (Medium Priority)

### [Naming] Method name is unclear
**File:** `modules/Order/Application/Command/ProcessOrder.php:25`
**Problem:** Method `process()` doesn't describe what it processes.
**Suggestion:** Rename to `calculateOrderTotal()` or similar.

## Minor Issues (Low Priority)

### [DRY] Duplicated validation logic
**File:** `modules/User/Domain/ValueObject/Email.php:30-35, modules/Order/Domain/ValueObject/CustomerEmail.php:40-45`
**Problem:** Same email validation appears in two places.
**Suggestion:** Extract to shared `Email` value object or trait.

## Architecture Compliance

| Module | Layer | Status | Notes |
|--------|-------|--------|-------|
| User | Domain | OK | No framework deps |
| User | Application | Warning | Direct DB call in handler |
| User | Infrastructure | OK | Properly isolated |

## Recommended Refactoring Steps

1. Extract `EmailService` from `UserService`
2. Create shared `Email` value object
3. Move DB call from handler to repository
```

## Checklist
- [ ] File(s) read and analyzed
- [ ] SOLID violations identified
- [ ] Clean code issues identified
- [ ] Architecture compliance checked
- [ ] Report generated with actionable suggestions
