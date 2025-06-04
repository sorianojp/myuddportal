import { PageProps } from './index';

export interface ScheduleEntry {
  SUB_CODE: string;
  SUB_NAME: string;
  SECTION: string;
  ROOM_NUMBER: string;
  WEEK_DAY: number;
  TIME_FROM: string;
  TIME_TO: string;
  IS_LEC: number;
  SUB_SEC_INDEX: number;
}

export interface SchedulePageProps extends PageProps {
  schedule: ScheduleEntry[];
}
