#!/bin/bash

mysql -u root -p8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db -D "condense" -e "create table \`$1\` (id mediumint unsigned AUTO_INCREMENT primary key, title varchar(255),category varchar(20), isArticleData TINYINT,keywords varchar(512), url VARCHAR( 512 ) NOT NULL unique, image VARCHAR( 612 ), author VARCHAR( 30 ), date DATE, source TINYINT unsigned,fblikes mediumint unsigned, fbshares mediumint unsigned,mozPa TINYINT unsigned, total mediumint unsigned, fbCount mediumint unsigned default '0',reddit MEDIUMINT unsigned, pinterest MEDIUMINT unsigned,charCount MEDIUMINT unsigned,wordCount MEDIUMINT unsigned, sentCount SMALLINT unsigned,titleCount TINYINT unsigned,imgCount TINYINT unsigned,stopWords SMALLINT unsigned,entropy TINYINT unsigned);"
source ../../newsenv/bin/activate
python3 cdx-index-client-master/cdx-index-client.py -c all $1/* --fl url -d ../../avcrawled -p 2
deactivate
php cdx-index-client-master/insert.php $1



# php fbProxyFullThreadL.php $1



# source ../../newsenv/bin/activate
python3 avArticleDataThread.py $1
# deactivate

# mysql -u root -p8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db -D "condense" -e "CREATE INDEX dateI ON \`$1\`(\`date\`);"

# #php socialmediaAndMoz/mozApi/paBatch.php $1

# source ../../newsenv/bin/activate
# python3 socialmediaAndMoz/mozApi/checkMoz.py $1
# python3 socialmediaAndMoz/RedditEtcThread.py $1
# deactivate
