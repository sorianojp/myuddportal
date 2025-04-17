import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

// Core user interface shared globally
export interface User {
  USER_ID: string;
  full_name: string;
  ID_NUMBER?: string; // You were accessing this, but it wasn't declared
  [key: string]: unknown;
}

// PageProps for usePage<PageProps>()
export interface PageProps {
  auth: {
    user: User | null;
  };
  ziggy: Config & { location: string };
  sidebarOpen: boolean;
  [key: string]: unknown;
}

// Layout UI types
export interface BreadcrumbItem {
  title: string;
  href: string;
}

export interface NavGroup {
  title: string;
  items: NavItem[];
}

export interface NavItem {
  title: string;
  href: string;
  icon?: LucideIcon | null;
  isActive?: boolean;
}
