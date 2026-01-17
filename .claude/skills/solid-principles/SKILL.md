# SOLID Principles Skill

## Activation Triggers
- Refactoring discussions
- Code review requests
- Design pattern questions
- "How should I structure this?" questions

## SOLID Overview

| Principle | Summary | Key Question |
|-----------|---------|--------------|
| **S**ingle Responsibility | One class, one reason to change | "What is the ONE thing this class does?" |
| **O**pen/Closed | Open for extension, closed for modification | "Can I add behavior without changing existing code?" |
| **L**iskov Substitution | Subtypes must be substitutable | "Can I use any subclass where the parent is expected?" |
| **I**nterface Segregation | Many specific interfaces > one general | "Does every implementer use every method?" |
| **D**ependency Inversion | Depend on abstractions | "Am I depending on interfaces or implementations?" |

---

## Single Responsibility Principle (SRP)

> A class should have only one reason to change.

### Violation Example
```php
// BAD: Multiple responsibilities
class UserService
{
    public function createUser(array $data): User
    {
        // Validation - responsibility 1
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }

        // Persistence - responsibility 2
        $user = new User($data);
        $this->db->insert('users', $user->toArray());

        // Notification - responsibility 3
        $this->mailer->send($user->email, new WelcomeEmail());

        return $user;
    }
}
```

### Refactored
```php
// GOOD: Single responsibility each
class CreateUserHandler
{
    public function __construct(
        private UserRepository $repository,
        private EventDispatcher $events,
    ) {}

    public function __invoke(CreateUser $command): void
    {
        $user = User::create(
            UserId::generate(),
            Email::fromString($command->email),  // Validation in VO
        );

        $this->repository->save($user);
        $this->events->dispatch(new UserCreated($user->id()));
    }
}

class SendWelcomeEmailOnUserCreated
{
    public function __invoke(UserCreated $event): void
    {
        // Only handles email sending
    }
}
```

---

## Open/Closed Principle (OCP)

> Open for extension, closed for modification.

### Violation Example
```php
// BAD: Must modify to add new payment type
class PaymentProcessor
{
    public function process(Order $order): void
    {
        match ($order->paymentType()) {
            'credit_card' => $this->processCreditCard($order),
            'paypal' => $this->processPaypal($order),
            'apple_pay' => $this->processApplePay($order),
            // Add new types here = modification
        };
    }
}
```

### Refactored
```php
// GOOD: Extend via interface
interface PaymentGateway
{
    public function supports(PaymentType $type): bool;
    public function process(Order $order): PaymentResult;
}

class CreditCardGateway implements PaymentGateway { ... }
class PaypalGateway implements PaymentGateway { ... }
class ApplePayGateway implements PaymentGateway { ... }  // Extension!

class PaymentProcessor
{
    /** @param PaymentGateway[] $gateways */
    public function __construct(private array $gateways) {}

    public function process(Order $order): PaymentResult
    {
        foreach ($this->gateways as $gateway) {
            if ($gateway->supports($order->paymentType())) {
                return $gateway->process($order);
            }
        }
        throw new UnsupportedPaymentTypeException();
    }
}
```

---

## Liskov Substitution Principle (LSP)

> Objects of a superclass should be replaceable with objects of its subclasses.

### Violation Example
```php
// BAD: Rectangle/Square problem
class Rectangle
{
    protected int $width;
    protected int $height;

    public function setWidth(int $width): void { $this->width = $width; }
    public function setHeight(int $height): void { $this->height = $height; }
    public function area(): int { return $this->width * $this->height; }
}

class Square extends Rectangle
{
    public function setWidth(int $width): void
    {
        $this->width = $width;
        $this->height = $width;  // Breaks LSP!
    }
}

// This breaks:
function calculateArea(Rectangle $rect): int
{
    $rect->setWidth(5);
    $rect->setHeight(10);
    return $rect->area();  // Expects 50, Square returns 100
}
```

### Refactored
```php
// GOOD: Separate types, no inheritance
interface Shape
{
    public function area(): int;
}

final readonly class Rectangle implements Shape
{
    public function __construct(
        private int $width,
        private int $height,
    ) {}

    public function area(): int
    {
        return $this->width * $this->height;
    }
}

final readonly class Square implements Shape
{
    public function __construct(
        private int $side,
    ) {}

    public function area(): int
    {
        return $this->side * $this->side;
    }
}
```

---

## Interface Segregation Principle (ISP)

> No client should be forced to depend on methods it doesn't use.

### Violation Example
```php
// BAD: Fat interface
interface Worker
{
    public function work(): void;
    public function eat(): void;
    public function sleep(): void;
}

class Robot implements Worker
{
    public function work(): void { /* OK */ }
    public function eat(): void { /* Robots don't eat! */ }
    public function sleep(): void { /* Robots don't sleep! */ }
}
```

### Refactored
```php
// GOOD: Segregated interfaces
interface Workable
{
    public function work(): void;
}

interface Eatable
{
    public function eat(): void;
}

interface Sleepable
{
    public function sleep(): void;
}

class Human implements Workable, Eatable, Sleepable { ... }
class Robot implements Workable { ... }  // Only what it needs
```

---

## Dependency Inversion Principle (DIP)

> High-level modules should not depend on low-level modules. Both should depend on abstractions.

### Violation Example
```php
// BAD: High-level depends on low-level
class OrderService
{
    private MySqlOrderRepository $repository;  // Concrete!

    public function __construct()
    {
        $this->repository = new MySqlOrderRepository();  // Instantiation!
    }
}
```

### Refactored
```php
// GOOD: Both depend on abstraction
interface OrderRepository  // Abstraction
{
    public function save(Order $order): void;
    public function findById(OrderId $id): ?Order;
}

class OrderService  // High-level
{
    public function __construct(
        private OrderRepository $repository,  // Depends on abstraction
    ) {}
}

class MySqlOrderRepository implements OrderRepository { ... }  // Low-level
class InMemoryOrderRepository implements OrderRepository { ... }  // For tests
```

---

## Quick Reference

### Code Smell → Principle Violated

| Smell | Likely Violation |
|-------|------------------|
| Class does too much | SRP |
| Switch on type | OCP |
| Type checking with `instanceof` | LSP |
| Empty method implementations | ISP |
| `new` in business logic | DIP |
| Hard to test | DIP |
| Changes ripple through codebase | SRP, OCP |

### Refactoring Patterns

| Problem | Pattern |
|---------|---------|
| Multiple responsibilities | Extract Class |
| Switch on type | Strategy Pattern |
| Fat interface | Interface Segregation |
| Concrete dependencies | Dependency Injection |
| Complex conditionals | Replace with Polymorphism |

## When to Apply

- **Always** for Domain and Application layers
- **Usually** for Infrastructure (some pragmatism OK)
- **Judgment** for simple scripts/prototypes

Remember: SOLID is a guide, not a dogma. The goal is maintainable, testable code.
