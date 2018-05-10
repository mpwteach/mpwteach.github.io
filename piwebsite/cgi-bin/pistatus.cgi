#!/bin/bash


echo -e "Content-type: text/html\n\n"

HOST=`hostname`
echo "<h1>Raspberry Pi: $HOST</h1>"

echo "<p>"
echo "has been up: "
uptime
echo "</p>"
