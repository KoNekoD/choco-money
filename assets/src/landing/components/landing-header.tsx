import * as React from "react";
import {observer} from "mobx-react-lite";

export const LandingHeader = observer(() => {
    return (
        <div className="topbar_wrap" id="fix_div">
            <div className="topbar_ins absolute top-0" id="fix_elem">
                <div className="topbar">

                    <div className="tolbar_lang">
                        <div className="langlist_div">
                            <div className="langlist_title"><span>ru</span></div>
                            <div className="langlist_ul">

                <span data-link="https://mine.exchange/" className="hidden-link langlist_li ">
                    <div className="langlist_liimg">
                        <img src="https://mine.exchange/wp-content/plugins/premiumbox/flags/ru_RU.png" alt=""/>
                    </div>
                    Русский
                </span>
                                <span data-link="https://mine.exchange/en/"
                                      className="hidden-link langlist_li ">
                    <div className="langlist_liimg">
                        <img src="https://mine.exchange/wp-content/plugins/premiumbox/flags/en_US.png" alt=""/>
                    </div>
                    English
                </span>
                            </div>
                        </div>
                    </div>


                    <div className="topbar_icon feature2">
                        Круглосуточная техподдержка
                    </div>

                    <a href="https://mine.exchange/register/" className="toplink">Регистрация</a>
                    <a href="https://mine.exchange/login/" className="toplink">Войти</a>

                    <div className="clear"></div>
                </div>
            </div>
        </div>
    )
})