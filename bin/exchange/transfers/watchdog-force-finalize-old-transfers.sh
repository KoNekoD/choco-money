#!/bin/bash
while true
do
bin/console app:exchange:transfers:force-finalize-old-transfers &
sleep 60
done
