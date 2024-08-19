import {observer} from "mobx-react-lite";
import React from "react";

export const LandingMenu = observer(() => {
    return (
        <div className="tophead_wrap">
            <div className="tophead_ins">
                <div className="tophead">

                    <div className="logoblock">
                        <div className="logoblock_ins">
                            <a href="https://mine.exchange">


                                <img src="/images/logo.svg" className="h-8"/>
                                <h1 className="tagline">Автоматический обмен электронных валют</h1>


                            </a>

                        </div>
                    </div>

                    <div className="topmenu">
                        <ul id="menu-verhnee-menyu" className="tmenu js_menu">
                            <li id="menu-item-824"
                                className="menu-item menu-item-type-post_type menu-item-object-page  first_menu_li menu-item-824">
                                <a href="https://mine.exchange/faq/"><span>FAQ</span></a></li>
                            <li id="menu-item-33"
                                className="menu-item menu-item-type-post_type menu-item-object-page menu-item-33">
                                <a href="https://mine.exchange/reviews/"><span>Отзывы</span></a></li>
                            <li id="menu-item-889"
                                className="menu-item menu-item-type-custom menu-item-object-custom menu-item-889">
                                <a href="https://mine.exchange/category/novosti/"><span>Новости</span></a>
                            </li>
                            <li id="menu-item-306"
                                className="menu-item menu-item-type-post_type menu-item-object-page menu-item-306">
                                <a href="https://mine.exchange/tos/"><span>Правила</span></a></li>
                            <li id="menu-item-412"
                                className="menu-item menu-item-type-post_type menu-item-object-page menu-item-412">
                                <a href="https://mine.exchange/terms/"><span>Партнёрам</span></a></li>
                            <li id="menu-item-1304"
                                className="menu-item menu-item-type-post_type menu-item-object-page  last_menu_li menu-item-1304">
                                <a href="https://mine.exchange/o-nas/"><span>О нас</span></a></li>
                        </ul>
                        <div className="clear"></div>
                    </div>
                    <div className="clear"></div>
                </div>
            </div>
        </div>
    )
})