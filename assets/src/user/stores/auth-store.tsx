import {action, makeObservable, observable} from "mobx";
import {handleFormSubmit} from "../../main/form/handle-form";
import {TokenStorage} from "../../landing/stores/token-storage";
import {parseJwt} from "../../main/jwt/jwt";
import {Notificator} from "../../landing/stores/notificator";
import {isNull} from "lodash";
import {ApiClient} from "../../api-client";
import {LoginRequest, RegistrationRequest} from "../../api-client/zood/gen";
import {FORM_ERROR} from "final-form";

export type AuthUser = {
    id: string;
    roles: string[];
    username?: string;
    [key: string]: unknown;
};

export class AuthStore {
    user?: AuthUser;
    authenticated: boolean;
    selectedProfileId: string | null;

    constructor(
        private client: ApiClient,
        private tokenStorage: TokenStorage,
        private notificator: Notificator,
    ) {
        this.authenticated = !isNull(this.tokenStorage.getToken())
        this.selectedProfileId = this.tokenStorage.getProfileSelection()

        if (this.authenticated) {
            try {
                this.user = parseJwt<AuthUser>(this.tokenStorage.getToken() as string);
            } catch (e) {
                this.authenticated = false;
                this.logout();
            }

        }
        makeObservable(this, {
            user: observable,
            authenticated: observable,
            selectedProfileId: observable,
            authenticate: action,
            logout: action,
            selectProfile: action,
        });
    }

    get username() {
        return this.user?.username;
    }

    checkPermission(roleToCheck: string): boolean {
        if (roleToCheck === 'ROLE_ANONYMOUS') {
            return true;
        }

        if (roleToCheck === 'ROLE_UNAUTHORIZED' && !this.isAuthenticated()) {
            return true;
        }

        if (roleToCheck === 'ROLE_AUTHORIZED' && this.isAuthenticated()) {
            return true;
        }

        if (roleToCheck === 'ROLE_PROFILE_SELECTED' && this.isProfileSelected() && this.isAuthenticated()) {
            return true;
        }

        if (!this.user) {
            return false;
        }

        return this.user['roles'].find((userRole) => userRole === roleToCheck) !== undefined;
    }

    authenticate(accessToken?: string, refreshToken?: string) {
        const parsedJwt = accessToken || this.tokenStorage.getToken();
        if (!parsedJwt || !refreshToken) {
            return;
        }
        this.tokenStorage.setToken(parsedJwt);
        this.tokenStorage.setRefreshToken(refreshToken);
        try {
            this.user = parseJwt<AuthUser>(parsedJwt);
            this.authenticated = true;
        } catch (e) {
            this.authenticated = false;
            this.logout();
        }
    }

    async submitLoginForm(data: LoginRequest) {
        const result = await handleFormSubmit(this.client.usersApi.postApiAuthTokenLogin(data));
        if (result.errors) {
            this.notificator.error(String(result.errors[FORM_ERROR]));
            return result.errors;
        }
        this.authenticate(result.response!.data.token, result.response!.data.refresh_token);
        this.notificator.success("Успешно авторизован")
    }

    async submitRegisterForm(data: RegistrationRequest) {
        const result = await handleFormSubmit(this.client.usersApi.postAppRegister(data));
        if (result.errors) {
            // Errors 400 notified in api-client
            return result.errors;
        }
        this.authenticate(result.response!.data.token, result.response!.data.refresh_token);
        this.notificator.success("Успешная регистрация")
    }

    logout() {
        this.user = undefined;
        this.tokenStorage.removeToken();
        this.tokenStorage.removeRefreshToken();
        this.tokenStorage.removeProfileSelection();
        this.authenticated = false;
        this.selectedProfileId = null;
    }

    getAccessToken() {
        return this.tokenStorage.getToken();
    }

    isAuthenticated() {
        return this.authenticated;
    }

    private isProfileSelected() {
        return null !== this.selectedProfileId;
    }

    selectProfile(id: string) {
        this.selectedProfileId = id;
        this.tokenStorage.setProfileSelection(id);
    }
}