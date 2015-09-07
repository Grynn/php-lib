#!/bin/bash

set -e

git diff --cached --quiet || (echo "Working dir is dirty; cowardly refusing to tag."; exit 1)
# todo check that there is a delta between this tag and the last
# todo generate a changelog from git commits
git tag $(<VERSION)
git push --tags origin master
