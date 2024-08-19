import {observer} from "mobx-react-lite";
import React from "react";

export const UserWidget = observer(() => {

  return (
    <div className="user_widget">
      <div className="user_widget_ins">

        <div className="user_widget_title">
          <div className="user_widget_title_ins">
            Личный кабинет
          </div>
        </div>

        <div className="uswidin">
          <div className="uswidinleft">Ваша скидка</div>
          <div className="uswidinright">2%</div>
          <div className="clear"></div>
          <div className="uswidinleft">Мой счет</div>
          <div className="uswidinright">0</div>
          <div className="clear"></div>
        </div>

        <div className="user_widget_body">
          <div className="user_widget_body_ins">

            <ul>
              <li className=" "><a href="https://mine.exchange/account/">Личный
                кабинет</a>
              </li>
              <li className=" "><a href="https://mine.exchange/security/">Настройки
                безопасности</a></li>
              <li className=" "><a href="https://mine.exchange/domacc/">Внутренний
                счет</a>
              </li>
              <li className=" "><a href="https://mine.exchange/userxch/">Ваши
                операции</a>
              </li>
              <li className=" "><a
                href="https://mine.exchange/userwallets/">Ваши счета</a>
              </li>
              <li className=" "><a
                href="https://mine.exchange/request-exportxml.xml?lang=ru"
                target="_blank">XML-файл курсов</a></li>
              <li className=" "><a
                href="https://mine.exchange/request-exporttxt.txt?lang=ru"
                target="_blank">TXT-файл курсов</a></li>
              <li className=" "><a href="https://mine.exchange/paccount/">Партнёрский
                аккаунт</a></li>
              <li className=" "><a href="https://mine.exchange/plinks/">Партнёрские
                переходы</a></li>
              <li className=" "><a href="https://mine.exchange/pexch/">Партнёрские
                обмены</a>
              </li>
              <li className=" "><a
                href="https://mine.exchange/preferals/">Рефералы</a></li>
              <li className=" "><a href="https://mine.exchange/payouts/">Вывод
                партнёрских
                средств</a></li>
              <li className=" "><a
                href="https://mine.exchange/partnersfaq/">Партнёрский
                FAQ</a></li>
              <li className=" "><a href="https://mine.exchange/terms/">Условия
                участия в
                партнерской программе</a></li>
            </ul>

            <div className="user_widget_exit">
              <a
                href="/ajax-logout.html?meth=get&amp;yid=3a85c8a2fd5d&amp;lang=ru"
                className="exit_link">Выйти</a>
            </div>

          </div>
        </div>

      </div>
    </div>
  )
})
