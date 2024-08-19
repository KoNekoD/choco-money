import {observer} from "mobx-react-lite";
import React from "react";
import {useStore} from "../../main/context-provider";
import {Navigate, useNavigate} from "react-router-dom";
import {
  NoticeMessage
} from "../components/exchange-transaction-start/notice-message";
import {
  WarningMesage
} from "../components/exchange-transaction-start/warning-mesage";
import {
  AnotherExchangeDirections
} from "../components/exchange-transaction-start/another-exchange-directions";
import {Breadcrumbs} from "../../main/components/breadcrumbs";
import {Timer} from "../../main/components/timer";
import {useInterval} from "react-use";
import {DelayedTransferStatusEnum} from "../../api-client/gen";
import {
  WalletForReceiveMoney
} from "../components/exchange-transaction/wallet-for-receive-money";
import {
  ReceiveMoneyAmount
} from "../components/exchange-transaction/receive-money-amount";
import {
  TransferStatus
} from "../components/exchange-transaction/transfer-status";

export const ExchangeTransaction =
  observer(() => {
    const {currencyExchangeStore} = useStore();
    const navigate = useNavigate();

    if (currencyExchangeStore.activeTransfer) {
      useInterval(async () => {
        await currencyExchangeStore
          .fetchActiveTransferStatus()

        const time = Date.parse(currencyExchangeStore.activeTransfer?.expiresAt ?? '2000-01-01') - Date.now();
        const seconds = time / 1000
        if (
          seconds <= 0 ||
          currencyExchangeStore.activeTransferStatus === DelayedTransferStatusEnum.Overdue
        ) {
          // currencyExchangeStore.setActiveTransfer(null)
          // navigate('/exchange/transaction/overdue')
        }

        if (
          currencyExchangeStore.activeTransferStatus === DelayedTransferStatusEnum.Cancelled
        ) {
          currencyExchangeStore.setActiveTransfer(null)
          navigate('/exchange/transaction/cancelled')
        }

        if (
          currencyExchangeStore.activeTransferStatus === DelayedTransferStatusEnum.Exchanged
        ) {
          currencyExchangeStore.setActiveTransfer(null)
          navigate('/exchange/transaction/exchanged')
        }

      }, 1000) // 1 second
    }

    let activeTransfer = currencyExchangeStore.activeTransfer;
    if (activeTransfer) {

      let exchangeStringTitle = (
        `Обмен ${currencyExchangeStore.selectedBaseAsset}` +
        ` на ${currencyExchangeStore.selectedQuoteAsset}`
      );

      return (
        <div className="wrapper">
          <div className="contentwrap">
            <div className="thecontent">
              <Breadcrumbs items={[
                {
                  itemName: 'Обмен валют',
                  linkUrl: 'https://mine.exchange',
                  isLinkExists: true
                },
                {
                  itemName: exchangeStringTitle,
                  linkUrl: null,
                  isLinkExists: false
                },
              ]}/>
              <h1 itemProp="title">{exchangeStringTitle}</h1>
              <div className="ajax_post_bids">
                <div className="exch_ajax_wrap">
                  <div className="w-full flex flex-col space-y-2">
                    <NoticeMessage/>

                    <WalletForReceiveMoney
                      baseAsset={activeTransfer.baseAsset}
                      wallet={activeTransfer.exchangerBaseWalletAddress}
                    />

                    <ReceiveMoneyAmount
                      baseAsset={activeTransfer.baseAsset}
                      exceptedAmount={activeTransfer.leadBaseExchangeAmount}
                    />

                    <TransferStatus/>

                    <div>
                      <span className="text-xl font-bold">Время ожидания:</span>
                      <div className="text-xl">
                        <Timer
                          deadlineString={activeTransfer.expiresAt}
                        />
                      </div>
                    </div>

                    <WarningMesage/>

                    <AnotherExchangeDirections/>
                  </div>
                </div>
              </div>
              <div className="clear"></div>
            </div>
            {/*<Sidebar/>*/
            }
            <div className="clear"></div>
          </div>
        </div>
      )
    } else {
      return (
        <Navigate to={'/exchange/transaction/overdue'}/>
      )
    }
  })
