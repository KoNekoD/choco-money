import {observer} from "mobx-react-lite";
import React from "react";

export const ReviewsWidget = observer(() => {

  return (
    <div className="widget widget_reviews_div">

      <div className="widget_ins">
        <div className="widget_title">
          <div className="widget_titlevn">
            Отзывы
          </div>
        </div>


        <div className="widget_reviews_line  firstodd">
          <div className="widget_reviews_author">Алесей,</div>
          <div className="widget_reviews_date">19.04.2023, 03:33</div>
          <div className="clear"></div>
          <div className="widget_reviews_content">Отличный сервис,
            особенно по кайфу система
            скидок, деньги сестре перевожу, из-за скидок, считай что без…
          </div>
        </div>

        <div className="widget_reviews_line even">
          <div className="widget_reviews_author">Олег,</div>
          <div className="widget_reviews_date">18.04.2023, 18:41</div>
          <div className="clear"></div>
          <div className="widget_reviews_content">Доволен сервисом, обмен
            произведён быстро и
            качественно по выгодному курсу.. Рекомендую!!
          </div>
        </div>

        <div className="widget_reviews_line lastodd">
          <div className="widget_reviews_author">Игнат,</div>
          <div className="widget_reviews_date">17.04.2023, 16:43</div>
          <div className="clear"></div>
          <div className="widget_reviews_content">Быстро, удобно, курсы
            хорошие.
          </div>
        </div>


        <div className="widget_reviews_more"><a
          href="https://mine.exchange/reviews/">Все отзывы
          (4342)</a></div>
      </div>

    </div>
  )
})
