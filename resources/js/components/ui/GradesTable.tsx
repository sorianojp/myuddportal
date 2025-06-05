import React from 'react';
import type { GradeEntry } from '@/types/grade';

interface GradesTableProps {
  grades: GradeEntry[];
  title: string;
}

export default function GradesTable({ grades, title }: GradesTableProps) {
  return (
    <div className="rounded-md overflow-hidden border">
      <div className="px-4 py-2 bg-gray-100 dark:bg-neutral-800 dark:text-white font-semibold border-b">
        {title}
      </div>
      <div className="overflow-x-auto">
        <table className="min-w-full text-sm text-left bg-white text-black dark:bg-neutral-900 dark:text-white">
          <thead className="bg-gray-100 dark:bg-neutral-800">
            <tr>
              <th className="px-4 py-2 w-24">Subject Code</th>
              <th className="px-4 py-2 w-48">Subject Name</th>
              <th className="px-4 py-2 w-24">Grade Type</th>
              <th className="px-4 py-2 w-24">Grade</th>
              <th className="px-4 py-2 w-24">Credits</th>
              <th className="px-4 py-2 w-24">Remark</th>
              <th className="px-4 py-2 w-48">Encoded By</th>
            </tr>
          </thead>
          <tbody>
            {grades.length === 0 ? (
              <tr>
                <td colSpan={8} className="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                  No grades found.
                </td>
              </tr>
            ) : (
              grades.map((entry, index) => (
                <tr key={index} className="hover:bg-gray-50 dark:hover:bg-neutral-800 border-t">
                  <td className="px-4 py-2">{entry.SUB_CODE}</td>
                  <td className="px-4 py-2">{entry.SUB_NAME}</td>
                  <td className="px-4 py-2">{entry.GRADE_NAME}</td>
                    <td
                    className={`px-4 py-2 font-semibold ${
                        entry.GRADE == null || isNaN(parseFloat(entry.GRADE))
                        ? 'text-gray-900'
                        : parseFloat(entry.GRADE) < 75
                        ? 'text-red-500'
                        : 'text-green-500'
                    }`}
                    >
                    {entry.GRADE == null || isNaN(parseFloat(entry.GRADE)) ? 'N/A' : entry.GRADE}
                    </td>
                  <td className="px-4 py-2">{entry.CREDIT_EARNED}</td>
                  <td className="px-4 py-2 font-semibold">
                  <span className={
                    entry.REMARK === 'Passed' ? 'text-green-500' :
                    entry.REMARK === 'Failed' ? 'text-red-500' :
                    entry.REMARK === 'In Progress' ? 'text-yellow-500' :
                    entry.REMARK === 'NE' ? 'text-blue-500' :
                    'text-gray-500'
                  }>
                    {entry.REMARK ?? 'N/A'}
                  </span>
                  </td>
                  <td className="px-4 py-2">{entry.ENCODED_BY}</td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
