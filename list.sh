#!/bin/bash
grep function src/helpers.php |grep -v exists | grep -v static | cut -f5 -d" " --complement | sort | awk ' {print;} NR % 1 == 0 { print ""; }'
