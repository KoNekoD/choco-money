import {observer} from "mobx-react-lite";
import React from "react";
import {CurrencyExchangeSnapshotDTO} from "../../api-client/gen";
import {useStore} from "../../main/context-provider";

export const LandingExchangeMenuQuoteAmountInput = observer(() => {
    const {currencyExchangeStore} = useStore();

    let snapshot: CurrencyExchangeSnapshotDTO | undefined;
    if (null !== currencyExchangeStore.previousExchangeSnapshot) {
        snapshot = currencyExchangeStore.previousExchangeSnapshot
    } else if (currencyExchangeStore.actualExchangeSnapshot?.state === 'fulfilled') {
        snapshot = currencyExchangeStore.actualExchangeSnapshot.value
    }

    let snapshot1 = snapshot as CurrencyExchangeSnapshotDTO;

    return (
        <div className="xtp_curs_wrap">
            <div className="xtp_input_wrap js_wrap_error js_wrap_error_br ">
                <input
                    type="text"
                    name=""
                    className="js_summ2"
                    onChange={(event) => {
                        currencyExchangeStore.setQuoteCurrencyAmount(event.target.value, snapshot1.price as number)
                    }}
                    value={
                        currencyExchangeStore.quoteCurrencyAmount
                    }
                />
                <div className="js_error js_summ2_error"></div>
            </div>
            <div className="xtp_select_wrap">
                <div className="select_js">

                    <div className="select_js_title">
                        <div
                            className="select_js_title_ins">{currencyExchangeStore.selectedQuoteAsset}
                        </div>
                    </div>
                    <div className="select_js_ul">
                        <div
                            className="select_js_ulli active">{currencyExchangeStore.selectedQuoteAsset}
                        </div>
                    </div>
                </div>
                <div className={"clear-both"}></div>
            </div>
        </div>
    )
})
