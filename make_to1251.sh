#!/bin/bash
source_dir="last_version"
target_dir=".last_version"
rm -rf "$target_dir"
mkdir -p "$target_dir"
cp -r "$source_dir"/* "$target_dir"
cd "$target_dir/lang"

find . -type f -print -exec iconv -f utf8 -t windows-1251 -o {}.converted {} \; -exec mv {}.converted {} \;


cd ../..

rm .last_version.tar.gz
tar -zcvf .last_version.tar.gz .last_version/
