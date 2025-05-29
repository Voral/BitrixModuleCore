#!/bin/sh

rm -f updates/.last_version.tar.gz
tar -zcvf updates/.last_version.tar.gz --transform 's/^src/.last_version/' src/