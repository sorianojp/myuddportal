// resources/js/types/grade.d.ts
import { PageProps } from 'index'; // or wherever your PageProps is defined

export interface Grade {
    GRADE_NAME: string;
    GRADE: string;
    CREDIT_EARNED: number;
    remark?: { REMARK: string };
    encoded_by_user?: {
        LNAME?: string;
        FNAME?: string;
        MNAME?: string;
    };
    sub_section?: {
        subject?: {
            SUB_NAME: string;
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
    filterFrom: string;
    filterTo: string;
    filterSem: string;
    filterGradeName: string;
    finalGroupedGrades: Record<string, Grade[]>;
    [key: string]: unknown; // ✅ this fixes the TypeScript constraint error
}

