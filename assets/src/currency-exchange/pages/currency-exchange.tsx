import {Outlet} from "react-router-dom";

export const CurrencyExchange = () => {
  return(
      <div className="body w-screen h-screen overflow-y-scroll">
          <Outlet/>
      </div>
  )
}
