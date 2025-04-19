import type { EnrolledSubject } from '@/types/subjectload';

interface SubjectLoadTableProps {
  term: string;
  subjects: EnrolledSubject[];
}

export default function SubjectLoadTable({ term, subjects }: SubjectLoadTableProps) {
  const [sy, semCode] = term.split('-S');
  const [syFrom, syTo] = sy.split('-');

  const semLabel = {
    '0': 'Summer',
    '1': '1st Semester',
    '2': '2nd Semester',
  }[semCode] ?? 'Unknown';

  // Group subjects by subject code
  const groupedSubjects = subjects.reduce((acc: Record<string, EnrolledSubject[]>, sub) => {
    acc[sub.SUB_CODE] = acc[sub.SUB_CODE] || [];
    acc[sub.SUB_CODE].push(sub);
    return acc;
  }, {});

  // Calculate total units only after groupedSubjects is declared
  const totalUnits = Object.values(groupedSubjects).reduce(
    (sum, group) => sum + Number(group[0]?.total_units || 0),
    0
  );

  return (
    <div className="rounded-md overflow-hidden border">
      <div className="px-4 py-2 bg-gray-100 dark:bg-neutral-800 dark:text-white font-semibold border-b">
        School Year: {syFrom} - {syTo} | Semester: {semLabel}
      </div>
      <div className="overflow-x-auto">
        <table className="min-w-full text-sm text-left bg-white text-black dark:bg-neutral-900 dark:text-white">
          <thead className="bg-gray-100 dark:bg-neutral-800">
            <tr>
              <th className="px-4 py-2 w-24">Subject Code</th>
              <th className="px-4 py-2 w-48">Subject Name</th>
              <th className="px-4 py-2 w-24">Section</th>
              <th className="px-4 py-2 w-24 text-center">Units</th>
              <th className="px-4 py-2 w-64">Schedule</th>
              <th className="px-4 py-2 w-48">Faculty</th>
            </tr>
          </thead>
          <tbody>
            {Object.entries(groupedSubjects).map(([code, group]) => {
              const subject = group[0];
              return (
                <tr key={code} className="hover:bg-gray-50 dark:hover:bg-neutral-800 border-t">
                  <td className="px-4 py-2">{subject.SUB_CODE}</td>
                  <td className="px-4 py-2">{subject.SUB_NAME}</td>
                  <td className="px-4 py-2">{subject.SECTION}</td>
                  <td className="px-4 py-2 text-center">{subject.total_units}</td>
                  <td className="px-4 py-2">{subject.schedule || 'N/A'}</td>
                  <td className="px-4 py-2">{subject.faculty_name || 'N/A'}</td>
                </tr>
              );
            })}
          </tbody>
          <tfoot className="bg-gray-100 dark:bg-neutral-800">
            <tr>
              <th colSpan={3}></th>
              <th className="px-4 py-2 text-center">{totalUnits}</th>
              <th colSpan={2}></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  );
}
