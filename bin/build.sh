#!/bin/bash
DIRECTORY=$(realpath "$(dirname "${BASH_SOURCE[0]}")")/..
ZIP_FILE="$DIRECTORY/yedpay.ocmod.zip"
cd $DIRECTORY

if [[ -f $ZIP_FILE ]]; then
    rm $ZIP_FILE
    echo "$ZIP_FILE exist"
fi

composer update

zip -r $ZIP_FILE upload
