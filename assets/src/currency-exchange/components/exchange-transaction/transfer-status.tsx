import {observer} from "mobx-react-lite";
import React from "react";
import {useStore} from "../../../main/context-provider";
import {DelayedTransferStatusEnum} from "../../../api-client/gen";
import {WaitButton} from "../../../main/components/wait-button";

export const TransferStatus = observer(() => {
  const {currencyExchangeStore} = useStore();

  let status =
    currencyExchangeStore.activeTransferStatus;

  if (status === DelayedTransferStatusEnum.Pending) {
    return <WaitButton
      text={"Ожидание поступления денег"}
      isDone={false}
      isFailed={false}
    />
  } else if (status === DelayedTransferStatusEnum.Cancelled) {
    return <WaitButton
      text={"Обмен был отменен"}
      isDone={false}
      isFailed={true}
    />
  } else if (status === DelayedTransferStatusEnum.Overdue) {
    return <WaitButton
      text={"Истекло время ожидания"}
      isDone={false}
      isFailed={true}
    />
  } else if (status === DelayedTransferStatusEnum.MoneyReceived) {
    return (
      <div>
        <WaitButton
          text={"Ожидание поступления денег"}
          isDone={true}
          isFailed={false}
        />
        <WaitButton
          text={"Деньги получены, подготовка к ответной отправке"}
          isDone={false}
          isFailed={false}
        />
      </div>
    )
  } else if (status === DelayedTransferStatusEnum.MutualMoneySent) {
    return (
      <div>
        <WaitButton
          text={"Ожидание поступления денег"}
          isDone={true}
          isFailed={false}
        />
        <WaitButton
          text={"Деньги получены, подготовка к ответной отправке"}
          isDone={true}
          isFailed={false}
        />
        <WaitButton
          text={"Ответные деньги отправлены, ожидание отзыва"}
          isDone={false}
          isFailed={false}
        />
      </div>
    )
  } else if (status === DelayedTransferStatusEnum.Exchanged) {
    return (
      <div>
        <WaitButton
          text={"Ожидание поступления денег"}
          isDone={true}
          isFailed={false}
        />
        <WaitButton
          text={"Деньги получены, подготовка к ответной отправке"}
          isDone={true}
          isFailed={false}
        />
        <WaitButton
          text={"Ответные деньги отправлены, ожидание отзыва"}
          isDone={true}
          isFailed={false}
        />
        <WaitButton
          text={"Отзыв был оставлен или истекло время ожидания создания отзыва, переадресация на главную страницу"}
          isDone={false}
          isFailed={false}
        />
      </div>
    )
  }
  return (
    <div/>
  )
})
