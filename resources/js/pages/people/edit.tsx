import UserForm from '@/components/forms/user-form';
import Heading from '@/components/heading';
import AppLayout from '@/layouts/app-layout';
import { Role, User, type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: route('users.index'),
    },
    {
        title: 'Edit User',
        href: route('users.create'),
    },
];

interface UserEditProps {
    roles: Role[];
    user: User;
}

export default function PersonEdit({ roles, user }: UserEditProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <Heading title="Edit User" />
                <UserForm roles={roles} user={user} type="update" />
            </div>
        </AppLayout>
    );
}
