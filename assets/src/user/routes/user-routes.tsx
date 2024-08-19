import {AuthenticatedRoute} from "../../main/pages/authentcated-route";
import {AccountManagePage} from "../pages/account-manage-page";
import {LoginPage} from "../pages/auth/login-page";
import {RegisterPage} from "../pages/auth/register-page";
import {LogoutPage} from "../pages/auth/logout-page";
import {UnauthentcatedRoute} from "../../main/pages/unauthentcated-route";
import {CreateProfilePage} from "../pages/create-profile-page";

export const userRoutes = [
    {
        path: '/login',
        element: <UnauthentcatedRoute><LoginPage/></UnauthentcatedRoute>,
    },
    {
        path: '/register',
        element: <UnauthentcatedRoute><RegisterPage/></UnauthentcatedRoute>,
    },
    {
        path: '/logout',
        element: <AuthenticatedRoute><LogoutPage/></AuthenticatedRoute>,
    },
    {
        path: '/me',
        element: <AuthenticatedRoute><AccountManagePage/></AuthenticatedRoute>,
    },
    {
        path: '/profile/+',
        element: <AuthenticatedRoute><CreateProfilePage/></AuthenticatedRoute>
    }
];