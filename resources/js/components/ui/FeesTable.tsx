import React from 'react';
import type { Fee } from '@/types/fee';

interface FeesTableProps {
  fees: Fee[];
}
function getSemesterName(semester: number): string {
    switch (semester) {
      case 1:
        return '1st Semester';
      case 2:
        return '2nd Semester';
      case 3:
        return 'Summer';
      default:
        return `Semester ${semester}`;
    }
  }

// Helper to group fees by SY_FROM, SY_TO, and SEMESTER
function groupFeesByTerm(fees: Fee[]) {
  const groups: Record<string, Fee[]> = {};

  for (const fee of fees) {
    const key = `${fee.SY_FROM}-${fee.SY_TO}-Semester-${fee.SEMESTER}`;
    if (!groups[key]) {
      groups[key] = [];
    }
    groups[key].push(fee);
  }

  return groups;
}

export default function FeesTable({ fees }: FeesTableProps) {
  const groupedFees = groupFeesByTerm(fees);

  if (fees.length === 0) {
    return (
      <div className="px-4 py-4 text-center text-gray-500 dark:text-gray-400 border rounded-md">
        No fee records found.
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {Object.entries(groupedFees).map(([term, termFees]) => {
        const [SY_FROM, SY_TO, , SEMESTER] = term.split('-');

        return (
          <div key={term} className="rounded-md overflow-hidden border">
            <div className="px-4 py-2 bg-gray-100 dark:bg-neutral-800 dark:text-white font-semibold border-b">
            SY {SY_FROM} - {SY_TO} | {getSemesterName(Number(SEMESTER))}
            </div>
            <div className="overflow-x-auto">
              <table className="min-w-full text-sm text-left bg-white text-black dark:bg-neutral-900 dark:text-white">
                <thead className="bg-gray-100 dark:bg-neutral-800">
                  <tr>
                    <th className="px-4 py-2 whitespace-nowrap">Tuition Fee</th>
                    <th className="px-4 py-2 whitespace-nowrap">Miscellaneous Fee</th>
                    <th className="px-4 py-2 whitespace-nowrap">Other Charges</th>
                  </tr>
                </thead>
                <tbody>
                  {termFees.map((fee) => (
                    <tr key={fee.fee_hist_index} className="hover:bg-gray-50 dark:hover:bg-neutral-800 border-t">
                      <td className="px-4 py-2">₱{Number(fee.TOTAL_TUITION).toFixed(2)}</td>
                      <td className="px-4 py-2">₱{Number(fee.TOTAL_MISC).toFixed(2)}</td>
                      <td className="px-4 py-2">₱{Number(fee.TOTAL_OTHER).toFixed(2)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        );
      })}
    </div>
  );
}
