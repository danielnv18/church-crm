import { DeleteConfirmDialog } from '@/components/delete-confirm-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Person } from '@/types';
import { router } from '@inertiajs/react';
import { Eye, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface PeopleTableProps {
    people: Person[];
}

export function PeopleDatatable({ people }: PeopleTableProps) {
    const [personToDelete, setPersonToDelete] = useState<number | null>(null);
    const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);

    const handleView = (id: number) => {
        router.visit(route('people.show', id));
    };

    const handleUpdate = (id: number) => {
        router.visit(route('people.edit', id));
    };

    const handleDeleteClick = (id: number) => {
        setPersonToDelete(id);
        setIsDeleteDialogOpen(true);
    };

    const confirmDelete = () => {
        if (personToDelete) {
            setIsDeleteDialogOpen(false);
            setPersonToDelete(null);
            router.delete(route('people.destroy', personToDelete), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    // Optionally, you can show a success message or perform any other action
                },
            });
        }
    };

    const cancelDelete = () => {
        setIsDeleteDialogOpen(false);
        setPersonToDelete(null);
    };

    return (
        <>
            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Marital</TableHead>
                            <TableHead className="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {people.map((person) => (
                            <TableRow key={person.id}>
                                <TableCell className="font-medium">
                                    {person.first_name} {person.last_name}
                                </TableCell>
                                <TableCell>{person.email}</TableCell>
                                <TableCell>{person.civil_status}</TableCell>
                                <TableCell className="text-right">
                                    <div className="flex justify-end gap-2">
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            onClick={() => handleView(person.id)}
                                            aria-label={`View details for ${person.first_name} ${person.last_name}`}
                                        >
                                            <Eye className="h-4 w-4" />
                                        </Button>
                                        <Button variant="outline" size="icon" onClick={() => handleUpdate(person.id)} aria-label={`Edit ${person.first_name} ${person.last_name}`}>
                                            <Pencil className="h-4 w-4" />
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            onClick={() => handleDeleteClick(person.id)}
                                            aria-label={`Delete ${person.first_name} ${person.last_name}`}
                                            className="text-red-500 hover:bg-red-50 hover:text-red-700"
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>

            <DeleteConfirmDialog
                isOpen={isDeleteDialogOpen}
                onClose={cancelDelete}
                onConfirm={confirmDelete}
                title="Delete User"
                description="Are you sure you want to delete this user? This action cannot be undone."
            />
        </>
    );
}
