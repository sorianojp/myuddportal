import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';
import type { SchedulePageProps } from '@/types/schedule';
import HeadingSmall from '@/components/heading-small';
import ScheduleTable from '@/components/ui/ScheduleTable';

export default function SchedulePage() {
  const { schedule } = usePage<SchedulePageProps>().props;

  return (
    <AppLayout breadcrumbs={[{ title: 'Schedule', href: '/schedule' }]}>
      <Head title="My Class Schedule" />
      <div className="px-4 py-6 space-y-6">
        <HeadingSmall
          title="Subject Schedule"
          description="This list contains your enrolled lecture and laboratory schedules."
        />
        <ScheduleTable schedule={schedule} />
      </div>
    </AppLayout>
  );
}
