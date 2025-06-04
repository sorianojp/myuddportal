import { PageProps } from './index';

export interface Payment {
  DESCRIPTION: string | TrustedHTML;
  PAYMENT_INDEX: number;
  OR_NUMBER: string;
  DATE_PAID: string;
  AMOUNT: number;
}

export interface PaymentsPageProps extends PageProps {
  payments: Payment[];
}
