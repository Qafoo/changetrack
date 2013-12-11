#!/bin/bash
git log --pretty="format:%an (%ae)" | grep -v 'waffle\.io' | sort | uniq -c | sort -n -r | sed 's/^\s*[0-9]\+ //g'
