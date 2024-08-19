import {observer} from "mobx-react-lite";
import React from "react";

export const NoticeMessage = observer(() => {

  return (
    <div className="notice_message">
      <div className="notice_message_ins">
        <div className="notice_message_abs"></div>
        <div className="notice_message_close"></div>
        <div className="notice_message_title">
          <div className="notice_message_title_ins">
            <span>Внимание!</span>
          </div>
        </div>
        <div className="notice_message_text">
          <div className="notice_message_text_ins">
            <div>
              <p>🕒 Данная операция производится в автоматическом
                режиме <span>24/7</span>&nbsp;<span
                >и занимает до&nbsp;</span><span
                >5&nbsp;минут&nbsp; днем&nbsp;</span><span
                >и до</span><span
                >&nbsp;15 мин ночью.</span></p>
              {/*<ul>*/}
                {/*<li>Для совершения обмена нужна <a*/}
                {/*    href="https://mine.exchange/register/"><span*/}
                {/*>регистрация</span></a> и <a*/}
                {/*    href="https://mine.exchange/faq/#card-verification-process"><span*/}
                {/*>верификация</span></a> карты*/}
                {/*    на нашем сайте.*/}
                {/*</li>*/}
                {/*<li>После регистрации зайдите в свой личный кабинет и*/}
                {/*    верифицируйте номер своей карты в разделе <a*/}

                {/*        href="https://mine.exchange/userwallets/">«Ваши*/}
                {/*        счета»</a>.*/}
                {/*</li>*/}
                {/*<li>Заявки суммой до <span*/}
                {/*>10000 рублей</span>,*/}
                {/*    принимаются на карты <span*/}
                {/*    >Qiwi Bank.</span>*/}
                {/*</li>*/}
              {/*</ul>*/}
              {/*<p><span*/}
              {/*>🚨&nbsp;</span>После*/}
              {/*    перехода на страницу оплаты нажмите выберите <strong>«Способ*/}
              {/*        оплаты»</strong><strong>→ </strong><strong>«Картой без*/}
              {/*        регистрации»</strong></p>*/}
              {/*<p>В целях противодействия легализации доходов, полученных*/}
              {/*    преступным путем, и финансированию терроризма проводятcя*/}
              {/*    AML-проверки согласно <strong><a*/}
              {/*        href="https://mine.exchange/aml-ctf-kyc-policy/">AML/CTF и*/}
              {/*        KYC Политики</a></strong></p>*/}
            </div>
            <p>&nbsp;</p>

          </div>
        </div>
      </div>
    </div>
  )
})
