import {ApiClient} from "../../api-client";
import {makeAutoObservable} from "mobx";
import {fromPromise, IPromiseBasedObservable} from "mobx-utils";
import {Notificator} from "../../landing/stores/notificator";
import {
  CurrencyDTO,
  CurrencyExchangeSnapshotDTO,
  DelayedTransferStatusEnum,
  TransferDTO
} from "../../api-client/gen";
import {TokenStorage} from "../../landing/stores/token-storage";
import {toNumber} from "lodash";

// export interface CachedProfilesBufferInterface {
//     profileId: string,
//     profilePromise: IPromiseBasedObservable<ProfileDTO>
// }

export class CurrencyExchangeStore {

  actualCurrenciesList?: IPromiseBasedObservable<CurrencyDTO[]>;

  selectedBaseAsset: string | null;
  selectedQuoteAsset: string | null;

  actualExchangeSnapshot?: IPromiseBasedObservable<CurrencyExchangeSnapshotDTO>;
  previousExchangeSnapshot?: CurrencyExchangeSnapshotDTO | null;

  baseCurrencyAmount: string;
  quoteCurrencyAmount: string;

  activeTransfer: TransferDTO | null;
  activeTransferStatus: DelayedTransferStatusEnum | null;

  // actualProfileInfo?: IPromiseBasedObservable<ProfileDTO>
  // cachedProfilesBuffer: CachedProfilesBufferInterface[] = []

  constructor(
    private client: ApiClient,
    private notificator: Notificator,
    private tokenStorage: TokenStorage,
  ) {
    makeAutoObservable(this)

    this.selectedBaseAsset = tokenStorage.getCurrencyExchangeSelectedBaseCurrency();
    this.selectedQuoteAsset = tokenStorage.getCurrencyExchangeSelectedQuoteCurrency();

    this.previousExchangeSnapshot = null;

    this.baseCurrencyAmount = String(tokenStorage.getCurrencyExchangeBaseCurrencyAmount());
    this.quoteCurrencyAmount = this.baseCurrencyAmount;

    this.activeTransfer = tokenStorage.getCurrencyExchangeActiveTransfer();
    this.activeTransferStatus = null;
  }


  fetchCurrencies(): void {
    this.actualCurrenciesList = fromPromise(
      this.client.currencyExchange.getCurrenciesList().then(res => res.data)
    );
  }

  fetchCurrencyExchangeIfSelectedAnotherCurrency(): void {
    if (null === this.selectedBaseAsset) {
      this.notificator.error('Please select first currency')
    } else if (null === this.selectedQuoteAsset) {
      this.notificator.error('Please select second currency')
    } else {
      this.fetchCurrencyExchangeIfCurrenciesSelectedAndUpdateQuoteAmount().then(r => r)
      // this.actualExchangeSnapshot = fromPromise(
      //     this.client.currencyExchange.getGetCurrencyExchangeSnapshot(
      //         this.selectedBaseCurrencyCode, this.selectedQuoteCurrencyCode
      //     ).then(res => res.data)
      // );
    }
  }

  async fetchCurrencyExchangeIfCurrenciesSelectedAndUpdateQuoteAmount(
  ): Promise<void> {
    if (null !== this.selectedBaseAsset && null !== this.selectedQuoteAsset) {
      this.actualExchangeSnapshot = fromPromise(
        this.client.currencyExchange.getApiGetCurrencyExchangeSnapshot(
          this.selectedBaseAsset, this.selectedQuoteAsset
        ).then(res => res.data)
      );
      let result = await this.actualExchangeSnapshot;
      if (result) {
        this.setPreviousExchangeSnapshot(result);
      }
      if (this.previousExchangeSnapshot) {
        this.setBaseCurrencyAmount(this.baseCurrencyAmount, this.previousExchangeSnapshot.price as number)
      }
    }
  }

  async fetchActiveTransferStatus(
  ): Promise<void> {
    if (this.activeTransfer?.id) {
      this.activeTransferStatus = (
        await fromPromise(
          this.client.currencyExchange.getApiTransferGet(
            this.activeTransfer.id
          ).then(r => r.data)
        )
      );
    }
  }
  selectBaseCurrency(currencyCode: string): void {
    this.tokenStorage.setCurrencyExchangeSelectedBaseCurrency(currencyCode);
    this.selectedBaseAsset = currencyCode;
    this.fetchCurrencyExchangeIfSelectedAnotherCurrency()
  }

  selectQuoteCurrency(currencyCode: string): void {
    this.tokenStorage.setCurrencyExchangeSelectedQuoteCurrency(currencyCode);
    this.selectedQuoteAsset = currencyCode;
    this.fetchCurrencyExchangeIfSelectedAnotherCurrency()
  }

  setBaseCurrencyAmount(amount: string, snapshotPrice: number): void {
    this.baseCurrencyAmount = amount;
    let num = toNumber(amount)
    if (!isNaN(num)) {
      this.tokenStorage.setCurrencyExchangeBaseCurrencyAmount(num);
      this.quoteCurrencyAmount = String(num * snapshotPrice);
    }
  }

  setQuoteCurrencyAmount(amount: string, snapshotPrice: number): void {
    this.quoteCurrencyAmount = amount;
    let num = toNumber(amount)
    if (!isNaN(num)) {
      this.tokenStorage.setCurrencyExchangeBaseCurrencyAmount(num / snapshotPrice);
      this.baseCurrencyAmount = String(num / snapshotPrice);
    }
  }

  setPreviousExchangeSnapshot(previousExchangeSnapshot: CurrencyExchangeSnapshotDTO) {
    this.previousExchangeSnapshot = previousExchangeSnapshot;
  }

  setActiveTransfer(transfer: TransferDTO | null) {
    this.tokenStorage.setCurrencyExchangeActiveTransfer(transfer)
    this.activeTransfer = transfer
  }

  async createTransfer(
    leadBaseWalletAddress: string,
    leadQuoteWalletAddress: string,
    leadEmail: string
  ): Promise<void> {
    if (null === this.selectedBaseAsset) {
      this.notificator.error('Please select first currency')
    } else if (null === this.selectedQuoteAsset) {
      this.notificator.error('Please select second currency')
    } else {
      let result = await this.client.currencyExchange.postApiTransferCreate({
        baseAsset: this.selectedBaseAsset,
        quoteAsset: this.selectedQuoteAsset,
        leadQuoteWalletAddress: leadQuoteWalletAddress,
        leadBaseExchangeAmount: toNumber(this.baseCurrencyAmount),
        leadEmail: leadEmail,
        leadBaseWalletAddress: leadBaseWalletAddress
      })

      if (result.status === 201) {
        this.setActiveTransfer(result.data)
      }
    }
  }
}
