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
              <th className="px-4 py-2 w-48">Section</th>
              <th className="px-4 py-2 w-64">Schedule</th>
              <th className="px-4 py-2 w-48">Faculty</th>
            </tr>
          </thead>
          <tbody>
            {Object.entries(groupedSubjects).map(([code, group]) => {
              const subject = group[0]; // all entries are grouped, so use the first one
              return (
                <tr key={code} className="hover:bg-gray-50 dark:hover:bg-neutral-800 border-t">
                  <td className="px-4 py-2">{subject.SUB_CODE}</td>
                  <td className="px-4 py-2">{subject.SUB_NAME}</td>
                  <td className="px-4 py-2">{subject.SECTION}</td>
                  <td className="px-4 py-2">{subject.schedule || 'N/A'}</td>
                  <td className="px-4 py-2">{subject.faculty_name || 'N/A'}</td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
}
