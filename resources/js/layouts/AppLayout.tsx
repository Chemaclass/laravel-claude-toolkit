import type { ReactNode } from 'react';

interface BreadcrumbItem {
    label: string;
    href?: string;
}

interface AppLayoutProps {
    title?: string;
    breadcrumbs?: BreadcrumbItem[];
    headerActions?: ReactNode;
    children: ReactNode;
}

export function AppLayout({ title, breadcrumbs, headerActions, children }: AppLayoutProps) {
    return (
        <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
            <header className="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <div className="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <div>
                        {breadcrumbs && breadcrumbs.length > 0 && (
                            <nav className="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                {breadcrumbs.map((crumb, i) => (
                                    <span key={i}>
                                        {i > 0 && <span className="mx-1">/</span>}
                                        {crumb.href ? (
                                            <a
                                                href={crumb.href}
                                                className="hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                                            >
                                                {crumb.label}
                                            </a>
                                        ) : (
                                            <span>{crumb.label}</span>
                                        )}
                                    </span>
                                ))}
                            </nav>
                        )}
                        {title && (
                            <h1 className="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                {title}
                            </h1>
                        )}
                    </div>
                    {headerActions && <div>{headerActions}</div>}
                </div>
            </header>
            <main className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                {children}
            </main>
        </div>
    );
}
