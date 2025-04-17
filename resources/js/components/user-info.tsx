import { useInitials } from '@/hooks/use-initials';
import { usePage } from '@inertiajs/react';
import type { PageProps, User } from '@/types';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';

export function UserInfo() {
    const { auth } = usePage<PageProps>().props;
    const user = auth.user as User;
    const getInitials = useInitials();

    if (!user) return null;
    return (
        <>
            <Avatar className="h-8 w-8 overflow-hidden rounded-full">
                <AvatarFallback className="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                    {getInitials(user.full_name)}
                </AvatarFallback>
            </Avatar>
            <div className="grid flex-1 text-left text-sm leading-tight ml-2">
                <span className="truncate font-medium">{user.full_name}</span>
                <span className="text-muted-foreground truncate text-xs">{user.USER_ID}</span>
            </div>
        </>
    );
}
