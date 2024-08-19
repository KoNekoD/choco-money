import {observer} from "mobx-react-lite";
import React from "react";

export const LandingReviews = observer(() => {
    return (
        <div className="home_reviews_wrap">
            <div className="home_reviews_ins">
                <div className="home_reviews_block">
                    <div className="home_reviews_title"><a
                        href="https://mine.exchange/reviews/">Отзывы</a></div>

                    <div className="home_reviews_div">
                        <div className="home_reviews_div_ins">

                            <a href="https://mine.exchange/reviews/">
                                <div className="home_reviews_one">
                                    <div className="home_reviews_content">Все отлично, всё
                                        прошло
                                        успешно рекомендую
                                    </div>
                                    <div className="clear"></div>
                                    <div className="home_reviews_date">Ruzik, 12.04.2023, 10:25
                                    </div>
                                </div>
                            </a>

                            <a href="https://mine.exchange/reviews/">
                                <div className="home_reviews_one">
                                    <div className="home_reviews_content">Быстро и удобно.
                                        Огромное
                                        спасибо!
                                    </div>
                                    <div className="clear"></div>
                                    <div className="home_reviews_date">Yakonsta, 11.04.2023,
                                        21:10
                                    </div>
                                </div>
                            </a>

                            <a href="https://mine.exchange/reviews/">
                                <div className="home_reviews_one">
                                    <div className="home_reviews_content">Менял Сбер на usdt.
                                        Обмен
                                        прошёл быстро и культурно. Рекомендую всем.
                                    </div>
                                    <div className="clear"></div>
                                    <div className="home_reviews_date">Valerii, 11.04.2023,
                                        12:35
                                    </div>
                                </div>
                            </a>


                            <div className="clear"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    )
})