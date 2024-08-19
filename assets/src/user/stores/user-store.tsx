import {ApiClient} from "../../api-client";
import {Notificator} from "../../landing/stores/notificator";
import {action, makeObservable, observable} from "mobx";
import {
  CreateProfileCommand,
  ProfileDTO,
  UserDTO
} from "../../api-client/zood/gen";
import {fromPromise, IPromiseBasedObservable} from "mobx-utils";

export class UserStore {

    profilesList?: IPromiseBasedObservable<ProfileDTO[]>;
    userInfo?: IPromiseBasedObservable<UserDTO>;

    constructor(
        private client: ApiClient,
        private notificator: Notificator,
    ) {
        makeObservable(this, {
            profilesList: observable,
            userInfo: observable,
            fetchProfilesList: action,
            fetchUserInfo: action,
            createProfile: action,
        });
    }

    async fetchProfilesList(page: number) {
        this.profilesList = fromPromise(
            this.client.profilesApi.getApiProfilesList(page).then(res => res.data)
        );
    }

    async fetchUserInfo() {
        this.userInfo = fromPromise(
            this.client.usersApi.getApiUsersMe().then(res => res.data)
        );
    }

    async createProfile(data: CreateProfileCommand) {
        const result = await this.client.profilesApi.postApiProfilesCreate(data);
        if (result.status !== 200) {
            this.notificator.error(String(result));
            console.log(result);
            return false;
        }
        this.notificator.success("Успешно создан")
        return true;
    }
}
