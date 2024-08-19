import {observer} from "mobx-react-lite";
import React from "react";

interface Props {
  baseAsset: string;
  wallet: string;
}
export const WalletForReceiveMoney = observer((props: Props) => {

  return (
    <div
      className="flex items-center space-x-2 p-4 text-sm text-gray-800 border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
      <svg
        className="w-8 h-8"
        aria-hidden="true"
        fill="none"
        stroke="currentColor"
        strokeWidth="1.5"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg">
        <path
          d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"
          strokeLinecap="round"
          strokeLinejoin="round"
        />
      </svg>
      <span className="sr-only">Wallet</span>
      <div className="flex space-x-2">
        <span>{props.baseAsset} адрес для получения денег:</span>
        <span className="font-bold">{props.wallet}</span>
      </div>
    </div>
  )
})
