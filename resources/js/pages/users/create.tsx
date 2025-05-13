import Heading from '@/components/heading';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, Role } from '@/types';
import { Head } from '@inertiajs/react';
import UserForm from '@/components/forms/user-form';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: route('users.index'),
    },
    {
        title: 'Create User',
        href: route('users.create'),
    },
];

interface UserCreateProps {
    roles: Role[];
}

export default function UserCreate({ roles }: UserCreateProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <Heading title="Create User" />
                <UserForm roles={roles} type='create' />
            </div>
        </AppLayout>
    );
}
