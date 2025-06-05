import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';
import type { GradesPageProps } from '@/types/grade';
import HeadingSmall from '@/components/heading-small';
import GradesTable from '@/components/ui/GradesTable';

export default function GradesPage() {
  const { finalGroupedGrades } = usePage<GradesPageProps>().props;

  return (
    <AppLayout breadcrumbs={[{ title: 'Grades', href: '/mygrades' }]}>
      <Head title="My Grades" />
      <div className="px-4 py-6 space-y-6">
        <HeadingSmall
          title="All Grades"
          description="Below is the complete list of your grades grouped by school year and semester."
        />

        {Object.entries(finalGroupedGrades).map(([term, grades]) => (
          <GradesTable key={term} title={term} grades={grades} />
        ))}
      </div>
    </AppLayout>
  );
}
