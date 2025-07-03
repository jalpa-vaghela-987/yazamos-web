import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from "@/view/Home.vue";
import AdminLogin from "../view/admin/auth/login/index.vue";
import AdminOtpVarification from "../view/admin/auth/login/OtpVarification.vue";
import Login from "../view/user/auth/login/index.vue";
import OtpVerify from "../view/user/auth/login/OtpVerification.vue";
import Signup from "../view/user/auth/SignupForm.vue";
import SignupSuccess from "../view/user/auth/SignupSuccess.vue";
import AdminDashboard from "../view/admin/dashboard/index.vue";
import Dashboard from "../view/user/dashboard/index.vue";
// import ResetPassword from "../view/admin/ResetPassword/index.vue";
import AdminLayout from "../view/admin/layouts";
import { hasAuthUser, removeStorage, getAuthUser } from "@/Util/auth";
import Profile from "../view/auth/profile/index.vue";
import ChangePassword from "../view/auth/settings/changePassword.vue";

import CategoryIndex from "../view/admin/assetType/Index.vue";
import CategoryCreate from "../view/admin/assetType/Create.vue";
import CategoryEdit from "../view/admin/assetType/Edit.vue";

import ProjectIndex from "../view/user/projects/Index.vue";
import AdminProjectIndex from "../view/admin/projects/Index.vue";
import AuthAdminProjectIndex from "../view/admin/projects/showProjects.vue";
import ProjectCreate from "../view/admin/projects/Create.vue";
import ProjectEdit from "../view/admin/projects/Edit.vue";
import DeletedProjects from "../view/admin/projects/DeletedProjects.vue";
import ProjectShow from "../view/admin/projects/show.vue";
import AllMessages from "../view/admin/projects/AllMessages.vue";

import PhaseIndex from "../view/admin/Phase/Index.vue";
import PhaseCreate from "../view/admin/Phase/Create.vue";
import PhaseEdit from "../view/admin/Phase/Edit.vue";

import AdminUserIndex from "../view/admin/AdminUser/Index.vue";
import AuthAdminUserIndex from "../view/admin/AdminUser/showUsers.vue";
import AdminUserCreate from "../view/admin/AdminUser/Create.vue";
import AdminUserEdit from "../view/admin/AdminUser/Edit.vue";

import UserIndex from "../view/admin/users/userIndex.vue";
import UsersInfo from "../view/admin/users/userShow.vue";
import UserCreate from "../view/admin/users/userCreate.vue";
import UserEdit from "../view/admin/users/userEdit.vue";

import EntrepreneurIndex from "../view/admin/Entrepreneur/EntrepreneurIndex.vue";
import EntrepreneurCreate from "../view/admin/Entrepreneur/EntrepreneurCreate.vue";
import EntrepreneurEdit from "../view/admin/Entrepreneur/EntrepreneurEdit.vue";

import TenantIndex from "../view/admin/Tenant/TenantIndex.vue";
import TenantCreate from "../view/admin/Tenant/TenantCreate.vue";
import TenantEdit from "../view/admin/Tenant/TenantEdit.vue";

import InvestorIndex from "../view/admin/Investor/InvestorIndex.vue";
import InvestorCreate from "../view/admin/Investor/InvestorCreate.vue";
import InvestorEdit from "../view/admin/Investor/InvestorEdit.vue";

import MilestoneIndex from "../view/admin/milestone/index.vue";
import MilestoneCreate from "../view/admin/milestone/create.vue";
import MilestoneEdit from "../view/admin/milestone/edit.vue";


import InvitationLog from "../view/admin/InvitationLog/index.vue";

import Plan from "../view/admin/plan/index.vue";

import Transaction from "../view/admin/transactions/index.vue";

import Support from "../view/admin/Support/index.vue";

import Cards from "../view/admin/cards/index.vue";

Vue.use(VueRouter);

//error
import Error from "../view/error";

const ParentComponent = {
    render(h) {
        return h('router-view');
    }
};

