import { PageProps } from './index';

export interface GradeEntry {
  SUB_CODE: string;
  SUB_NAME: string;
  GRADE_NAME: string;
  GRADE: string;
  CREDIT_EARNED: number;
  REMARK: string;
  ENCODED_BY: string;
  SY_FROM: number;
  SY_TO: number;
  SEMESTER: number;
}


export interface GradesPageProps extends PageProps {
  finalGroupedGrades: Record<string, GradeEntry[]>;
}
