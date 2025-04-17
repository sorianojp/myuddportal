import {
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { UserInfo } from '@/components/user-info';
import { useMobileNavigation } from '@/hooks/use-mobile-navigation';
import { Link } from '@inertiajs/react';
import { LogOut, Settings } from 'lucide-react';



export function UserMenuContent() {
    const cleanup = useMobileNavigation();

    return (
        <>
            <DropdownMenuLabel className="p-0 font-normal">
                <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                    <UserInfo  />
                </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            {/* You can add other links here later, like "My Grades" or "Change Password" */}
            <DropdownMenuItem asChild>
                    <Link className="block w-full" href={route('profile.view')} as="button" prefetch onClick={cleanup}>
                        <Settings className="mr-2" />
                        Settings
                    </Link>
            </DropdownMenuItem>
            <DropdownMenuItem asChild>
                <Link
                    className="block w-full"
                    method="post"
                    href={route('logout')}
                    as="button"
                    onClick={cleanup}
                >
                    <LogOut className="mr-2" />
                    Log out
                </Link>
            </DropdownMenuItem>
        </>
    );
}
