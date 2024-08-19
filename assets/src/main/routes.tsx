import {createBrowserRouter, RouteObject} from 'react-router-dom';
import {Layout} from './layout';
import {landingRoutes} from "../landing/routes/landing-routes";
import React from "react";
import {NotFoundPage} from "./pages/not-found-page";
import {userRoutes} from "../user/routes/user-routes";
import {
  currencyExchangeRoutes
} from "../currency-exchange/routes/currency-exchange-routes";

const routes: RouteObject[] = [
    {
        element: <Layout/>,
        children: [
            ...landingRoutes,
            ...currencyExchangeRoutes,
            ...userRoutes,
            {
                path: '*',
                element: <NotFoundPage/>,
            },
        ],
    },
    // {
    //   path: '/login',
    //   element: <LoginModal />,
    // },
    // {
    //   path: '/register',
    //   element: <RegisterModal />,
    // },
];

export const browserRouter = createBrowserRouter(routes);
