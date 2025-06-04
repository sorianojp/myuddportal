import React from 'react';
import type { Payment } from '@/types/payment';

interface PaymentsTableProps {
  payments: Payment[];
}

export default function PaymentsTable({ payments }: PaymentsTableProps) {
  return (
    <div className="rounded-md overflow-hidden border">
      <div className="px-4 py-2 bg-gray-100 dark:bg-neutral-800 dark:text-white font-semibold border-b">
        Payment History
      </div>
      <div className="overflow-x-auto">
        <table className="min-w-full text-sm text-left bg-white text-black dark:bg-neutral-900 dark:text-white">
          <thead className="bg-gray-100 dark:bg-neutral-800">
            <tr>
              <th className="px-4 py-2 w-32">OR Number</th>
              <th className="px-4 py-2 w-48">Description</th>
              <th className="px-4 py-2 w-24">Date Paid</th>
              <th className="px-4 py-2 w-24">Amount</th>
            </tr>
          </thead>
          <tbody>
            {payments.length === 0 ? (
              <tr>
                <td colSpan={4} className="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                  No payment records found.
                </td>
              </tr>
            ) : (
              payments.map((payment) => (
                <tr key={payment.PAYMENT_INDEX} className="hover:bg-gray-50 dark:hover:bg-neutral-800 border-t">
                  <td className="px-4 py-2">{payment.OR_NUMBER}</td>
                  <td
                    className="px-4 py-2"
                    dangerouslySetInnerHTML={{ __html: payment.DESCRIPTION }}
                  />
                  <td className="px-4 py-2">
                    {new Date(payment.DATE_PAID).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })}
                  </td>
                  <td className="px-4 py-2">₱{Number(payment.AMOUNT).toFixed(2)}</td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