const routes = [
    {
        path: "/",
        component: Home,
        meta: { breadcrumbs: [{ name: "Home", path: "/" }] }
    },
    {
        path: '/login',
        name: 'Login',
        meta: {
            authRequired: false,
            breadcrumbs: [{ name: "Login", path: "/login" }]
        },
        component: Login,
    },
    {
        path: '/otp-verify',
        name: 'OtpVerify',
        meta: {
            authRequired: false,
            breadcrumbs: [{ name: "Login", path: "/login" }]
        },
        component: OtpVerify,
    },
    //support
    {
        path: '/Support',
        name: 'Support',
        component: Support,
        meta: {
            breadcrumbs: [{ name: "Dashboard", path: "/" }, { name: "Support" },]
        },

    },
    {
        path: '/signup',
        name: 'Signup',
        meta: {
            authRequired: false,
            breadcrumbs: [{ name: "Signup", path: "/signup" }]
        },
        component: Signup,
    },
    {
        path: '/signup-success',
        name: 'SignupSuccess',
        meta: {
            authRequired: false,
            breadcrumbs: [{ name: "Signup Success", path: "/signup-success" }]
        },
        component: SignupSuccess,
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }]
        },
        component: Dashboard
    },
    {
        path: '/InvitationLog',
        name: 'InvitationLog',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/" }, { name: "Invitation Log" }]
        },
        component: InvitationLog
    },
    {
        path: '/plan',
        name: 'plan',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Plan" }]
        },
        component: Plan
    },
    {
        path: '/profile',
        name: 'Profile',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Profile" }]
        },
        component: Profile
    },
    {
        path: '/change-password',
        name: 'ChangePassword',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Change Password" }]
        },
        component: ChangePassword,
    },
    {
        path: '/projects',
        name: 'ProjectIndex',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Projects" }]
        },
        component: ProjectIndex
    },
    {
        path: '/projects/show/:id',
        name: 'ProjectShow',
        component: ProjectShow,
        meta: {
            breadcrumbs: [
                { name: "Dashboard", path: "/" },
                { name: "Projects", path: "/projects" },
                { name: "Show" },
            ],
        },
    },
    {
        path: 'projects',
        name: 'EntrepreneurProjectIndex',
        component: AdminProjectIndex,
        meta: {
            breadcrumbs: [
                { name: "Dashboard", path: "/" },
                { name: "Projects" },
            ],
        },
    },
    {
        path: '/projects/create',
        name: 'ProjectCreate',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Projects", path: "/projects" }, { name: "Create Project" }]
        },
        component: ProjectCreate
    },
    {
        path: '/projects/edit/:id',
        name: 'ProjectEdit',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Projects", path: "/projects" }, { name: "Edit Project" }]
        },
        component: ProjectEdit,
        props: true
    },
    {
        path: '/projects/:id/messages',
        name: 'ProjectMessages',
        component: () => import('@/view/admin/projects/AllMessages.vue'),
        meta: {
            requiresAuth: true
        }
    },
    {
        path: '/phases/:project_id?',
        name: 'PhaseIndex',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Projects", path: "/projects" }, { name: "Phases" }]
        },
        component: PhaseIndex,
        props: true
    },
    {
        path: '/phases/create/:project_id',
        name: 'PhaseCreate',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Projects", path: "/projects" }, { name: "Phases", path: "/phases" }, { name: "Create Phase" }]
        },
        component: PhaseCreate,
        props: true
    },
    {
        path: '/phases/:id/edit',
        name: 'PhaseEdit',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Projects", path: "/projects" }, { name: "Phases", path: "/phases" }, { name: "Edit Phase" }]
        },
        component: PhaseEdit,
        props: true
    },
    {
        path: '/milestones/:phase_id?',
        name: 'MilestoneIndex',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Projects", path: "/projects" }, { name: "Phases", path: "/phases" }, { name: "Milestones" }]
        },
        component: MilestoneIndex,
        props: true
    },
    {
        path: '/milestones/create/:phase_id',
        name: 'MilestoneCreate',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Projects", path: "/projects" }, { name: "Phases", path: "/phases" }, { name: "Milestones", path: "/milestones" }, { name: "Create Milestone" }]
        },
        component: MilestoneCreate,
        props: true
    },
    {
        path: '/milestones/:id/edit',
        name: 'MilestoneEdit',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Projects", path: "/projects" }, { name: "Phases", path: "/phases" }, { name: "Milestones", path: "/milestones" }, { name: "Edit Milestone" }]
        },
        component: MilestoneEdit,
        props: true
    },
    {
        path: '/tenants',
        name: 'TenantIndex',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Tenants" }]
        },
        component: TenantIndex
    },
    {
        path: '/tenants/create',
        name: 'TenantCreate',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Tenants", path: "/tenants" }, { name: "Create Tenant" }]
        },
        component: TenantCreate
    },
    {
        path: '/tenants/edit/:id',
        name: 'TenantEdit',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Tenants", path: "/tenants" }, { name: "Edit Tenant" }]
        },
        component: TenantEdit,
        props: true
    },
    {
        path: '/investors',
        name: 'InvestorIndex',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Investors" }]
        },
        component: InvestorIndex
    },
    {
        path: '/investors/create',
        name: 'InvestorCreate',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Investors", path: "/investors" }, { name: "Create Investor" }]
        },
        component: InvestorCreate
    },
    {
        path: '/investors/edit/:id',
        name: 'InvestorEdit',
        meta: {
            authRequired: true,
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Investors", path: "/investors" }, { name: "Edit Investor" }]
        },
        component: InvestorEdit,
        props: true
    },
    {
        path: '/entrepreneurs',
        name: 'EntrepreneurIndex',
        meta: {
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Entrepreneurs" }]
        },
        component: EntrepreneurIndex
    },
    {
        path: '/entrepreneurs/create',
        name: 'EntrepreneurCreate',
        meta: {
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Entrepreneurs", path: "/entrepreneurs" }, { name: "Create Entrepreneur" }]
        },
        component: EntrepreneurCreate
    },
    {
        path: '/entrepreneurs/edit/:id',
        name: 'EntrepreneurEdit',
        meta: {
            breadcrumbs: [{ name: "Dashboard", path: "/dashboard" }, { name: "Entrepreneurs", path: "/entrepreneurs" }, { name: "Edit Entrepreneur" }]
        },
        component: EntrepreneurEdit,
        props: true
    },
    // support routes
    {
        path: 'Support',
        name: 'Support',
        component: Support,
        meta: {
            breadcrumbs: [
                { name: "Dashboard", path: "/" },
                { name: "Support" },
            ],
        },
        props: true
    },
    // Admin Routes
    {
        path: '/admin/login',
        name: 'AdminLogin',
        component: AdminLogin,
        meta: {
            authAdminRequired: false,
            breadcrumbs: [{ name: "Admin Login", path: "/admin/login" }]
        },
    },
    {
        path: '/admin/2fa',
        name: 'login-2fa',
        component: AdminOtpVarification,
        meta: {
            authAdminRequired: false,
            breadcrumbs: [{ name: "Admin Login", path: "/admin/login" }]
        },
    },
    {
        path: '/admin',
        meta: {
            authAdminRequired: true,
        },
        component: AdminLayout,
        children: [

            {
                path: 'UsersInfo/:id?',
                name: 'UsersInfo',
                component: UsersInfo,
                meta: {
                    breadcrumbs: [{ name: "Dashboard", path: "/" }, { name: "Users Info" }]
                },
            },
            {
                path: 'InvitationLog',
                name: 'AdminInvitationLog',
                component: InvitationLog,
                meta: {
                    breadcrumbs: [{ name: "Dashboard", path: "/" }, { name: "Invitation Log" }]
                },
            },
            {
                path: 'DeletedProjects',
                name: 'DeletedProjects',
                component: DeletedProjects,
                meta: {
                    breadcrumbs: [{ name: "Dashboard", path: "/" }, { name: "Deleted Projects" }]
                },
            },
            // support routes
            {
                path: 'dashboard',
                name: 'AdminDashboard',
                component: AdminDashboard,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                    ],
                },
            },
            {
                path: 'plan',
                name: 'AdminPlan',
                component: Plan,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Plan" },
                    ],
                },
            },
            {
                path: 'transaction',
                name: 'AdminTransactionIndex',
                component: Transaction,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Transaction" },
                    ],
                },
            },
            {
                path: 'profile',
                name: 'AdminProfile',
                component: Profile,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Profile" },
                    ],
                },
            },
            {
                path: 'cards',
                name: 'AdminCard',
                component: Cards,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Card" },
                    ],
                },
            },
            {
                path: 'change-password',
                name: 'AdminChangePassword',
                component: ChangePassword,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Change Password" },
                    ],
                },
            },
            {
                path: 'asset',
                name: 'CategoryIndex',
                component: CategoryIndex,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Asset" },
                    ],
                },
            },
            {
                path: 'asset/create',
                name: 'CategoryCreate',
                component: CategoryCreate,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Asset", path: "/admin/asset" },
                        { name: "Create" },
                    ],
                },
            },
            {
                path: 'asset/edit/:id',
                name: 'CategoryEdit',
                component: CategoryEdit,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Asset", path: "/admin/asset" },
                        { name: "Edit" },
                    ],
                },
            },
            {
                path: 'projects',
                name: 'AdminProjectIndex',
                component: AdminProjectIndex,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Projects" },
                    ],
                },
            },
            {
                path: 'projects/create',
                name: 'AdminProjectCreate',
                component: ProjectCreate,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Projects", path: "/admin/projects" },
                        { name: "Create" },
                    ],
                },
            },
            {
                path: 'projects/show/:id',
                name: 'AdminProjectShow',
                component: ProjectShow,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Projects", path: "/admin/projects" },
                        { name: "Show" },
                    ],
                },
            },
            {
                path: 'projects/edit/:id',
                name: 'AdminProjectEdit',
                component: ProjectEdit,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Projects", path: "/admin/projects" },
                        { name: "Edit" },
                    ],
                },
                props: true,
            },
            {
                path: 'auth-admin-project/:id?',
                name: 'AuthAdminProjectIndex',
                component: AuthAdminProjectIndex,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Company Admins", path: "/admin/admin-user" },
                        { name: "Projects" },
                    ],
                },
            },

            {
                path: 'phases/:project_id?',
                name: 'AdminPhaseIndex',
                component: PhaseIndex,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Phases" },
                    ],
                },
                props: true,
            },
            {
                path: 'phases/create/:project_id',
                name: 'AdminPhaseCreate',
                component: PhaseCreate,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Phases", path: "/admin/phases" },
                        { name: "Create" },
                    ],
                },
                props: true,
            },
            {
                path: 'phases/:id/edit',
                name: 'AdminPhaseEdit',
                component: PhaseEdit,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Phases", path: "/admin/phases" },
                        { name: "Edit" },
                    ],
                },
                props: true,
            },
            {
                path: 'milestones/:phase_id?',
                name: 'AdminMilestoneIndex',
                component: MilestoneIndex,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Milestones" },
                    ],
                },
                props: true,
            },
            {
                path: 'milestones/create/:phase_id',
                name: 'AdminMilestoneCreate',
                component: MilestoneCreate,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Milestones", path: "/admin/milestones" },
                        { name: "Create" },
                    ],
                },
                props: true,
            },
            {
                path: 'milestones/:id/edit',
                name: 'AdminMilestoneEdit',
                component: MilestoneEdit,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Milestones", path: "/admin/milestones" },
                        { name: "Edit" },
                    ],
                },
                props: true,
            },
            {
                path: 'admin-user',
                name: 'AdminUserIndex',
                component: AdminUserIndex,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Company Admins" },
                    ],
                },
            },
            {
                path: 'auth-admin-user/:id?',
                name: 'AuthAdminUserIndex',
                component: AuthAdminUserIndex,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Company Admins", path: "/admin/admin-user" },
                        { name: "Users" },
                    ],
                },
            },
            {
                path: 'admin-user/create',
                name: 'AdminUserCreate',
                component: AdminUserCreate,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Company Admins", path: "/admin/admin-user" },
                        { name: "Create" },
                    ],
                },
            },
            {
                path: 'admin-user/edit/:id',
                name: 'AdminUserEdit',
                component: AdminUserEdit,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Company Admins", path: "/admin/admin-user" },
                        { name: "Edit" },
                    ],
                },
                props: true,
            },

            {
                path: 'entrepreneurs',
                name: 'AdminEntrepreneurIndex',
                component: EntrepreneurIndex,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Entrepreneurs" },
                    ],
                },
            },
            {
                path: 'entrepreneurs/create',
                name: 'AdminEntrepreneurCreate',
                component: EntrepreneurCreate,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Entrepreneurs", path: "/admin/entrepreneurs" },
                        { name: "Create" },
                    ],
                },
            },
            {
                path: 'entrepreneurs/edit/:id',
                name: 'AdminEntrepreneurEdit',
                component: EntrepreneurEdit,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Entrepreneurs", path: "/admin/entrepreneurs" },
                        { name: "Edit" },
                    ],
                },
                props: true,
            },
            // Tenant Routes
            {
                path: 'tenants',
                name: 'AdminTenantIndex',
                component: TenantIndex,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Tenants" },
                    ],
                },
            },
            {
                path: 'tenants/create',
                name: 'AdminTenantCreate',
                component: TenantCreate,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Tenants", path: "/admin/tenants" },
                        { name: "Create" },
                    ],
                },
            },
            {
                path: 'tenants/edit/:id',
                name: 'AdminTenantEdit',
                component: TenantEdit,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Tenants", path: "/admin/tenants" },
                        { name: "Edit" },
                    ],
                },
                props: true,
            },
            // Investor Routes
            {
                path: 'investors',
                name: 'AdminInvestorIndex',
                component: InvestorIndex,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Investors" },
                    ],
                },
            },
            {
                path: 'investors/create',
                name: 'AdminInvestorCreate',
                component: InvestorCreate,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Investors", path: "/admin/investors" },
                        { name: "Create" },
                    ],
                },
            },
            {
                path: 'investors/edit/:id',
                name: 'AdminInvestorEdit',
                component: InvestorEdit,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Investors", path: "/admin/investors" },
                        { name: "Edit" },
                    ],
                },
                props: true,
            },

            //users routes
            {
                path: 'users',
                name: 'AdminChildUserIndex',
                component: UserIndex,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Admin Users" },
                    ],
                },
            },
            {
                path: 'users/create',
                name: 'AdminChildUserCreate',
                component: UserCreate,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Admin Users", path: "/admin/users" },
                        { name: "Create" },
                    ],
                },
            },
            {
                path: 'users/edit/:id',
                name: 'AdminChildUserEdit',
                component: UserEdit,
                meta: {
                    breadcrumbs: [
                        { name: "Dashboard", path: "/" },
                        { name: "Admin Users", path: "/admin/users" },
                        { name: "Edit" },
                    ],
                },
                props: true
            },



        ],
    },
    {
        path: '*',
        beforeEnter: (to, from, next) => {
            next('/404');
        },
    },
    {
        path: '/404',
        name: '404',
        component: Error,
    },
];


