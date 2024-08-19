import {Outlet} from 'react-router-dom';
import LandingPage from "../pages/landing-page";

export const landingRoutes = [
  {
    path: '/',
    element: <Outlet />,
    handle: {
      title: () => `Home`,
    },
    children: [
      {
        index: true,
        element: <LandingPage />,
      },
      // {
      //   path: ':id/edit',
        //   element: <BookPage />,
        //   handle: {
      //     title: (id?: number) => `${id} edit`,
      //   },
      // },
      // {
      //   path: ':id/edit',
      //   element: <BookEdit />,
      //   handle: {
      //     title: (id?: string) => `${id} edit`,
      //   },
      // },
      // {
      //   path: 'new',
      //   element: <BookCreate />,
      //   handle: {
      //     title: () => 'Create new',
      //   },
      // },
    ],
  },
];
