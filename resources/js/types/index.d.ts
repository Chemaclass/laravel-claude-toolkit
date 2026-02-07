import type { PageProps as InertiaPageProps } from '@inertiajs/core';

export interface User {
    id: string;
    name: string;
    email: string;
}

export interface FlashMessages {
    message?: string;
}

export interface PageProps extends InertiaPageProps {
    auth: {
        user: User | null;
    };
    flash: FlashMessages;
}

declare module '@inertiajs/react' {
    export function usePage<T extends PageProps = PageProps>(): { props: T };
}
