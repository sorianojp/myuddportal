import { PageProps } from './index';

export interface Grade {
  GRADE_NAME: string;
  GRADE: string;
  CREDIT_EARNED: number;
  remark?: {
    REMARK: string;
  };
  encoded_by_user?: {
    LNAME?: string;
    FNAME?: string;
    MNAME?: string;
  };
  sub_section?: {
    subject?: {
      SUB_NAME: string;
      SUB_CODE: string;
    };
  };
}

export interface Curriculum {
  SY_FROM: string;
  SY_TO: string;
  SEMESTER: number;
}

export interface GradesPageProps extends PageProps {
  availableTerms: Curriculum[];
  finalGroupedGrades: Record<string, Grade[]>;
  defaultSy: string | null;
  defaultSem: string | null;
}
