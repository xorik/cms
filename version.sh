#!/bin/bash

tag=`git tag | tail -n1`
rev=`git log --oneline | wc -l`
ci=`git log HEAD~..HEAD --oneline | cut --delimiter=' ' -f 1`
date=`git log HEAD~..HEAD --date=short | grep -o [0-9]*-[0-9]*-[0-9]*`

echo "xorik CMS $tag (r$rev $ci $date)" > version
