export interface EnrolledSubject {
    total_units: string;
    schedule: string;
    ROOM_NUMBER: string;
    SECTION: string;
    SY_FROM: number;
    SY_TO: number;
    CURRENT_SEMESTER: number;
    SUB_CODE: string;
    SUB_NAME: string;
    WEEK_DAY: number | string | null;
    HOUR_FROM_24: number | null;
    HOUR_TO_24: number | null;
    faculty_name: string;
}
