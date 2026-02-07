import { Head } from '@inertiajs/react';
import { AppLayout } from '@/layouts/AppLayout';

export default function Index() {
    return (
        <AppLayout
            title="Dashboard"
            breadcrumbs={[{ label: 'Dashboard' }]}
        >
            <Head title="Dashboard" />
            <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Welcome
                </h2>
                <p className="mt-2 text-gray-600 dark:text-gray-400">
                    Your application is ready.
                </p>
            </div>
        </AppLayout>
    );
}
