import {observer} from "mobx-react-lite";
import React from "react";
import {UserWidget} from "./user-widget";
import {ReviewsWidget} from "./reviews-widget";

export const Sidebar = observer(() => {

  return (
    <div className="sidebar">
      <UserWidget/>
      <ReviewsWidget/>
    </div>
  )
})
