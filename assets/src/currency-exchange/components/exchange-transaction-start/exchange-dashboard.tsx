import {observer} from "mobx-react-lite";
import React, {useEffect, useState} from "react";
import {useStore} from "../../../main/context-provider";
import {useInterval} from "react-use";

export const ExchangeDashboard = observer(
  () => {
    const {currencyExchangeStore} = useStore();

    const [
      creatingTransaction,
      setCreatingTransaction
    ] = useState(false)

    const [
      leadBaseWalletAddress,
      setLeadBaseWalletAddress
    ] = useState('')

    const [
      leadQuoteWalletAddress,
      setLeadQuoteWalletAddress
    ] = useState('')

    const [
      leadEmail,
      setLeadEmail
    ] = useState('')

    useEffect(() => {
      currencyExchangeStore.fetchCurrencies();
      // currencyExchangeStore.fetchCurrencyExchangeIfCurrenciesSelected()
      currencyExchangeStore
        .fetchCurrencyExchangeIfCurrenciesSelectedAndUpdateQuoteAmount()
        .then(r => r)
    }, []);

    useInterval(async () => {
      await currencyExchangeStore
        .fetchCurrencyExchangeIfCurrenciesSelectedAndUpdateQuoteAmount()
    }, 10000) // 10 seconds

    const handleSubmit = () => {
      setCreatingTransaction(true)
      currencyExchangeStore
        .createTransfer(
          leadBaseWalletAddress,
          leadQuoteWalletAddress,
          leadEmail
        ).then(r => r)
    }

    return (
      <div className="xchange_div">
        <div className="xchange_div_ins">
          <div className="xchange_data_title otd">
            <div className="xchange_data_title_ins">Отдаете</div>
          </div>
          <div className="xchange_data_div">
            <div className="xchange_data_ins">
              <div>
                <div className="xchange_select">
                  <div className="select_js iselect_js flex">
                    <div className="select_js_title">
                      <div className="select_js_title_ins">
                        <div className="select_txt">
                          {currencyExchangeStore.selectedBaseAsset}
                        </div>
                      </div>
                    </div>
                    <div className="xchange_sum_line">
                      <div className="xchange_sum_label">
                        Сумма<i className="red">*</i>:
                      </div>

                      <div className="xchange_sum_input js_wrap_error">
                        <input type="text"
                               value=
                                 {currencyExchangeStore.baseCurrencyAmount}
                        />
                        {/*<div className="js_error">*/}
                        {/*max.:<span className="js_amount">150</span> RUB*/}
                        {/*</div>*/}
                      </div>

                      <div className="clear"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div className="xchange_data_left">
                <div className="xchange_curs_line">
                  <div className="xchange_curs_line_ins">
                    <div className="xchange_curs_label">
                      <div className="xchange_curs_label_ins">
                        <label>
                          <span className="xchange_label">
                            С карты/адреса&nbsp;
                            {currencyExchangeStore.selectedBaseAsset}
                            <span className="req">*</span>:
                          </span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div className="xchange_curs_input js_wrap_error">
                    <input type="text"
                           value={leadBaseWalletAddress}
                           onChange={
                             (event) =>
                               setLeadBaseWalletAddress(event.target.value)
                           }
                    />
                  </div>
                  <div className="clear"></div>

                </div>


              </div>
              <div className="clear"></div>
            </div>
          </div>
          <div className="xchange_data_title pol">
            <div className="xchange_data_title_ins">
              <span>Получаете</span>
            </div>
          </div>
          <div className="xchange_data_div">
            <div className="xchange_data_ins">
              {/*<div className="xchange_data_left">*/}
              {/*  <div className="xchange_info_line"><span*/}
              {/*    className="span_skidka">Ваша скидка: 2%</span>*/}
              {/*  </div>*/}
              {/*</div>*/}
              {/*<div className="xchange_data_right">*/}
              {/*  <div className="xchange_info_line"><span*/}
              {/*    className="span_get_max">max.: 149.25435366 BTC</span>*/}
              {/*  </div>*/}
              {/*</div>*/}
              <div className="xchange_select">

                <div className="select_js iselect_js flex">
                  <div className="select_js_title">
                    <div className="select_js_title_ins">
                      <div className="select_txt">
                        {currencyExchangeStore.selectedQuoteAsset}
                      </div>
                    </div>
                  </div>
                  <div className="xchange_sum_line">
                    <div className="xchange_sum_label">
                      Сумма<span className="red">*</span>:
                    </div>

                    <div className="xchange_sum_input js_wrap_error ">
                      <input type="text"
                             value={currencyExchangeStore.quoteCurrencyAmount}
                      />
                      <div className="js_error js_summ2_error"></div>
                    </div>

                    <div className="clear"></div>
                  </div>
                </div>
                <div></div>
              </div>
              <div className="clear"></div>
              <div className="xchange_data_left">
                <div className="xchange_curs_line">
                  <div className="xchange_curs_line_ins">
                    <div className="xchange_curs_label">
                      <div className="xchange_curs_label_ins">
                        <label>
                          <span className="xchange_label">
                        На карту/адрес
                            {currencyExchangeStore.selectedQuoteAsset}
                            <span className="req">*</span>:
                          </span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div className="xchange_curs_input js_wrap_error">
                    <input
                      type="text"
                      value={leadQuoteWalletAddress}
                      onChange={
                        (event) =>
                          setLeadQuoteWalletAddress(event.target.value)
                      }
                    />
                  </div>
                  <div className="clear"></div>
                </div>
              </div>
              <div className="clear"></div>
            </div>
          </div>


          <div className="xchange_pers">
            <div className="xchange_pers_ins">
              <div className="xchange_pers_title">
                <div className="xchange_pers_title_ins">
                  <span>Личные данные</span>
                </div>
              </div>
              <div className="xchange_pers_div">
                <div className="xchange_pers_div_ins">
                  <div className="xchange_pers_line has_help">
                    <div className="xchange_pers_label">
                      <div className="xchange_pers_label_ins">
                        <label>
                        <span className="xchange_label">
                          E-mail
                          <span className="req">*</span>:
                          <span className="help_tooltip_label"></span>
                        </span>
                        </label>
                      </div>
                    </div>
                    <div className="xchange_pers_input">
                      <div
                        className="js_wrap_error">
                        <input type="text"
                               className="cache_data check_cache js_cf6"
                               placeholder=""
                               value={leadEmail}
                               onChange={
                                 (event) =>
                                   setLeadEmail(event.target.value)
                               }
                        />
                        <div className="js_error js_cf6_error"></div>
                      </div>
                    </div>
                    <div className="clear"></div>
                  </div>
                  <div className="clear"></div>
                </div>
              </div>
            </div>
          </div>
          <div className="xchange_submit_div">
            <input type="submit"
                   disabled={creatingTransaction}
                   className="xchange_submit"
                   value="Обменять"
                   onClick={handleSubmit}
            />
            <div className="clear"></div>
          </div>
          <div className="xchange_checkdata_div">
            <div className="checkbox ">
              <input type="checkbox" id="check_data" value="1"/>
              &nbsp;Запомнить введенные данные
            </div>
          </div>
        </div>
      </div>
    )
  })
