import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';
import type { FeesPageProps } from '@/types/fee';
import HeadingSmall from '@/components/heading-small';
import FeesTable from '@/components/ui/FeesTable';

export default function FeesPage() {
    const { fees } = usePage<FeesPageProps>().props;

    return (
      <AppLayout breadcrumbs={[{ title: 'Fees', href: '/fees' }]}>
        <Head title="Fees" />
        <div className="px-4 py-6 space-y-6">
          <HeadingSmall
            title="Transaction Records"
            description="Below is a summary of your past transactions and receipt information."
          />
          <FeesTable fees={fees} />
        </div>
      </AppLayout>
    );
  }
