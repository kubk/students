#!/bin/bash

mkdir public/vendor public/vendor/{css,js}

ln -sf `pwd`/vendor/twbs/bootstrap/dist/css/bootstrap.min.css public/vendor/css/bootstrap.min.css
ln -sf `pwd`/vendor/twbs/bootstrap/dist/js/bootstrap.min.js public/vendor/js/bootstrap.min.js
ln -sf `pwd`/vendor/components/jquery/jquery.min.js public/vendor/js/jquery.min.js
ln -sf `pwd`/vendor/twbs/bootstrap/dist/fonts public/vendor/
