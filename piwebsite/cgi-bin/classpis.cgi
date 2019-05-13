#!/bin/bash


echo -e "Content-type: text/html\n\n"


echo "<h1>Class Raspberry Pi Status</h1>"


echo "<p>"
echo "checking class pi's..."

echo "<pre> $(./rpisalive) </pre>"

echo "</p>"

