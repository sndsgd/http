#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
FILES_DIR="$( realpath $DIR/../files )"


http --form PUT http://localhost:8000 \
name==two-images \
string=abc \
integer=42 \
float=4.2 \
array=1 \
array=2 \
array=3 \
a[nested][value]=thing \
random@"$FILES_DIR/random.png" \
qr@"$FILES_DIR/qr.jpg"


http --form PUT http://localhost:8000 \
name==two-image-array \
string=abc \
integer=42 \
float=4.2 \
array=1 \
array=2 \
array=3 \
a[nested][value]=thing \
image@"$FILES_DIR/random.png" \
image@"$FILES_DIR/qr.jpg"

http --form PUT http://localhost:8000 \
'name==empty-file' \
string=abc \
integer=42 \
float=4.2 \
array=1 \
array=2 \
array=3 \
a[nested][value]=thing \
file@"$FILES_DIR/empty"


http --form PUT http://localhost:8000 \
name==urlencoded \
string=abc \
integer=42 \
float=4.2 \
array=1 \
array=2 \
array=3 \
a[nested][value]=thing
