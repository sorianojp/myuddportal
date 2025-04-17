// // import AppLayout from '@/layouts/app-layout';
// // import { Head, router, usePage } from '@inertiajs/react';
// // import { useEffect, useState } from 'react';
// // import type { GradesPageProps, Curriculum } from '@/types/grade';

// // import {
// //   Select,
// //   SelectContent,
// //   SelectItem,
// //   SelectTrigger,
// //   SelectValue,
// // } from '@/components/ui/select';
// // import { Button } from '@/components/ui/button';
// // import { Input } from '@/components/ui/input';
// // import HeadingSmall from '@/components/heading-small';
// // import GradesTable from '@/components/ui/GradesTable';

// // export default function MyGrade() {
// //   const { props } = usePage<GradesPageProps>();
// //   const {
// //     availableTerms,
// //     filterFrom,
// //     filterTo,
// //     filterSem,
// //     filterGradeName,
// //     finalGroupedGrades,
// //   } = props;

// //   const [syFrom, setSyFrom] = useState(filterFrom || 'all');
// //   const [syTo, setSyTo] = useState(filterTo || '');
// //   const [semester, setSemester] = useState(filterSem || 'all');
// //   const [gradeName, setGradeName] = useState(filterGradeName || 'all');
// //   const [loading, setLoading] = useState(false);

// //   useEffect(() => {
// //     const matching = availableTerms.find((t: Curriculum) => t.SY_FROM === syFrom);
// //     setSyTo(matching?.SY_TO ?? '');
// //   }, [syFrom, availableTerms]);

// //   const normalize = (val: string) => (val === 'all' ? '' : val);

// //   const handleFilter = (e: React.FormEvent) => {
// //     e.preventDefault();
// //     setLoading(true);
// //     router.get(
// //       route('mygrades'),
// //       {
// //         sy_from: normalize(syFrom),
// //         sy_to: normalize(syTo),
// //         semester: normalize(semester),
// //         grade_name: normalize(gradeName),
// //       },
// //       {
// //         onFinish: () => setLoading(false),
// //       }
// //     );
// //   };

// //   const resetFilters = () => {
// //     setSyFrom('all');
// //     setSyTo('');
// //     setSemester('all');
// //     setGradeName('all');
// //     setLoading(true);
// //     router.get(route('mygrades'), {}, {
// //       onFinish: () => setLoading(false),
// //     });
// //   };

// //   return (
// //     <AppLayout breadcrumbs={[{ title: 'Grades', href: '/mygrades' }]}>
// //       <Head title="My Grades" />
// //       <div className="px-4 py-6 space-y-6">
// //         <HeadingSmall
// //           title="All Grades"
// //           description="Note: Missing grades under certain Grade Types may mean the instructor hasn't encoded them yet."
// //         />

// //         <form onSubmit={handleFilter} className="grid grid-cols-1 md:grid-cols-4 gap-4">
// //           <div>
// //             <label htmlFor="sy_from" className="block text-sm font-medium mb-1">SY From</label>
// //             <Select value={syFrom} onValueChange={setSyFrom}>
// //               <SelectTrigger>
// //                 <SelectValue placeholder="All" />
// //               </SelectTrigger>
// //               <SelectContent>
// //                 <SelectItem value="all">All</SelectItem>
// //                 {[...new Set(availableTerms.map(t => t.SY_FROM))].map(year => (
// //                   <SelectItem key={year} value={year}>{year}</SelectItem>
// //                 ))}
// //               </SelectContent>
// //             </Select>
// //           </div>

// //           <div>
// //             <label htmlFor="sy_to" className="block text-sm font-medium mb-1">SY To</label>
// //             <Input id="sy_to" value={syTo} readOnly />
// //           </div>

// //           <div>
// //             <label htmlFor="semester" className="block text-sm font-medium mb-1">Semester</label>
// //             <Select value={semester} onValueChange={setSemester}>
// //               <SelectTrigger>
// //                 <SelectValue placeholder="All" />
// //               </SelectTrigger>
// //               <SelectContent>
// //                 <SelectItem value="all">All</SelectItem>
// //                 <SelectItem value="1">1st Sem</SelectItem>
// //                 <SelectItem value="2">2nd Sem</SelectItem>
// //                 <SelectItem value="0">Summer</SelectItem>
// //               </SelectContent>
// //             </Select>
// //           </div>

