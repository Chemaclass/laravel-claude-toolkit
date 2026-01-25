# Security Review Command

Perform comprehensive security analysis of the codebase or specific components.

## Arguments

- `$ARGUMENTS` - Optional: specific module, file, or component to review

## Workflow

1. **Automated Scans**
   ```bash
   # Check dependencies for vulnerabilities
   composer audit

   # Run static analysis
   ./vendor/bin/phpstan analyse
   ```

2. **Manual Review Checklist**

   ### Authentication & Authorization
   - [ ] Routes protected with appropriate middleware
   - [ ] Policies defined for resource access
   - [ ] Password hashing uses bcrypt/argon2
   - [ ] Session regeneration on login

   ### Input Validation
   - [ ] All user input validated via Form Requests
   - [ ] File uploads restricted by type and size
   - [ ] No raw SQL with user input
   - [ ] No shell commands with user input

   ### Data Protection
   - [ ] Sensitive data encrypted at rest
   - [ ] No secrets in code or config files
   - [ ] Logging doesn't expose sensitive data
   - [ ] API responses don't leak internal errors

   ### Laravel-Specific
   - [ ] `$fillable` or `$guarded` on all models
   - [ ] CSRF protection enabled
   - [ ] Debug mode disabled in production config
   - [ ] APP_KEY is set and secure

3. **Output Format**

   ```markdown
   ## Security Review: [Scope]

   ### Automated Scan Results
   [composer audit and phpstan output]

   ### Manual Review Findings

   #### Critical (Fix Immediately)
   - [file:line] Vulnerability description
     - Impact: [What could happen]
     - Fix: [How to remediate]

   #### High Priority
   ...

   #### Recommendations
   ...

   ### Verified Secure
   - [List of security controls verified as working]
   ```

4. **Reference the security-reviewer agent** for detailed analysis assistance
