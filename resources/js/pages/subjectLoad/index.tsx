import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';
import type { PageProps } from '@/types';
import type { EnrolledSubject } from '@/types/subjectload';
import HeadingSmall from '@/components/heading-small';
import { useState } from 'react';
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
} from '@/components/ui/select';
import SubjectLoadTable from '@/components/ui/SubjectLoadTable';

export default function SubjectLoad() {
  const { props } = usePage<PageProps & { enrolledSubjects: Record<string, EnrolledSubject[]> }>();
  const { enrolledSubjects } = props;

  const [filterSyFrom, setFilterSyFrom] = useState('all');
  const [filterSem, setFilterSem] = useState('all');

  const termKeys = Object.keys(enrolledSubjects);
  const syOptions = Array.from(new Set(termKeys.map(term => term.split('-S')[0])));

  const filteredTerms = termKeys.filter(term => {
    const [sy, sem] = term.split('-S');
    const matchSy = filterSyFrom === 'all' || filterSyFrom === sy;
    const matchSem = filterSem === 'all' || filterSem === sem;
    return matchSy && matchSem;
  });

  return (
    <AppLayout breadcrumbs={[{ title: 'Subject Load Schedule', href: '/subjectload' }]}>
      <Head title="Subjects Load Schedule" />
      <div className="px-4 py-6 space-y-6">
        <HeadingSmall
          title="All Schedule"
          description="View all your subject load schedule."
        />

        <div className="flex flex-wrap gap-4">
          <div>
            <label className="block text-sm font-medium mb-1">School Year</label>
            <Select value={filterSyFrom} onValueChange={setFilterSyFrom}>
              <SelectTrigger>
                <SelectValue placeholder="All" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                {syOptions.map(sy => (
                  <SelectItem key={sy} value={sy}>{sy}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div>
            <label className="block text-sm font-medium mb-1">Semester</label>
            <Select value={filterSem} onValueChange={setFilterSem}>
              <SelectTrigger>
                <SelectValue placeholder="All" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                <SelectItem value="1">1st Semester</SelectItem>
                <SelectItem value="2">2nd Semester</SelectItem>
                <SelectItem value="0">Summer</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>

        {filteredTerms.length === 0 && (
          <p className="text-gray-600 mt-6">No enrolled subjects found.</p>
        )}

        {filteredTerms.map(term => (
          <SubjectLoadTable key={term} term={term} subjects={enrolledSubjects[term]} />
        ))}
      </div>
    </AppLayout>
  );
}
