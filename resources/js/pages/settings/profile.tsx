import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { Head, usePage } from '@inertiajs/react';
import HeadingSmall from '@/components/heading-small';
import type { PageProps, User } from '@/types';

const breadcrumbs = [
    {
        title: 'Profile information',
        href: route('profile.view')
    },
];

export default function Profile() {
    const { auth } = usePage<PageProps>().props;
    const user = auth.user as User;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Profile" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Profile info" description="Your personal profile details" />

                    <div className="space-y-2 text-sm">
                        <div className="flex items-center gap-2">
                            <span className="w-40 font-medium text-neutral-600">Full name:</span>
                            <span>{user.full_name}</span>
                        </div>
                        <div className="flex items-center gap-2">
                            <span className="w-40 font-medium text-neutral-600">Username:</span>
                            <span>{user?.USER_ID}</span>
                        </div>
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
