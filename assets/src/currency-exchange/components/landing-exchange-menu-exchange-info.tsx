import {observer} from "mobx-react-lite";
import React from "react";
import {CurrencyExchangeSnapshotDTO} from "../../api-client/gen";
import {useStore} from "../../main/context-provider";

export const LandingExchangeMenuExchangeInfo = observer(() => {
    const {currencyExchangeStore} = useStore();

    let snapshot: CurrencyExchangeSnapshotDTO | undefined;
    if (null !== currencyExchangeStore.previousExchangeSnapshot) {
        snapshot = currencyExchangeStore.previousExchangeSnapshot
    } else if (currencyExchangeStore.actualExchangeSnapshot?.state === 'fulfilled') {
        snapshot = currencyExchangeStore.actualExchangeSnapshot.value
    }

    let snapshot1 = snapshot as CurrencyExchangeSnapshotDTO;

    return (
        <div>
            <div className="xtp_line xtp_exchange_rate">
                <div>
                    Курс обмена:
                    <span
                        className="js_curs_html">1 {snapshot1.baseAsset} = {snapshot1.price} {snapshot1.quoteAsset}</span>
                </div>


            </div>

            <div className="xtp_line xtp_exchange_reserve">
                Резерв: <span
                className="js_reserv_html">151.04651235 {currencyExchangeStore.selectedQuoteAsset}</span>
            </div>
        </div>
    )
})
