const Welcome = () => import('~/pages/welcome')
const Login = () => import('~/pages/auth/login')
const Register = () => import('~/pages/auth/register')
const PasswordEmail = () => import('~/pages/auth/password/email')
const PasswordReset = () => import('~/pages/auth/password/reset')

const Dashboard = () => import('~/pages/dashboard')
const Settings = () => import('~/pages/settings/index')
const SettingsProfile = () => import('~/pages/settings/profile')
const SettingsPassword = () => import('~/pages/settings/password')

export default [
  { path: '/', name: 'welcome', component: Welcome },

  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/password/reset', name: 'password.request', component: PasswordEmail },
  { path: '/password/reset/:token', name: 'password.reset', component: PasswordReset },

  { path: '/dashboard', name: 'dashboard', component: Dashboard },
  { path: '/settings', component: Settings, children: [
    { path: '', redirect: { name: 'settings.profile' }},
    { path: 'profile', name: 'settings.profile', component: SettingsProfile },
    { path: 'password', name: 'settings.password', component: SettingsPassword }
  ] },

  { path: '/company', name: 'company', component: () => import('~/pages/company/index') },
  { path: '/company/add', name: 'add-company', component: () => import('~/pages/company/add') },
  { path: '/company/edit/:id', name: 'edit-company', component: () => import('~/pages/company/edit') },
  { path: '/company/:id', name: 'show-company', component: () => import('~/pages/company/show') },
  { path: '/company-search', name: 'company-search', component: () => import('~/pages/company/search') },

  { path: '/company-category', name: 'company-category', component: () => import('~/pages/company/category/index') },
  { path: '/company-category/add', name: 'add-category', component: () => import('~/pages/company/category/add') },
  { path: '/company-category/edit/:id', name: 'edit-category', component: () => import('~/pages/company/category/edit') },
  { path: '/company-category/:id', name: 'show-category', component: () => import('~/pages/company/category/show') },

  { path: '*', component: require('~/pages/errors/404') }
]
