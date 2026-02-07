import type { ReactNode } from 'react';

interface GuestLayoutProps {
    children: ReactNode;
}

export function GuestLayout({ children }: GuestLayoutProps) {
    return (
        <div className="flex min-h-screen items-center justify-center bg-gray-50 px-4 dark:bg-gray-900">
            <div className="w-full max-w-md">
                {children}
            </div>
        </div>
    );
}
