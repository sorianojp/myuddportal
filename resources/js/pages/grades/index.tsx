import AppLayout from '@/layouts/app-layout';
import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import type { GradesPageProps } from '@/types/grade';

import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';

import HeadingSmall from '@/components/heading-small';
import GradesTable from '@/components/ui/GradesTable';

export default function MyGrade() {
  const { props } = usePage<GradesPageProps>();
  const {
    availableTerms,
    finalGroupedGrades,
    defaultSy,
    defaultSem,
  } = props;

  const [schoolYear, setSchoolYear] = useState(defaultSy ?? '');
  const [semester, setSemester] = useState(defaultSem ?? '');
  const [gradeName, setGradeName] = useState('all');

  const handleFilterChange = (key: string, value: string) => {
    const updatedSy = key === 'sy' ? value : schoolYear;
    const updatedSem = key === 'sem' ? value : semester;
    const updatedGrade = key === 'grade' ? value : gradeName;

    setSchoolYear(updatedSy);
    setSemester(updatedSem);
    setGradeName(updatedGrade);

    router.visit(route('mygrades'), {
      data: {
        sy: updatedSy,
        sem: updatedSem,
        grade: updatedGrade,
      },
      preserveState: true,
      preserveScroll: true,
    });
  };

  const groupedEntries = Object.entries(finalGroupedGrades);

  return (
    <AppLayout breadcrumbs={[{ title: 'Grades', href: '/mygrades' }]}>
      <Head title="My Grades" />
      <div className="px-4 py-6 space-y-6">
        <HeadingSmall
          title="All Grades"
          description="Note: Missing grades under certain Grade Types may mean the instructor hasn't encoded them yet."
        />

        <form onSubmit={e => e.preventDefault()} className="flex flex-wrap gap-4">
          {/* School Year */}
          <div>
            <label className="block text-sm font-medium mb-1">School Year</label>
            <Select value={schoolYear} onValueChange={val => handleFilterChange('sy', val)}>
              <SelectTrigger>
                <SelectValue placeholder="Select School Year" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                {[...new Set(availableTerms.map(t => `${t.SY_FROM}-${t.SY_TO}`))].map((sy) => (
                  <SelectItem key={sy} value={sy}>{sy}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          {/* Semester */}
          <div>
            <label className="block text-sm font-medium mb-1">Semester</label>
            <Select value={semester} onValueChange={val => handleFilterChange('sem', val)}>
              <SelectTrigger>
                <SelectValue placeholder="Select Semester" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                <SelectItem value="1">1st Sem</SelectItem>
                <SelectItem value="2">2nd Sem</SelectItem>
                <SelectItem value="0">Summer</SelectItem>
              </SelectContent>
            </Select>
          </div>

          {/* Grade Type */}
          <div>
            <label className="block text-sm font-medium mb-1">Grade Type</label>
            <Select value={gradeName} onValueChange={val => handleFilterChange('grade', val)}>
              <SelectTrigger>
                <SelectValue placeholder="Select Grade Type" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                <SelectItem value="Prelim">Prelim</SelectItem>
                <SelectItem value="Midterm">Midterm</SelectItem>
                <SelectItem value="Semi-Final">Semi-Final</SelectItem>
                <SelectItem value="Final">Final</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </form>

        {groupedEntries.length === 0 && (
          <p className="text-gray-600 mt-4">No grades found for the selected filters.</p>
        )}

        {groupedEntries.map(([term, grades]) => (
          <GradesTable key={term} term={term} grades={grades} />
        ))}
      </div>
    </AppLayout>
  );
}
