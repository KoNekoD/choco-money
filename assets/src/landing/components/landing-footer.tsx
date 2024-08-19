import {observer} from "mobx-react-lite";
import React from "react";

export const LandingFooter = observer(() => {
    return (
        <div className="footer_wrap">
            <div className="footer">

                <div className="footer_left">

                    <div className="copyright">
                        <p>Обменный пункт электронных валют MINE.exchange. Все права защищены ©
                            2022<br/>
                            <a href="mailto:info@mine.exchange">info@mine.exchange</a></p>

                    </div>

                    <div className="footer_menu">
                        <ul id="menu-nizhnee-menyu" className="fmenu">
                            <li id="menu-item-635"
                                className="menu-item menu-item-type-post_type menu-item-object-page  first_menu_li menu-item-635">
                                <a href="https://mine.exchange/reviews/"><span>Отзывы</span></a></li>
                            <li id="menu-item-636"
                                className="menu-item menu-item-type-post_type menu-item-object-page menu-item-636">
                                <a href="https://mine.exchange/tos/"><span>Правила сайта</span></a></li>
                            <li id="menu-item-638"
                                className="menu-item menu-item-type-post_type menu-item-object-page menu-item-638">
                                <a href="https://mine.exchange/partnersfaq/"><span>Партнёрам</span></a>
                            </li>
                            <li id="menu-item-823"
                                className="menu-item menu-item-type-post_type menu-item-object-page menu-item-823">
                                <a href="https://mine.exchange/faq/"><span>Вопрос / Ответ</span></a>
                            </li>
                            <li id="menu-item-2096"
                                className="menu-item menu-item-type-custom menu-item-object-custom menu-item-2096">
                                <a href="https://mine.exchange/category/norubrik/"><span>Статьи</span></a>
                            </li>
                            <li id="menu-item-637"
                                className="menu-item menu-item-type-post_type menu-item-object-page menu-item-637">
                                <a href="https://mine.exchange/feedback/"><span>Контакты</span></a></li>
                            <li id="menu-item-634"
                                className="menu-item menu-item-type-post_type menu-item-object-page menu-item-634">
                                <a href="https://mine.exchange/politika-konfidentsialnosti/"><span>Политика конфиденциальности</span></a>
                            </li>
                            <li id="menu-item-856"
                                className="menu-item menu-item-type-custom menu-item-object-custom menu-item-856">
                                <a href="https://mine.exchange/sitemap/"><span>Карта сайта</span></a>
                            </li>
                            <li id="menu-item-1792"
                                className="menu-item menu-item-type-post_type menu-item-object-page  last_menu_li menu-item-1792">
                                <a href="https://mine.exchange/aml-ctf-kyc-policy/"><span>AML/CTF и KYC Политика</span></a>
                            </li>
                        </ul>
                        <div className="clear"></div>
                    </div>

                    <div className="mobile_link">
                                    <span className="hidden-link"
                                          data-link="/ajax-set_site_vers.html?meth=get&amp;yid=66c0707594eb&amp;lang=ru&amp;set=mobile&amp;return_url=%2F">Мобильная версия</span>
                    </div>


                </div>
                <div className="footer_right">

                    <div className="home_partner_div">
                        <div className="home_partner_title">Партнеры</div>

                        <div className="home_partner_one">
                            <a href="https://www.bestchange.ru/mine-exchanger.html" target="_blank">
                                <img src="https://www.bestchange.ru/bestchange.gif" alt=""/>
                            </a>
                        </div>

                        <div className="home_partner_one">
                            <img src="https://perfectmoney.is/img/banners/ru_RU/accepted_2a.jpg"
                                 className="min-h-fit" alt=""/>
                        </div>
                        <div className="home_partner_one">
                            <img src="https://kurs.expert/i/buttonY.png" className="min-h-fit" alt=""/>
                        </div>
                        <div className="home_partner_one">
                            <img src="https://mine.exchange/wp-content/uploads/88x31.gif"
                                 className="min-h-fit" alt=""/>
                        </div>
                        <div className="home_partner_one">
                            <img src="https://www.okchanger.com/images/banners/90x32.png"
                                 className="min-h-fit" alt=""/>
                        </div>
                        <div className="home_partner_one">
                            <img src="https://udifo.com/images/udifo_logo.png" className="min-h-fit"
                                 alt=""/>
                        </div>
                        <div className="clear"></div>
                    </div>
                    <div className="clear"></div>
                </div>

            </div>
            <div className="clear"></div>
        </div>
    )
})