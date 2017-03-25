#!/bin/bash

set -e

mkdir -p public/vendor/{css,js}

cp -R `pwd`/vendor/twbs/bootstrap/dist/css/bootstrap.min.css public/vendor/css/bootstrap.min.css
cp -R `pwd`/vendor/twbs/bootstrap/dist/js/bootstrap.min.js public/vendor/js/bootstrap.min.js
cp -R `pwd`/vendor/components/jquery/jquery.min.js public/vendor/js/jquery.min.js
cp -R `pwd`/vendor/twbs/bootstrap/dist/fonts public/vendor/
