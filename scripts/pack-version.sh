#!/bin/sh

if [ -z "$1" ]; then
  echo "Usage: composer pack <directory>"
  exit 1
fi

cd updates
rm -f "$1.tar.gz"
tar -zcvf "$1.tar.gz" "$1/"
cd ..