// //           <div>
// //             <label htmlFor="grade_name" className="block text-sm font-medium mb-1">Grade Type</label>
// //             <Select value={gradeName} onValueChange={setGradeName}>
// //               <SelectTrigger>
// //                 <SelectValue placeholder="All" />
// //               </SelectTrigger>
// //               <SelectContent>
// //                 <SelectItem value="all">All</SelectItem>
// //                 <SelectItem value="Prelim">Prelim</SelectItem>
// //                 <SelectItem value="Midterm">Midterm</SelectItem>
// //                 <SelectItem value="Semi-Final" disabled>Semi-Final</SelectItem>
// //                 <SelectItem value="Final">Final</SelectItem>
// //               </SelectContent>
// //             </Select>
// //           </div>

// //           <div className="md:col-span-4 flex flex-wrap gap-2 mt-2">
// //             <Button type="submit" disabled={loading}>
// //               {loading ? 'Filtering...' : 'Filter'}
// //             </Button>
// //             <Button type="button" variant="secondary" onClick={resetFilters}>
// //               Reset Filter
// //             </Button>
// //           </div>
// //         </form>

// //         {Object.keys(finalGroupedGrades).length === 0 && !loading && (
// //           <p className="text-gray-600 mt-4">No grades found for the selected filters.</p>
// //         )}

// //         {loading && <p className="text-blue-500">Loading grades...</p>}

// //         {!loading &&
// //           Object.entries(finalGroupedGrades).map(([term, grades]) => (
// //             <GradesTable key={term} term={term} grades={grades} />
// //           ))}
// //       </div>
// //     </AppLayout>
// //   );
// // }
// import AppLayout from '@/layouts/app-layout';
// import { Head, usePage } from '@inertiajs/react';
// import { useEffect, useState } from 'react';
// import type { GradesPageProps, Curriculum } from '@/types/grade';

// import {
//   Select,
//   SelectContent,
//   SelectItem,
//   SelectTrigger,
//   SelectValue,
// } from '@/components/ui/select';

// import { Input } from '@/components/ui/input';
// import HeadingSmall from '@/components/heading-small';
// import GradesTable from '@/components/ui/GradesTable';

// export default function MyGrade() {
//   const { props } = usePage<GradesPageProps>();
//   const {
//     availableTerms,
//     finalGroupedGrades,
//   } = props;

//   const [syFrom, setSyFrom] = useState('all');
//   const [syTo, setSyTo] = useState('');
//   const [semester, setSemester] = useState('all');
//   const [gradeName, setGradeName] = useState('all');

//   useEffect(() => {
//     const matching = availableTerms.find((t: Curriculum) => t.SY_FROM === syFrom);
//     setSyTo(matching?.SY_TO ?? '');
//   }, [syFrom, availableTerms]);

//   const normalize = (val: string) => (val === 'all' ? '' : val);

//   const filteredTerms = Object.entries(finalGroupedGrades)
//   .map(([term, grades]) => {
//     const [sy, to, semLabel] = term.split('-');
//     const matchSy = !normalize(syFrom) || normalize(syFrom) === sy;
//     const matchTo = !normalize(syTo) || normalize(syTo) === to;
//     const matchSem = !normalize(semester) ||
//       {
//         '0': 'Summer',
//         '1': 'First Semester',
//         '2': 'Second Semester',
//       }[normalize(semester)] === semLabel;

//     const filteredGrades = normalize(gradeName)
//       ? grades.filter(g => g.GRADE_NAME === normalize(gradeName))
//       : grades;

//     const shouldInclude = matchSy && matchTo && matchSem && filteredGrades.length > 0;

//     return shouldInclude ? [term, filteredGrades] : null;
//   })
//   .filter(Boolean) as [string, typeof finalGroupedGrades[string]][];


//   return (
//     <AppLayout breadcrumbs={[{ title: 'Grades', href: '/mygrades' }]}>
//       <Head title="My Grades" />
//       <div className="px-4 py-6 space-y-6">
//         <HeadingSmall
//           title="All Grades"
//           description="Note: Missing grades under certain Grade Types may mean the instructor hasn't encoded them yet."
//         />

//         <form onSubmit={e => e.preventDefault()} className="grid grid-cols-1 md:grid-cols-4 gap-4">
//           <div>
//             <label htmlFor="sy_from" className="block text-sm font-medium mb-1">SY From</label>
//             <Select value={syFrom} onValueChange={setSyFrom}>
//               <SelectTrigger>
//                 <SelectValue placeholder="All" />
//               </SelectTrigger>
//               <SelectContent>
//                 <SelectItem value="all">All</SelectItem>
//                 {[...new Set(availableTerms.map(t => t.SY_FROM))].map(year => (
//                   <SelectItem key={year} value={year}>{year}</SelectItem>
//                 ))}
//               </SelectContent>
//             </Select>
//           </div>

