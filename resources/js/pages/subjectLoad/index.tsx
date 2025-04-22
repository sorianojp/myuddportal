import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';
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
import type { SubjectLoadPageProps } from '@/types/subjectload';

export default function SubjectLoad() {
  const {
    enrolledSubjects,
    availableTerms,
    defaultSy,
    defaultSem,
  } = usePage<SubjectLoadPageProps>().props;

  const [filterSyFrom, setFilterSyFrom] = useState(defaultSy ?? '');
  const [filterSem, setFilterSem] = useState(defaultSem ?? '');

  const handleFilterChange = (key: string, value: string) => {
    const newSy = key === 'sy' ? value : filterSyFrom;
    const newSem = key === 'sem' ? value : filterSem;

    setFilterSyFrom(newSy);
    setFilterSem(newSem);

    router.visit(route('subjectload'), {
      data: {
        sy: newSy,
        sem: newSem,
      },
      preserveScroll: true,
      preserveState: true,
    });
  };

  const termKeys = Object.keys(enrolledSubjects);
  const filteredTerms = termKeys;

  const syOptions = [...new Set(availableTerms.map(t => `${t.SY_FROM}-${t.SY_TO}`))];

  return (
    <AppLayout breadcrumbs={[{ title: 'Subject Load Schedule', href: '/subjectload' }]}>
      <Head title="Subject Load Schedule" />
      <div className="px-4 py-6 space-y-6">
        <HeadingSmall
          title="All Schedule"
          description="View all your subject load schedule."
        />

        <div className="flex flex-wrap gap-4">
          {/* School Year */}
          <div>
            <label className="block text-sm font-medium mb-1">School Year</label>
            <Select value={filterSyFrom} onValueChange={val => handleFilterChange('sy', val)}>
              <SelectTrigger>
                <SelectValue placeholder="Select School Year" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                {syOptions.map(sy => (
                  <SelectItem key={sy} value={sy}>{sy}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          {/* Semester */}
          <div>
            <label className="block text-sm font-medium mb-1">Semester</label>
            <Select value={filterSem} onValueChange={val => handleFilterChange('sem', val)}>
              <SelectTrigger>
                <SelectValue placeholder="Select Semester" />
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
