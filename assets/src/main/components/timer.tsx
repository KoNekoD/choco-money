import React, {useEffect, useState} from 'react';
import {observer} from "mobx-react-lite";
import {useInterval} from "react-use";

interface Props {
  deadlineString?: string;
  deadlineUNIX?: number;
}

export const Timer =
  observer((props: Props) => {
    const [days, setDays] = useState(0);
    const [hours, setHours] = useState(0);
    const [minutes, setMinutes] = useState(0);
    const [seconds, setSeconds] = useState(0);

    let deadlineUNIX: number;

    if (props.deadlineString) {
      deadlineUNIX = Date.parse(props.deadlineString)
    } else if (props.deadlineUNIX) {
      deadlineUNIX = props.deadlineUNIX
    } else {
      deadlineUNIX = Date.parse("December, 31, 2022")
    }

    const getTime = (deadlineUNIX: number) => {

      const time = deadlineUNIX - Date.now();

      setDays(Math.floor(time / (1000 * 60 * 60 * 24)));
      setHours(Math.floor((time / (1000 * 60 * 60)) % 24));
      setMinutes(Math.floor((time / 1000 / 60) % 60));
      setSeconds(Math.floor((time / 1000) % 60));
    };

    useEffect(() => {
      getTime(deadlineUNIX)
    }, [deadlineUNIX]);

    useInterval(() => {
      getTime(deadlineUNIX)
    }, 1000)

    return (
      <div className="flex space-x-2">
        {Object.entries({
          // Days: days,
          // Hours: hours,
          Minutes: minutes,
          Seconds: seconds,
        }).map(([label, value]) => (
          <div key={label}>
            {
              <div>
                <p>{`${Math.floor(value)}`.padStart(2, "0")}</p>
                <span>{label}</span>
              </div>
            }
          </div>
        ))}
      </div>
    );
  });
