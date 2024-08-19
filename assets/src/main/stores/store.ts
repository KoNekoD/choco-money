import {TokenStorage} from "../../landing/stores/token-storage";
import {Notificator} from "../../landing/stores/notificator";
import {AuthStore} from "../../user/stores/auth-store";
import {QuerySerializer} from "../routing/query-serializer";
import {ApiClient} from "../../api-client";
import {UserStore} from "../../user/stores/user-store";
import {
  CurrencyExchangeStore
} from "../../currency-exchange/store/currency-exchange-store";

export class RootStore {

    querySerializer = new QuerySerializer('hash');

    private tokenStorage = new TokenStorage(
        'token',
        'refresh_token',
        'selected_profile',
        'currency_exchange_selected_base_currency',
        'currency_exchange_selected_quote_currency',
        'currency_exchange_base_currency_amount',
      'currency_exchange_active_transfer'
    );
    private notificator = new Notificator();
    apiClient = new ApiClient(this.tokenStorage, this.notificator);
    authStore = new AuthStore(this.apiClient, this.tokenStorage, this.notificator);
    userStore = new UserStore(this.apiClient, this.notificator);
    currencyExchangeStore = new CurrencyExchangeStore(this.apiClient, this.notificator, this.tokenStorage);
}
