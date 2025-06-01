import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';
import type { PaymentsPageProps } from '@/types/payment';
import HeadingSmall from '@/components/heading-small';
import PaymentsTable from '@/components/ui/PaymentsTable';

export default function PaymentsPage() {
    const { payments } = usePage<PaymentsPageProps>().props;

    return (
      <AppLayout breadcrumbs={[{ title: 'Payments', href: '/payments' }]}>
        <Head title="My Payments" />
        <div className="px-4 py-6 space-y-6">
          <HeadingSmall
            title="Payment History"
            description="This list contains your payments and official receipt details."
          />

          <PaymentsTable payments={payments} />
        </div>
      </AppLayout>
    );
  }
