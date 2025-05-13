import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Role } from '@/types';
import { useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

type CreateForm = {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    role_ids: number[];
};

interface CreateUserFormProps {
    roles: Role[];
}

export default function CreateUserForm({ roles }: CreateUserFormProps) {
    const { data, setData, post, processing, errors, reset } = useForm<Required<CreateForm>>({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_ids: [],
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('users.store'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <>
            <form className="flex flex-col gap-6" onSubmit={submit}>
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="name">Name</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autoFocus
                            tabIndex={1}
                            autoComplete="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            disabled={processing}
                            placeholder="Full name"
                        />
                        <InputError message={errors.name} className="mt-2" />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            tabIndex={2}
                            autoComplete="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            disabled={processing}
                            placeholder="email@example.com"
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="password">Password</Label>
                        <Input
                            id="password"
                            type="password"
                            required
                            tabIndex={3}
                            autoComplete="new-password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            disabled={processing}
                            placeholder="Password"
                        />
                        <InputError message={errors.password} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="password_confirmation">Confirm password</Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            required
                            tabIndex={4}
                            autoComplete="new-password"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            disabled={processing}
                            placeholder="Confirm password"
                        />
                        <InputError message={errors.password_confirmation} />
                    </div>
                    <div className="grid gap-2">
                        <Label>Roles</Label>
                        <div className="grid grid-cols-2 gap-2">
                            {roles.map((role: Role) => (
                                <div key={role.id} className="flex flex-row items-start space-y-0 space-x-3 rounded-md border p-3">
                                    <Label htmlFor={`role-${role.id}`} className="flex items-center gap-2">
                                        <Checkbox
                                            key={role.id}
                                            id={`role-${role.id}`}
                                            checked={data.role_ids?.includes(role.id)}
                                            onCheckedChange={(checked) => {
                                                if (checked) {
                                                    setData('role_ids', [...data.role_ids, role.id]);
                                                } else {
                                                    setData(
                                                        'role_ids',
                                                        data.role_ids.filter((r) => r !== role.id),
                                                    );
                                                }
                                            }}
                                        />
                                        {role.name}
                                    </Label>
                                </div>
                            ))}
                        </div>
                        <InputError message={errors.role_ids} />
                    </div>

                    <Button type="submit" className="mt-2 w-full" tabIndex={5} disabled={processing}>
                        {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                        Create account
                    </Button>
                </div>
            </form>
        </>
    );
}
