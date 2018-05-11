#!/bin/bash


echo -e "Content-type: text/html\n\n"

HOST=`hostname`
echo "<h1>Raspberry Pi Status: $HOST</h1>"

echo "<h2>Network Info</h2>"
echo "<li>Host name : $(hostname)</li>"
echo "<li>IP Address: $(hostname -I)</li>"

echo "<h2>Up Time</h2>"
echo "<pre>$(uptime)</pre>"

echo "<h2>Top 5 Processes by CPU</h2>"
echo "<pre>$(ps -aux --sort=-pcpu | head -n 6 | cut -c 1-90)</pre>"

echo "<h2>Top 5 Processes by Memory</h2>"
echo "<pre>$(ps -aux --sort=-pmem | head -n 6 | cut -c 1-90)</pre>"


