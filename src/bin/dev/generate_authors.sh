#!/bin/bash
echo "Contributors sorted by number of contributions (i.e. Git commits):"
echo ""
git log --pretty="format:%an (%ae)" | grep -v 'waffle\.io' | sort | uniq -c | sort -n -r | sed 's/^\s*[0-9]\+ //g'
