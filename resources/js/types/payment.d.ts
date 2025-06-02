import { PageProps } from './index';

// export interface OtherSchoolFee {
//     FEE_NAME: string;
// }

export interface Payment {
  DESCRIPTION: string | TrustedHTML;
  PAYMENT_INDEX: number;
  OR_NUMBER: string;
  DATE_PAID: string;
  AMOUNT: number;
  // AMOUNT_TENDERED: number;
  // AMOUNT_CHANGE: number;
  // otherSchoolFee?: OtherSchoolFee;
}

export interface PaymentsPageProps extends PageProps {
  payments: Payment[];
}
