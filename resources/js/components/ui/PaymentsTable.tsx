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
              <th className="px-4 py-2 whitespace-nowrap">OR Number</th>
              <th className="px-4 py-2 whitespace-nowrap">Fee Name</th>
              <th className="px-4 py-2 whitespace-nowrap">Date Paid</th>
              <th className="px-4 py-2 whitespace-nowrap">Amount</th>
              <th className="px-4 py-2 whitespace-nowrap">Amount Tendered</th>
              <th className="px-4 py-2 whitespace-nowrap">Change</th>
            </tr>
          </thead>
          <tbody>
            {payments.length === 0 ? (
              <tr>
                <td colSpan={5} className="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                  No payment records found.
                </td>
              </tr>
            ) : (
              payments.map((payment) => (
                <tr key={payment.PAYMENT_INDEX} className="hover:bg-gray-50 dark:hover:bg-neutral-800 border-t">
                  <td className="px-4 py-2">{payment.OR_NUMBER}</td>
                  <td className="px-4 py-2">{payment.otherSchoolFee?.FEE_NAME ?? '—'}</td>
                  <td className="px-4 py-2">{payment.DATE_PAID}</td>
                  <td className="px-4 py-2">₱{Number(payment.AMOUNT).toFixed(2)}</td>
                  <td className="px-4 py-2">₱{Number(payment.AMOUNT_TENDERED).toFixed(2)}</td>
                  <td className="px-4 py-2">₱{Number(payment.AMOUNT_CHANGE).toFixed(2)}</td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
