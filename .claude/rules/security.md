# Security Rules

## Pre-Commit Security Checks

Before any commit, verify:

1. No hardcoded secrets (API keys, passwords, tokens)
2. All user inputs validated via Form Requests
3. SQL injection prevention (Eloquent or parameterized queries)
4. XSS prevention (Blade escaping `{{ }}` not `{!! !!}`)
5. CSRF protection enabled on routes
6. Authentication/authorization verified (middleware, policies)
7. Rate limiting on public endpoints
8. Error messages don't leak sensitive data

## Secret Management

```php
// BAD - Never do this
$apiKey = "sk-proj-xxxxx";

// GOOD - Use environment variables
$apiKey = config('services.api.key');
if (!$apiKey) {
    throw new RuntimeException('API key not configured');
}
```

## Common Vulnerabilities to Check

| Vulnerability | Prevention |
|---------------|------------|
| SQL Injection | Use Eloquent or prepared statements |
| XSS | Use `{{ $var }}` not `{!! $var !!}` |
| CSRF | Verify `@csrf` in forms, exclude only APIs |
| Mass Assignment | Define `$fillable` or `$guarded` |
| Path Traversal | Validate file paths, use Storage facade |
| Insecure Deserialization | Never `unserialize()` user input |

## Incident Response

When a vulnerability is discovered:

1. Stop current work immediately
2. Assess severity and impact
3. Fix critical vulnerabilities before any other work
4. Rotate any exposed credentials
5. Audit codebase for similar issues
6. Document the fix and prevention measures
