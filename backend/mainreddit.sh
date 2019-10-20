#!/bin/bash

source ../../newsenv/bin/activate
python3 socialmediaAndMoz/RedditEtcThread.py $1
deactivate

php socialmediaAndMoz/total.php $1

