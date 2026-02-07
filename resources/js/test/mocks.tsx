import { vi } from 'vitest';

// Mock @inertiajs/react
vi.mock('@inertiajs/react', () => ({
    Head: ({ children }: { children?: React.ReactNode }) => <>{children}</>,
    Link: ({ children, ...props }: { children?: React.ReactNode; href: string }) => (
        <a {...props}>{children}</a>
    ),
    Deferred: ({ children }: { children?: React.ReactNode }) => <>{children}</>,
    usePage: () => ({
        props: {
            auth: { user: { id: '1', name: 'Test User', email: 'test@example.com' } },
            flash: {},
        },
    }),
    useForm: (initialData: Record<string, unknown> = {}) => ({
        data: initialData,
        setData: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        patch: vi.fn(),
        delete: vi.fn(),
        processing: false,
        errors: {},
        reset: vi.fn(),
        clearErrors: vi.fn(),
        transform: vi.fn(),
    }),
    router: {
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        patch: vi.fn(),
        delete: vi.fn(),
        reload: vi.fn(),
        visit: vi.fn(),
    },
}));

// Mock axios
vi.mock('axios', () => ({
    default: {
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        patch: vi.fn(),
        delete: vi.fn(),
        create: vi.fn(),
    },
}));
