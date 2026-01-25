# Performance Rules

## Database Optimization

### N+1 Query Prevention
```php
// BAD: N+1 queries
$users = User::all();
foreach ($users as $user) {
    echo $user->orders->count(); // Query per user
}

// GOOD: Eager loading
$users = User::with('orders')->get();
foreach ($users as $user) {
    echo $user->orders->count(); // No additional queries
}
```

### Query Optimization

| Pattern | Use When |
|---------|----------|
| `select()` | Only need specific columns |
| `chunk()` | Processing large datasets |
| `cursor()` | Memory-constrained iteration |
| `lazy()` | Streaming large results |
| Indexes | Filtering/sorting on columns |

### Caching Strategy

```php
// Cache expensive queries
$users = Cache::remember('active_users', 3600, function () {
    return User::where('active', true)->get();
});

// Cache configuration data
$settings = Cache::rememberForever('app_settings', function () {
    return Setting::all()->pluck('value', 'key');
});
```

## Application Performance

### Avoid in Hot Paths

| Anti-Pattern | Alternative |
|--------------|-------------|
| `collect()->filter()` on large arrays | Array functions or generators |
| Multiple small queries | Single optimized query |
| Synchronous external calls | Queued jobs |
| File I/O in request | Background processing |

### Queue Heavy Operations

```php
// BAD: Blocking request
public function store(Request $request) {
    $this->sendWelcomeEmail($user);     // Slow
    $this->syncToExternalCRM($user);    // Slower
    return response()->json($user);
}

// GOOD: Queue for later
public function store(Request $request) {
    dispatch(new SendWelcomeEmail($user));
    dispatch(new SyncToExternalCRM($user));
    return response()->json($user);
}
```

## Monitoring Checklist

- [ ] Enable query logging in development
- [ ] Profile slow endpoints with Laravel Debugbar
- [ ] Monitor memory usage for batch operations
- [ ] Check for N+1 queries regularly
- [ ] Review queue job durations

## Performance Targets

| Metric | Target |
|--------|--------|
| API Response Time | < 200ms (P95) |
| Database Queries per Request | < 10 |
| Memory per Request | < 128MB |
| Queue Job Duration | < 30s |
