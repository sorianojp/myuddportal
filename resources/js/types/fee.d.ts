import { PageProps } from './index';

export interface Fee {
    fee_hist_index: number;
    TOTAL_TUITION: number;
    TOTAL_MISC: number;
    TOTAL_OTHER: number;
    SY_FROM: number;
    SY_TO: number;
    SEMESTER: number;
}

export interface FeesPageProps extends PageProps {
  fees: Fee[];
}
