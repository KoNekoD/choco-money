import {observer} from "mobx-react-lite";
import React from "react";

export const LandingAlert = observer(() => {
  return (
      <div className="bg-onPrimary text-primary mb-3">
          <a href="/bestchange-reviews-contest-rules/">
              <div className="trolley">
                  <div className="prize-sum"><span
                      className="trolley_prize_sum">126</span><span
                      className="small"><b>USDT</b></span></div>
              </div>
          </a>
          <div className="rail"></div>
          <div className="rules">
              <a href="/bestchange-reviews-contest-rules/">Оставь отзыв,
                  забери <span>золото</span></a>
              <a href="/bestchange-reviews-contest-rules/"
                 className="no_underline"><span
                  className="trolley_countdown">1д : 8ч : 1м : 42с</span></a>
          </div>
      </div>
  )
})