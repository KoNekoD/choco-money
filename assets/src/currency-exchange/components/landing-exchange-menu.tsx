import {observer} from "mobx-react-lite";
import React, {useEffect} from "react";
import {useStore} from "../../main/context-provider";
import {Link, useNavigate} from "react-router-dom";
import {
  LandingExchangeMenuExchangeInfo
} from "./landing-exchange-menu-exchange-info";
import {
  LandingExchangeMenuQuoteAmountInput
} from "./landing-exchange-menu-quote-amount-input";
import {
  LandingExchangeMenuBaseAmountInput
} from "./landing-exchange-menu-base-amount-input";
import {useInterval} from "react-use";

export const LandingExchangeMenu = observer(() => {
    const {currencyExchangeStore} = useStore();
    const navigate = useNavigate();

    useEffect(() => {
        currencyExchangeStore.fetchCurrencies();
        // currencyExchangeStore.fetchCurrencyExchangeIfCurrenciesSelected()
        currencyExchangeStore.fetchCurrencyExchangeIfCurrenciesSelectedAndUpdateQuoteAmount().then(r => r)
    }, []);

    useInterval(async () => {
        await currencyExchangeStore.fetchCurrencyExchangeIfCurrenciesSelectedAndUpdateQuoteAmount()
    }, 10000) // 10 seconds


    return (

        <div className="xchange_type_plitka_ins">
            <div className="xtp_table_wrap">
                <div className="xtp_table_wrap_ins">
                    <div className="xtp_col_table_top">
                        <div className="xtp_left_col_table">

                            <div className="xtp_table">
                                <div className="xtp_table_ins">
                                    <div className="xtp_table_title">
                                        <div className="xtp_table_title_ins">
                                            <span>Отдаете</span>
                                            {/*<div className="currency-groups-switch">*/}
                                            {/*    <div*/}
                                            {/*        className="xtp_icon js_icon_left js_icon_left_BTC">*/}
                                            {/*        <div className="xtp_icon_ins">*/}
                                            {/*            <div*/}
                                            {/*                className="xtp_icon_abs"></div>*/}
                                            {/*            BTC*/}
                                            {/*        </div>*/}
                                            {/*    </div>*/}
                                            {/*    <div*/}
                                            {/*        className="xtp_icon js_icon_left js_icon_left_XMR">*/}
                                            {/*        <div className="xtp_icon_ins">*/}
                                            {/*            <div*/}
                                            {/*                className="xtp_icon_abs"></div>*/}
                                            {/*            XMR*/}
                                            {/*        </div>*/}
                                            {/*    </div>*/}
                                            {/*</div>*/}
                                        </div>
                                    </div>
                                    <div className="clear"></div>

                                    <div className="xtp_table_list">
                                        <div className="xtp_table_list_ins">
                                            {currencyExchangeStore.actualCurrenciesList?.state === 'pending' &&
                                                <div>Wail please</div>}
                                            {currencyExchangeStore.actualCurrenciesList?.state === 'rejected' &&
                                                <div>Error</div>}
                                            {
                                                currencyExchangeStore.actualCurrenciesList?.state === 'fulfilled' &&
                                                (
                                                    currencyExchangeStore.actualCurrenciesList.value.length > 0 &&
                                                    currencyExchangeStore.actualCurrenciesList.value.map((currency, i) => {
                                                        return (
                                                            <div key={i}
                                                                 onClick={() => currencyExchangeStore.selectBaseCurrency(currency.asset as string)}
                                                                 className={
                                                                     currency.asset === currencyExchangeStore.selectedBaseAsset &&
                                                                     "xtp_item js_item js_item_left js_item_left_XMR active" ||
                                                                     "xtp_item js_item js_item_left js_item_left_XMR"
                                                                 }>
                                                                <div className="xtp_item_ins">
                                                                    <div className="xtp_item_abs"></div>
                                                                    <div className="xtp_item_ico p-1">
                                                                        <img
                                                                            src={'/images/currencies/' + currency.asset + '.svg'}
                                                                            alt={currency.name}/>
                                                                    </div>
                                                                </div>
                                                                <span>{currency.name}</span>
                                                            </div>
                                                        )
                                                    }) || <div>Пусто</div>
                                                )

                                            }


                                            <div className="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div className="xtp_right_col_table">

                            <div className="xtp_table">
                                <div className="xtp_table_ins">
                                    <div className="xtp_table_title">
                                        <div className="xtp_table_title_ins">
                                            <span>Получаете</span>
                                            {/*<div className="currency-groups-switch">*/}
                                            {/*    <div*/}
                                            {/*        className="xtp_icon js_icon_left js_icon_left_BTC">*/}
                                            {/*        <div className="xtp_icon_ins">*/}
                                            {/*            <div*/}
                                            {/*                className="xtp_icon_abs"></div>*/}
                                            {/*            BTC*/}
                                            {/*        </div>*/}
                                            {/*    </div>*/}
                                            {/*    <div*/}
                                            {/*        className="xtp_icon js_icon_left js_icon_left_XMR">*/}
                                            {/*        <div className="xtp_icon_ins">*/}
                                            {/*            <div*/}
                                            {/*                className="xtp_icon_abs"></div>*/}
                                            {/*            XMR*/}
                                            {/*        </div>*/}
                                            {/*    </div>*/}
                                            {/*</div>*/}
                                        </div>
                                    </div>
                                    <div className="clear"></div>

                                    <div className="xtp_table_list">
                                        <div className="xtp_table_list_ins">
                                            {currencyExchangeStore.actualCurrenciesList?.state === 'pending' &&
                                                <div>Wail please</div>}
                                            {currencyExchangeStore.actualCurrenciesList?.state === 'rejected' &&
                                                <div>Error</div>}
                                            {
                                                currencyExchangeStore.actualCurrenciesList?.state === 'fulfilled' &&
                                                (
                                                    currencyExchangeStore.actualCurrenciesList.value.length > 0 &&
                                                    currencyExchangeStore.actualCurrenciesList.value.map((currency, i) => {
                                                        return (
                                                            <div key={i}
                                                                 onClick={() => currencyExchangeStore.selectQuoteCurrency(currency.asset as string)}
                                                                 className={
                                                                     currency.asset === currencyExchangeStore.selectedQuoteAsset &&
                                                                     "xtp_item js_item js_item_left js_item_left_XMR active" ||
                                                                     "xtp_item js_item js_item_left js_item_left_XMR"
                                                                 }>
                                                                <div className="xtp_item_ins">
                                                                    <div className="xtp_item_abs"></div>
                                                                    <div className="xtp_item_ico p-1">
                                                                        <img
                                                                            src={'/images/currencies/' + currency.asset + '.svg'}
                                                                            alt={currency.name}/>
                                                                    </div>
                                                                </div>
                                                                <span>{currency.name}</span>
                                                            </div>
                                                        )
                                                    }) || <div>Пусто</div>
                                                )

                                            }


                                            <div className="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div className="clear"></div>
                    </div>

                    <div className="xtp_html_wrap">
                        <div className="xtp_html_abs js_loader"></div>
                        <div id="js_html">

                            <div className="xtp_col_table_body">
                                <div className="xtp_left_col_table">
                                    {
                                        (
                                            currencyExchangeStore.actualExchangeSnapshot?.state === 'fulfilled'
                                            ||
                                            null !== currencyExchangeStore.previousExchangeSnapshot
                                        ) &&
                                        <LandingExchangeMenuBaseAmountInput/>
                                    }

                                </div>
                                <div className="xtp_right_col_table">
                                    {
                                        (
                                            currencyExchangeStore.actualExchangeSnapshot?.state === 'fulfilled'
                                            ||
                                            null !== currencyExchangeStore.previousExchangeSnapshot
                                        ) &&
                                        <LandingExchangeMenuQuoteAmountInput/>
                                    }

                                    {
                                        (
                                            currencyExchangeStore.actualExchangeSnapshot?.state === 'fulfilled'
                                            ||
                                            null !== currencyExchangeStore.previousExchangeSnapshot
                                        ) &&
                                        <LandingExchangeMenuExchangeInfo/>
                                    }
                                </div>
                                <div className="clear"></div>
                            </div>
                        </div>
                    </div>

                    <div className="xtp_submit_wrap">
                        <Link to={'/exchange/transaction/start'} className="xtp_submit js_exchange_link" id="js_submit_button">
                            Обменять
                        </Link>
                        <div className="clear"></div>
                    </div>

                    <div id="js_error_div"></div>
                </div>
            </div>
        </div>
    )
})
