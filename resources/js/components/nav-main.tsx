import {SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem,} from '@/components/ui/sidebar';
import { type NavItem, type PageProps } from '@/types';
import { Link, usePage } from '@inertiajs/react';
  
  export function NavMain({ items = [] }: { items: NavItem[] }) {
    const { currentSchoolYear } = usePage<PageProps>().props;
    const page = usePage<PageProps>();
    const getSemesterLabel = (sem: number | string): string => {
        switch (parseInt(sem as string)) {
          case 1:
            return '1ST SEM';
          case 2:
            return '2ND SEM';
          case 0:
            return 'SUMMER';
          default:
            return 'UNKNOWN SEM';
        }
      };
      const schoolYearLabel = currentSchoolYear
      ? `SY ${currentSchoolYear.CUR_SCHYR_FROM}-${currentSchoolYear.CUR_SCHYR_TO} ${getSemesterLabel(currentSchoolYear.CUR_SEMESTER)}`
      : 'School Year: N/A';
  
    return (
      <SidebarGroup className="px-2 py-0">
        <SidebarGroupLabel>{schoolYearLabel}</SidebarGroupLabel>
        <SidebarMenu>
          {items.map((item) => (
            <SidebarMenuItem key={item.title}>
              <SidebarMenuButton
                asChild
                isActive={item.href === page.url}
                tooltip={{ children: item.title }}
              >
                <Link href={item.href} prefetch>
                  {item.icon && <item.icon />}
                  <span>{item.title}</span>
                </Link>
              </SidebarMenuButton>
            </SidebarMenuItem>
          ))}
        </SidebarMenu>
      </SidebarGroup>
    );
  }
  