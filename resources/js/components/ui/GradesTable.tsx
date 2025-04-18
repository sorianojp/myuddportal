import React from 'react';
import type { Grade } from '@/types/grade';

interface GradesTableProps {
  term: string;
  grades: Grade[];
}

export default function GradesTable({ term, grades }: GradesTableProps) {
  const [sy, to, semLabel] = term.split('-');

  return (
    <div className="rounded-md overflow-hidden border">
      <div className="px-4 py-2 bg-gray-100 dark:bg-neutral-800 dark:text-white font-semibold border-b">
        School Year: {sy} - {to} | Semester: {semLabel}
      </div>
      <div className="overflow-x-auto">
        <table className="min-w-full text-sm text-left bg-white text-black dark:bg-neutral-900 dark:text-white">
          <thead className="bg-gray-100 dark:bg-neutral-800">
            <tr>
                <th className="px-4 py-2 w-48">Subject Code</th>
              <th className="px-4 py-2 w-48">Subject Name</th>
              <th className="px-4 py-2 w-24">Grade Type</th>
              <th className="px-4 py-2 w-24">Grade</th>
              <th className="px-4 py-2 w-24">Credits</th>
              <th className="px-4 py-2 w-24">Remark</th>
              <th className="px-4 py-2 w-48">Encoded By</th>
            </tr>
          </thead>
          <tbody>
            {grades.map((grade, idx) => (
              <tr key={idx} className="hover:bg-gray-50 dark:hover:bg-neutral-800 border-t">
                                <td className="px-4 py-2">{grade.sub_section?.subject?.SUB_CODE ?? 'N/A'}</td>
                <td className="px-4 py-2">{grade.sub_section?.subject?.SUB_NAME ?? 'N/A'}</td>
                <td className="px-4 py-2">{grade.GRADE_NAME}</td>
                <td className="px-4 py-2">{grade.GRADE}</td>
                <td className="px-4 py-2">{grade.CREDIT_EARNED}</td>
                <td className="px-4 py-2">
                  <span className={
                    grade.remark?.REMARK === 'Passed' ? 'text-green-500' :
                    grade.remark?.REMARK === 'Failed' ? 'text-red-500' :
                    grade.remark?.REMARK === 'In Progress' ? 'text-yellow-500' :
                    grade.remark?.REMARK === 'NE' ? 'text-blue-500' :
                    'text-gray-500'
                  }>
                    {grade.remark?.REMARK ?? 'N/A'}
                  </span>
                </td>
                <td className="px-4 py-2">
                  {[grade.encoded_by_user?.LNAME, grade.encoded_by_user?.FNAME, grade.encoded_by_user?.MNAME]
                    .filter(Boolean)
                    .join(', ')}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
