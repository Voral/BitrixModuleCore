#!/bin/sh

rm -f updates/.last_version.tar.gz
tar -zcvf updates/.last_version.tar.gz --transform 's/^last_version/.last_version/' last_version/