export interface EnrolledSubject {
    ROOM_NUMBER: string;
    SECTION: string;
    SY_FROM: number;
    SY_TO: number;
    CURRENT_SEMESTER: number;
    SUB_CODE: string;
    SUB_NAME: string;
    tot_acad_unit: number;
    WEEK_DAY: number | string | null;
    HOUR_FROM_24: number | null;
    HOUR_TO_24: number | null;
    faculty_name: string;
}
