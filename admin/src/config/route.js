export default [
  {
    name: 'title.Dashboard',
    route: { name: 'AdminDashboard' },
    icon: 'speedometer',
    roles: ['super admin', 'admin']
  },
  {
    name: 'title.Dashboard',
    route: { name: 'Dashboard' },
    icon: 'speedometer',
    roles: ['entrepreneur', 'investor', 'tenant']
  },
  {
    name: 'title.Projects',
    route: { name: 'AdminProjectIndex' },
    icon: 'folder',
    roles: ['admin'],
  },
  {
    name: 'title.DeletedProjects',
    route: { name: 'DeletedProjects' },
    icon: 'trash',
    roles: ['admin'],
  },
  // {
  //   name: 'title.Projects',
  //   route: { name: 'EntrepreneurProjectIndex' },
  //   icon: 'folder',
  //   roles: ['entrepreneur'],
  // },
  {
    name: 'title.Projects',
    route: { name: 'ProjectIndex' },
    icon: 'folder',
    roles: ['investor', 'tenant','entrepreneur'],
  },
  {
    name: 'title.Users',
    route: { name: 'AdminChildUserIndex' },
    icon: 'people',
    roles: ['admin']
  },
  {
    name: 'title.CompanyAdmins',
    icon: 'people',
    route: { name: 'AdminUserIndex' },
    roles: ['super admin']
  },
  {
    name: 'title.Plans',
    icon: 'people',
    route: { name: 'AdminPlan' },
    roles: ['admin']
  },
  {
    name: 'title.Settings',
    icon: 'gear-fill',
    roles: ['super admin', 'admin', 'entrepreneur', 'investor', 'tenant'],
    children: [
      {
        name: 'title.Profile',
        route: { name: 'AdminProfile' },
        roles: ['super admin', 'admin']
      },
      {
        name: 'title.Profile',
        route: { name: 'Profile' },
        roles: ['entrepreneur', 'investor', 'tenant']
      },
      {
        name: 'title.cards',
        icon: 'card',
        route: { name: 'AdminCard' },
        roles: ['admin']
      },
      {
        name: 'title.Support',
        route: { name: 'Support' },
        roles: ['super admin', 'admin']
      },
      {
        name: 'title.Support',
        route: { name: 'Support' },
        roles: ['entrepreneur', 'investor', 'tenant']
      },
      {
        name: 'title.InvitationLog',
        route: { name: 'AdminInvitationLog' },
        icon: 'folder',
        roles: ['super admin', 'admin']
      },
      {
        name: 'title.InvitationLog',
        route: { name: 'InvitationLog' },
        icon: 'folder',
        roles: ['entrepreneur', 'investor', 'tenant']
      }
    ]
  }
];
