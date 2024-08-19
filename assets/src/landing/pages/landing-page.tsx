import React from "react";
import {LandingHeader} from "../components/landing-header";
import {LandingMenu} from "../components/landing-menu";
import {LandingAlert} from "../components/landing-alert";
import {
  LandingExchangeMenu
} from "../../currency-exchange/components/landing-exchange-menu";
import {LandingNews} from "../components/landing-news";
import {LandingReviews} from "../components/landing-reviews";
import {LandingLastExchange} from "../components/landing-last-exchange";
import {LandingFooter} from "../components/landing-footer";
import {LandingReserves} from "../components/landing-reserves";

function LandingPage() {

    return (
        <section className="body home page-template page-template-pn-homepage page-template-pn-homepage-php page page-id-4 custom-background">
            <LandingHeader/>
            <LandingMenu/>
            <div className="wrapper">
                <div className="homepage_wrap">
                    <div className="xchange_table_wrap">
                        <div className="xchange_type_plitka">
                            <LandingAlert/>
                            <LandingExchangeMenu/>
                        </div>
                    </div>
                    <LandingNews/>
                    <LandingReserves/>
                    <LandingReviews/>
                    <LandingLastExchange/>
                </div>
            </div>
            <LandingFooter/>
        </section>
    );
}

export default LandingPage;
