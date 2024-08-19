import React from 'react';
import {observer} from "mobx-react-lite";

interface Props {
  text: string;
  isDone: boolean;
  isFailed: boolean;
}

export const WaitButton =
  observer((props: Props) => {
    return (
      <span className="w-fit relative inline-flex">
        <button type="button"
                className="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-sky-500 bg-white dark:bg-slate-800 transition ease-in-out duration-150 cursor-not-allowed ring-1 ring-slate-900/10 dark:ring-slate-200/20"
                disabled={true}>
          {props.text}
        </button>
        {
          !props.isDone && !props.isFailed && <div>
              <span
                className="flex absolute h-3 w-3 top-0 right-0 -mt-1 -mr-1">
              <span
                className={
                  "animate-ping absolute inline-flex h-full w-full" +
                  " rounded-full bg-sky-400 opacity-75"
                }
              />
              <span
                className="relative inline-flex rounded-full h-3 w-3 bg-sky-500"
              />
              </span>
          </div>
        }

        {
          props.isDone && !props.isFailed && <div>
            <svg
              className="absolute -top-3 -right-3 inline-flex h-8 w-8 rounded-full text-green-400"
              aria-hidden="true"
              fill="none"
              stroke="currentColor"
              strokeWidth="1.5"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </svg>
          </div>
        }

        {
          props.isFailed && <div>
            <svg
              className="absolute -top-3 -right-3 inline-flex h-8 w-8 rounded-full text-red-400"
              aria-hidden="true"
              fill="none"
              stroke="currentColor"
              strokeWidth="1.5"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path
                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"
                strokeLinecap="round"
                strokeLinejoin="round"/>
            </svg>
          </div>
        }

      </span>
    )
  });
