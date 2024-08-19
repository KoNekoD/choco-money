import {observer} from "mobx-react-lite";
import React from "react";
import {useStore} from "../../main/context-provider";
import {CurrencyExchangeSnapshotDTO} from "../../api-client/gen";

export const LandingExchangeMenuBaseAmountInput = observer(() => {
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
      <div className="xtp_input_wrap js_wrap_error js_wrap_error_br error">
        <input
          type="text"
          name="sum1"
          className="js_summ1 cache_data"
          onChange={(event) => {
            currencyExchangeStore.setBaseCurrencyAmount(event.target.value, snapshot1.price as number)
          }}
          value={currencyExchangeStore.baseCurrencyAmount}
        />
        <div className="js_error js_summ1_error">
          max.:
          <span>{currencyExchangeStore.baseCurrencyAmount}</span> {currencyExchangeStore.selectedBaseAsset}
        </div>
      </div>
      <div className="xtp_select_wrap">
        <div className="select_js">
          <div className="select_js_title">
            <div
              className="select_js_title_ins">{currencyExchangeStore.selectedBaseAsset}</div>
          </div>
          <div className="select_js_ul">
            <div
              className="select_js_ulli active">{currencyExchangeStore.selectedBaseAsset}</div>
          </div>
        </div>
        <div className="clear-both"></div>
      </div>
    </div>
  )
})
