import React from 'react';
import type { ScheduleEntry } from '@/types/schedule';

interface ScheduleTableProps {
  schedule: ScheduleEntry[];
}

const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

export default function ScheduleTable({ schedule }: ScheduleTableProps) {
  return (
    <div className="rounded-md overflow-hidden border">
      <div className="px-4 py-2 bg-gray-100 dark:bg-neutral-800 dark:text-white font-semibold border-b">
        Class Schedule
      </div>
      <div className="overflow-x-auto">
        <table className="min-w-full text-sm text-left bg-white text-black dark:bg-neutral-900 dark:text-white">
          <thead className="bg-gray-100 dark:bg-neutral-800">
            <tr>
              <th className="px-4 py-2 whitespace-nowrap">Subject Code</th>
              <th className="px-4 py-2 whitespace-nowrap">Subject Name</th>
              <th className="px-4 py-2 whitespace-nowrap">Section</th>
              <th className="px-4 py-2 whitespace-nowrap">Room</th>
              <th className="px-4 py-2 whitespace-nowrap">Day</th>
              <th className="px-4 py-2 whitespace-nowrap">Time</th>
              <th className="px-4 py-2 whitespace-nowrap">Type</th>
            </tr>
          </thead>
          <tbody>
            {schedule.length === 0 ? (
              <tr>
                <td colSpan={7} className="px-4 py-4 text-center text-gray-500 dark:text-gray-400">
                  No schedule found.
                </td>
              </tr>
            ) : (
              schedule.map((entry) => (
                <tr key={`${entry.SUB_SEC_INDEX}-${entry.WEEK_DAY}`} className="hover:bg-gray-50 dark:hover:bg-neutral-800 border-t">
                  <td className="px-4 py-2">{entry.SUB_CODE}</td>
                  <td className="px-4 py-2">{entry.SUB_NAME}</td>
                  <td className="px-4 py-2">{entry.SECTION}</td>
                  <td className="px-4 py-2">{entry.ROOM_NUMBER}</td>
                  <td className="px-4 py-2">{dayNames[entry.WEEK_DAY]}</td>
                  <td className="px-4 py-2">
                    {entry.TIME_FROM} - {entry.TIME_TO}
                  </td>
                  <td className="px-4 py-2">{entry.IS_LEC == 0 ? 'Lecture' : 'Laboratory'}</td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
