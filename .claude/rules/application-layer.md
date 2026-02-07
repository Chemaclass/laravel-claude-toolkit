---
globs: modules/*/Application/**/*.php
---

# Application Layer Conventions

The Application layer contains use case handlers. It depends only on the Domain layer.

## Core Rules

- One handler per use case
- Use `__invoke()` for handlers
- Inject repository interfaces, not implementations
- Commands for writes, Queries for reads
- Command/Query DTOs are simple value objects carrying input data

## Command DTO

```php
final readonly class CreateOrder {
    public function __construct(
        public string $id,
        public string $userId,
        public array $items,
        public string $status,
    ) {}
}
```

- `final readonly class` with `public` constructor properties
- Carry primitive types (strings, arrays) — not domain objects
- Domain value objects are created inside the handler

## Command Handler (Write)

```php
final readonly class CreateOrderHandler {
    public function __construct(
        private OrderRepository $repository,
        private EventDispatcher $eventDispatcher,
        private TransactionManager $transactionManager,
    ) {}

    public function __invoke(CreateOrder $command): Order {
        return $this->transactionManager->transaction(function () use ($command): Order {
            $order = Order::create(
                id: OrderId::fromString($command->id),
                status: Status::fromString($command->status),
            );

            $this->repository->save($order);

            $this->eventDispatcher->dispatch(
                OrderCreated::fromOrder($order)
            );

            return $order;
        });
    }
}
```

- Wrap all mutations in `$this->transactionManager->transaction()`
- Convert primitive command args to domain value objects inside handler
- Dispatch domain events after save
- Return the created/updated entity

## Query Handler (Read)

```php
final readonly class GetUserByIdHandler {
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function __invoke(GetUserById $query): User {
        return $this->userRepository->findById(UserId::fromString($query->userId))
            ?? throw UserNotFound::withId($query->userId);
    }
}
```

- Query handlers return domain entities or arrays
- Throw domain exceptions for not-found cases
- No side effects — read only

## Key Interfaces

| Interface | Location | Method |
|-----------|----------|--------|
| `EventDispatcher` | `Shared\Domain\Event` | `dispatch(DomainEvent $event): void` |
| `TransactionManager` | `Shared\Domain\Transaction` | `transaction(callable $callback): mixed` |
| `DomainEvent` | `Shared\Domain\Event` | `occurredOn(): DateTimeImmutable` |
