#!/bin/bash
while true
do
bin/console app:exchange:transfers:check-money-received &
sleep 60
done
