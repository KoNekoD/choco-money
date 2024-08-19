#!/bin/bash
while true
do
bin/console app:exchange:snapshots:collect &
sleep 1
done