const router = new VueRouter({
    mode: 'history',
    base: '/',
    routes
});


router.beforeEach((to, from, next) => {
    const user = getAuthUser();
    const isLoggedIn = hasAuthUser();
    const isAdminRoute = to.matched.some(record => record.meta.authAdminRequired);
    const isUserRoute = to.matched.some(record => record.meta.authRequired);

    // Handle root path redirect
    if (to.path === '/admin/login' || to.path === '/' || to.path === '/admin') {
        if (!isLoggedIn) {
            if (!isLoggedIn || (user?.user?.role !== 'super admin' && user?.user?.role !== 'admin')) {
                removeStorage('auth');
                if (to.name !== 'AdminLogin') {
                    return next({ name: 'AdminLogin' });
                } else {
                    return next(); // already on login, don't redirect
                }
            }
        }

        const role = user?.user?.role;
        if (role === 'super admin' || role === 'admin') {
            return next({ name: 'AdminDashboard' });
        } else {
            return next({ name: 'Dashboard' });
        }
    }

    // Handle admin-only routes
    if (isAdminRoute) {
        if (!isLoggedIn || (user?.user?.role !== 'super admin' && user?.user?.role !== 'admin')) {
            removeStorage('auth');
            return next({ name: 'AdminLogin' });
        }
    }

    // Optional: block admins from accessing user-only pages
    if (isUserRoute) {
        const role = user?.user?.role;
        if (role === 'super admin' || role === 'admin') {
            return next({ name: 'AdminDashboard' });
        }
    }

    // If not logged in and trying to access protected route
    if ((isAdminRoute || isUserRoute) && !isLoggedIn) {
        removeStorage('auth');
        return next({ name: 'AdminLogin' });
    }

    return next();
});





export default router;
