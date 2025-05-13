import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, User } from '@/types';
import { Head, router } from '@inertiajs/react';
import { Pencil } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: route('users.index'),
    },
    {
        title: 'Show User',
        href: '#',
    },
];

interface UserShowProps {
    user: User;
}

export default function UserShow({ user }: UserShowProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <Heading title={user.name} />
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <CardTitle>User Information</CardTitle>
                        <Button
                            variant="outline"
                            size="sm"
                            className="flex items-center gap-1"
                            onClick={() => router.visit(route('users.edit', user.id))}
                        >
                            <Pencil className="h-4 w-4" />
                            Edit
                        </Button>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <h3 className="text-muted-foreground text-sm font-medium">Full Name</h3>
                                <p className="text-lg">{user.name}</p>
                            </div>
                            <div>
                                <h3 className="text-muted-foreground text-sm font-medium">Email</h3>
                                <p className="text-lg">{user.email}</p>
                            </div>
                            <div>
                                <h3 className="text-muted-foreground text-sm font-medium">Status</h3>
                                <Badge variant="outline">{user.email_verified_at ? 'Verified' : 'Unverified'}</Badge>
                            </div>
                            <div>
                                <h3 className="text-muted-foreground text-sm font-medium">Roles</h3>
                                <div className="mt-1 flex flex-wrap gap-2">
                                    {user.roles.map((role) => (
                                        <Badge key={role.id} variant="secondary">
                                            {role.name}
                                        </Badge>
                                    ))}
                                </div>
                            </div>
                            <div>
                                <h3 className="text-muted-foreground text-sm font-medium">Created At</h3>
                                <p className="text-lg">{user.created_at}</p>
                            </div>
                            <div>
                                <h3 className="text-muted-foreground text-sm font-medium">Last Login</h3>
                                <p className="text-lg">{user.updated_at || 'Never'}</p>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <p className="text-muted-foreground text-sm">User ID: {user.id}</p>
                    </CardFooter>
                </Card>
            </div>
        </AppLayout>
    );
}
