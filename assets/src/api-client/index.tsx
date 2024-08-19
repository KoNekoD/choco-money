import axios, {AxiosStatic} from 'axios';
import {TokenStorageType} from "../landing/stores/token-storage";
import {Notificator} from "../landing/stores/notificator";
import {Configuration, CurrencyExchangeApi, DefaultApi} from "./gen";

export class ApiClient {

    defaultApi: DefaultApi;
    currencyExchange: CurrencyExchangeApi;

    constructor(
        private jwtStorage: TokenStorageType,
        private notificator: Notificator,
        private client: AxiosStatic = axios,
    ) {
        client.interceptors.request.use(async (config) => {
            const token = this.jwtStorage.getToken();
            config.headers.Authorization = token ? `Bearer ${token}` : undefined;
            config.headers.setAccept('application/json');
            config.headers.setContentType('application/json');

            return config;
        });

        let configuration = new Configuration()

        this.defaultApi = new DefaultApi(configuration)
        this.currencyExchange = new CurrencyExchangeApi(configuration)

        this.createAxiosResponseInterceptor(); // Execute the method once during start
    }

    /**
     * Wrap the interceptor in a function, so that I can be re-instantiated
     */
    createAxiosResponseInterceptor() {
        const interceptor = axios.interceptors.response.use(
            (response) => response,
            (error) => {

                if (!error?.response?.status) {
                    return Promise.reject(error);
                }

                if (error.response.status === 500) {
                    this.notificator.error('Internal Server Error 500');
                }
                if (error.response.status === 400) {
                    if (error.response.data['reason'] && !error.response.data['violations']) {
                        this.notificator.error(error.response.data['reason']);
                    }
                }

                // Reject promise if usual error
                if (error.response.status !== 401) {
                    return Promise.reject(error);
                }

                /*
                 * When the response code is 401, try to refresh the token.
                 * Eject the interceptor, so it doesn't loop in case
                 * token refresh causes response 401.
                 *
                 * Must be re-attached later on or the token refresh will only happen once
                 */
                // axios.interceptors.response.eject(interceptor);

                let refresh_token = this.jwtStorage.getRefreshToken();
                if (refresh_token) {

                    // return this.usersApi.postApiAuthTokenRefresh({refresh_token})
                    //     .then((response) => {
                    //
                    //         if (response.data.token && response.data.refresh_token) {
                    //
                    //             this.jwtStorage.setToken(response.data.token);
                    //             this.jwtStorage.setRefreshToken(response.data.refresh_token);
                    //
                    //             error.response.config.headers["Authorization"] =
                    //                 "Bearer " + response.data.token;
                    //             // Retry the initial call, but with the updated token in the headers.
                    //             // Resolves the promise if successful
                    //             return axios(error.response.config);
                    //
                    //         }
                    //     }, (error) => {
                    //         console.log(error)
                    //         this.jwtStorage.removeToken()
                    //         this.jwtStorage.removeRefreshToken()
                    //         this.jwtStorage.removeProfileSelection()
                    //     })
                    //     .catch((error2) => {
                    //         // Retry failed, clean up and reject the promise
                    //
                    //         // this.router.push("/login");
                    //         // window.location.href = '/login';// TODO!
                    //
                    //         return Promise.reject(error2);
                    //     })
                    //     .finally(this.createAxiosResponseInterceptor); // Re-attach the interceptor by running the method

                } else {
                    // Retry failed, clean up and reject the promise
                    this.jwtStorage.removeToken()
                    this.jwtStorage.removeRefreshToken()
                    this.jwtStorage.removeProfileSelection()
                    // this.router.push("/login");
                    // window.location.href = '/login';// TODO!

                    return Promise.reject(error);
                }
            }
        );
    }
}
