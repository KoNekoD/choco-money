import {makeAutoObservable} from "mobx";
import {toNumber} from "lodash";
import {TransferDTO} from "../../api-client/gen";

export type TokenStorageType = {
  getToken(): string | null;
  setToken(token: string): void;
  removeToken(): void;
  getRefreshToken(): string | null;
  removeRefreshToken(): void;
  setRefreshToken(token: string): void;
  getProfileSelection(): string | null;
  removeProfileSelection(): void;
  setProfileSelection(id: string): void;
};

export class TokenStorage implements TokenStorageType {
  constructor(
    private accessTokenKey: string,
    private refreshTokenKey: string,
    private selectedProfile: string,
    private currencyExchangeSelectedBaseCurrency: string,
    private currencyExchangeSelectedQuoteCurrency: string,
    private currencyExchangeBaseCurrencyAmount: string,
    private currencyExchangeActiveTransfer: string,
  ) {
    makeAutoObservable(this);
  }

  getToken(): string | null {
    return localStorage.getItem(this.accessTokenKey);
  }

  removeToken(): void {
    localStorage.removeItem(this.accessTokenKey);
  }

  setToken(token: string): void {
    localStorage.setItem(this.accessTokenKey, token);
  }


  getRefreshToken(): string | null {
    return localStorage.getItem(this.refreshTokenKey);
  }

  removeRefreshToken(): void {
    localStorage.removeItem(this.refreshTokenKey);
    this.removeProfileSelection();
  }

  setRefreshToken(token: string): void {
    localStorage.setItem(this.refreshTokenKey, token);
  }

  getProfileSelection(): string | null {
    return localStorage.getItem(this.selectedProfile);
  }

  // Currency exchange
  getCurrencyExchangeSelectedBaseCurrency(): string | null {
    return localStorage.getItem(this.currencyExchangeSelectedBaseCurrency);
  }

  setCurrencyExchangeSelectedBaseCurrency(value: string) {
    localStorage.setItem(this.currencyExchangeSelectedBaseCurrency, value);
  }

  getCurrencyExchangeSelectedQuoteCurrency(): string | null {
    return localStorage.getItem(this.currencyExchangeSelectedQuoteCurrency);
  }

  setCurrencyExchangeSelectedQuoteCurrency(value: string) {
    localStorage.setItem(this.currencyExchangeSelectedQuoteCurrency, value);
  }

  getCurrencyExchangeBaseCurrencyAmount(): number {
    return toNumber(
      localStorage.getItem(this.currencyExchangeBaseCurrencyAmount) ?? 1
    );
  }

  setCurrencyExchangeBaseCurrencyAmount(value: number) {
    localStorage.setItem(this.currencyExchangeBaseCurrencyAmount, String(value));
  }

  // Profile selection
  removeProfileSelection(): void {
    localStorage.removeItem(this.selectedProfile);
  }

  setProfileSelection(id: string): void {
    localStorage.setItem(this.selectedProfile, id);
  }

  setCurrencyExchangeActiveTransfer(transfer: TransferDTO | null): void {
    localStorage.setItem(this.currencyExchangeActiveTransfer, JSON.stringify(transfer))
  }

  getCurrencyExchangeActiveTransfer(): TransferDTO | null {
    const item = localStorage.getItem(
      this.currencyExchangeActiveTransfer
    );
    if (!item) {
      return null
    }

    return JSON.parse(item)
  }
}