//           <div>
//             <label htmlFor="sy_to" className="block text-sm font-medium mb-1">SY To</label>
//             <Input id="sy_to" value={syTo} readOnly />
//           </div>

//           <div>
//             <label htmlFor="semester" className="block text-sm font-medium mb-1">Semester</label>
//             <Select value={semester} onValueChange={setSemester}>
//               <SelectTrigger>
//                 <SelectValue placeholder="All" />
//               </SelectTrigger>
//               <SelectContent>
//                 <SelectItem value="all">All</SelectItem>
//                 <SelectItem value="1">1st Sem</SelectItem>
//                 <SelectItem value="2">2nd Sem</SelectItem>
//                 <SelectItem value="0">Summer</SelectItem>
//               </SelectContent>
//             </Select>
//           </div>

//           <div>
//             <label htmlFor="grade_name" className="block text-sm font-medium mb-1">Grade Type</label>
//             <Select value={gradeName} onValueChange={setGradeName}>
//               <SelectTrigger>
//                 <SelectValue placeholder="All" />
//               </SelectTrigger>
//               <SelectContent>
//                 <SelectItem value="all">All</SelectItem>
//                 <SelectItem value="Prelim">Prelim</SelectItem>
//                 <SelectItem value="Midterm">Midterm</SelectItem>
//                 <SelectItem value="Semi-Final">Semi-Final</SelectItem>
//                 <SelectItem value="Final">Final</SelectItem>
//               </SelectContent>
//             </Select>
//           </div>
//         </form>

//         {filteredTerms.length === 0 && (
//           <p className="text-gray-600 mt-4">No grades found for the selected filters.</p>
//         )}

//         {filteredTerms.map(([term, grades]) => (
//           <GradesTable key={term} term={term} grades={grades} />
//         ))}
//       </div>
//     </AppLayout>
//   );
// }
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

  const [schoolYear, setSchoolYear] = useState('all');
  const [semester, setSemester] = useState('all');
  const [gradeName, setGradeName] = useState('all');

  const normalize = (val: string) => (val === 'all' ? '' : val);

  const filteredTerms = Object.entries(finalGroupedGrades)
    .map(([term, grades]) => {
      const [sy, to, semLabel] = term.split('-');
      const matchSy = schoolYear === 'all' || `${sy}-${to}` === schoolYear;
      const matchSem = normalize(semester)
        ? {
            '0': 'Summer',
            '1': 'First Semester',
            '2': 'Second Semester',
          }[normalize(semester)] === semLabel
        : true;

      const filteredGrades = normalize(gradeName)
        ? grades.filter(g => g.GRADE_NAME === normalize(gradeName))
        : grades;

      const shouldInclude = matchSy && matchSem && filteredGrades.length > 0;

      return shouldInclude ? [term, filteredGrades] : null;
    })
    .filter(Boolean) as [string, typeof finalGroupedGrades[string]][];

  return (
    <AppLayout breadcrumbs={[{ title: 'Grades', href: '/mygrades' }]}>
      <Head title="My Grades" />
      <div className="px-4 py-6 space-y-6">
        <HeadingSmall
          title="All Grades"
          description="Note: Missing grades under certain Grade Types may mean the instructor hasn't encoded them yet."
        />

        <form onSubmit={e => e.preventDefault()} className="flex flex-wrap gap-4">
          <div>
            <label htmlFor="school_year" className="block text-sm font-medium mb-1">School Year</label>
            <Select value={schoolYear} onValueChange={setSchoolYear}>
              <SelectTrigger>
                <SelectValue placeholder="All" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                {[...new Set(availableTerms.map(t => `${t.SY_FROM}-${t.SY_TO}`))].map((sy) => (
                  <SelectItem key={sy} value={sy}>{sy}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div>
            <label htmlFor="semester" className="block text-sm font-medium mb-1">Semester</label>
            <Select value={semester} onValueChange={setSemester}>
              <SelectTrigger>
                <SelectValue placeholder="All" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All</SelectItem>
                <SelectItem value="1">1st Sem</SelectItem>
                <SelectItem value="2">2nd Sem</SelectItem>
                <SelectItem value="0">Summer</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div>
            <label htmlFor="grade_name" className="block text-sm font-medium mb-1">Grade Type</label>
            <Select value={gradeName} onValueChange={setGradeName}>
              <SelectTrigger>
                <SelectValue placeholder="All" />
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

        {filteredTerms.length === 0 && (
          <p className="text-gray-600 mt-4">No grades found for the selected filters.</p>
        )}

        {filteredTerms.map(([term, grades]) => (
          <GradesTable key={term} term={term} grades={grades} />
        ))}
      </div>
    </AppLayout>
  );
}
