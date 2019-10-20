#!/bin/bash

source ../../newsenv/bin/activate
python3 socialmediaAndMoz/mozApi/prepostNoThreadNoProxy.py $1
deactivate
php socialmediaAndMoz/total.php $1

