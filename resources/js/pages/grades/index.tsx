import AppLayout from '@/layouts/app-layout';
import { Head, usePage } from '@inertiajs/react';
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
  } = props;

  const [schoolYear, setSchoolYear] = useState('');
  const [semester, setSemester] = useState('');
  const [gradeName, setGradeName] = useState('');

  const isSYAll = schoolYear === 'all';
  const isSYSelected = schoolYear !== '' && schoolYear !== null;

  const filteredTerms = isSYSelected
    ? Object.entries(finalGroupedGrades)
        .map(([term, grades]) => {
          const [sy, to, semLabel] = term.split('-');
          const syKey = `${sy}-${to}`;

          const matchSy = isSYAll || syKey === schoolYear;
          const matchSem = semester === '' || semester === 'all' || {
            '0': 'Summer',
            '1': 'First Semester',
            '2': 'Second Semester',
          }[semester] === semLabel;

          const gradeFiltered = gradeName === '' || gradeName === 'all'
            ? grades
            : grades.filter(g => g.GRADE_NAME.toLowerCase() === gradeName.toLowerCase());

          const shouldInclude = matchSy && matchSem && gradeFiltered.length > 0;

          return shouldInclude ? [term, gradeFiltered] : null;
        })
        .filter(Boolean) as [string, typeof finalGroupedGrades[string]][]
    : [];

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
            <label htmlFor="school_year" className="block text-sm font-medium mb-1">School Year</label>
            <Select value={schoolYear} onValueChange={setSchoolYear}>
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
            <label htmlFor="semester" className="block text-sm font-medium mb-1">Semester</label>
            <Select value={semester} onValueChange={setSemester}>
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
            <label htmlFor="grade_name" className="block text-sm font-medium mb-1">Grade Type</label>
            <Select value={gradeName} onValueChange={setGradeName}>
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

        {!isSYSelected && (
          <p className="text-gray-600 mt-4 italic">
            Please select a School Year to view your grades.
          </p>
        )}

        {isSYSelected && filteredTerms.length === 0 && (
          <p className="text-gray-600 mt-4">No grades found for the selected filters.</p>
        )}

        {filteredTerms.map(([term, grades]) => (
          <GradesTable key={term} term={term} grades={grades} />
        ))}
      </div>
    </AppLayout>
  );
}
