import {observer} from "mobx-react-lite";
import React from "react";

export const LandingLastExchange = observer(() => {
    return (
        <div className="home_partner_wrap">
            <div className="home_gray_blick"></div>
            <div className="home_partner_wrap_ins">
                <div className="home_partner_block">

                    <div className="home_lchange_div">
                        <div className="home_lchange_title">Последний обмен</div>
                        <div className="home_lchange_date">13.04.2023, 10:57</div>
                    </div>
                    <div className="home_lchange_body">


                        <div className="home_lchange_arr"></div>

                        <div className="clear"></div>
                    </div>


                    <div className="clear"></div>


                </div>
            </div>
        </div>
    )
})