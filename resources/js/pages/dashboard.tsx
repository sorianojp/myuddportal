import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import type { PageProps } from '@/types';
import HeadingSmall from '@/components/heading-small';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

export default function Dashboard() {
  const { auth } = usePage<PageProps>().props;

  if (!auth.user) return null; // ✅ null check avoids unsafe assertion

  const user = auth.user;

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      <div className="px-4 py-6 space-y-6">
        <HeadingSmall
          title={`Welcome, ${user.full_name} (${user.ID_NUMBER})`}
          description={`${user.course.COURSE_NAME} (${user.course.COURSE_CODE})`}
        />
      </div>
    </AppLayout>
  );
}
