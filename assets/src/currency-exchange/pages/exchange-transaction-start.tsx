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
import {
  ExchangeDashboard
} from "../components/exchange-transaction-start/exchange-dashboard";

export const ExchangeTransactionStart =
  observer(() => {
    const {currencyExchangeStore} = useStore();
    const navigate = useNavigate();

    if (currencyExchangeStore.activeTransfer) {
      return (
        <Navigate to={'/exchange/transaction/'}/>
      )
    } else {

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
                  <div id="exch_html">
                    <NoticeMessage/>
                    <ExchangeDashboard/>
                    <WarningMesage/>
                    <AnotherExchangeDirections/>
                  </div>
                </div>
              </div>
              <div className="clear"></div>
            </div>
            {/*<Sidebar/>*/}
            <div className="clear"></div>
          </div>
        </div>
      )
    }
  })
