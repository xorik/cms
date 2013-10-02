#!/bin/bash

tag=`git tag | tail -n1`
rev=`git log | grep Author | wc -l`
ci=`git log HEAD~..HEAD | head -n1 | cut --delimiter=' ' -f 2 | head -c 7`
date=`git log HEAD~..HEAD --date=short | grep -o [0-9]*-[0-9]*-[0-9]*`

echo "$tag (r$rev $ci $date)" > modules/res/version
