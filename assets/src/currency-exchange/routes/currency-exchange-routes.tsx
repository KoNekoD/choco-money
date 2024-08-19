import {ExchangeTransactionStart} from "../pages/exchange-transaction-start";
import {CurrencyExchange} from "../pages/currency-exchange";
import {ExchangeTransaction} from "../pages/exchange-transaction";

export const currencyExchangeRoutes = [
    {
        path: '/exchange/transaction/',
        element: <CurrencyExchange/>,
        handle: {
            title: () => `Exchange transaction`,
        },
        children: [
            {
              index: true,
              element: <ExchangeTransaction />,
            },
            {
                path: 'start',
                element: <ExchangeTransactionStart/>,
                handle: {
                    title: `Exchange transaction start`,
                },
            },
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
