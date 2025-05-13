import { UserDatatable } from '@/components/datatables/users-datatable';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, User } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { PlusCircle } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: route('users.index'),
    },
];

interface UserIndexProps {
    users: User[];
}

export default function UserIndex({ users }: UserIndexProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <Heading title="User Management" />
                <Button asChild>
                    <Link href={route('users.create')}>
                        <PlusCircle className="h-4 w-4" />
                        Create User
                    </Link>
                </Button>
                <UserDatatable users={users} />
            </div>
        </AppLayout>
    );
}
