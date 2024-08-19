import {observer} from "mobx-react-lite";
import React from "react";

export const LandingNews = observer(() => {
    return (
        <div className="home_news_wrap">
            <div className="home_white_blick"></div>

            <div className="home_news_ins">
                <div className="home_news_block">
                    <div className="home_news_title"><a
                        href="https://mine.exchange/category/novosti/">Новости</a></div>


                    <div className="home_news_line">
                        <div className="home_news_one"

                        >
                            <div className="home_news_content">
                                <div className="home_news_content_main">
                                    Мы с гордостью объявляем, что начиная с этой недели,
                                    MINE.exchange увеличивает сумму призового фонда нашего
                                    Пятничного Конкурса в десять раз!
                                </div>
                                <div className="home_news_content_footer">
                                    10.03.2023
                                </div>
                            </div>
                        </div>
                        <div className="home_news_one"

                        >
                            <div className="home_news_content">
                                <div className="home_news_content_main">
                                    Отличная новость! С сегодняшнего дня вы можете обменивать
                                    криптовалюту Polkadot на нашем сервисе.
                                </div>
                                <div className="home_news_content_footer">
                                    21.02.2023
                                </div>
                            </div>
                        </div>
                        <div className="home_news_one"

                        >
                            <div className="home_news_content">
                                <div className="home_news_content_main">
                                    Мы хоть и суровые Шахтеры, но очень романтичные в душе,
                                    поэтому
                                    принимайте наши криптовалентинки!
                                </div>
                                <div className="home_news_content_footer">
                                    14.02.2023
                                </div>
                            </div>
                        </div>

                        <div className="clear"></div>
                    </div>


                </div>
            </div>
        </div>
    )
})