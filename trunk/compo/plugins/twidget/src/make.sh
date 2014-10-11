#!/bin/sh
uglifyjs twidget.js -c -m -o ../twidget.min.js
cleancss twidget.css -e --s0 -o ../twidget.min.css
