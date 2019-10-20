#!/bin/bash

# mysql -u root -p8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db -D "condense" -e "create table \`$1\` (id mediumint unsigned AUTO_INCREMENT primary key, title varchar(255),category varchar(20), isArticleData TINYINT,keywords varchar(512), url VARCHAR( 512 ) NOT NULL unique, image VARCHAR( 612 ), author VARCHAR( 30 ), date DATE, source TINYINT unsigned,fblikes mediumint unsigned, fbshares mediumint unsigned,mozPa TINYINT unsigned, total mediumint unsigned, fbCount mediumint unsigned default '0',reddit MEDIUMINT unsigned, pinterest MEDIUMINT unsigned,charCount MEDIUMINT unsigned,wordCount MEDIUMINT unsigned, sentCount SMALLINT unsigned,titleCount TINYINT unsigned,imgCount TINYINT unsigned,stopWords SMALLINT unsigned,entropy TINYINT unsigned);"
# source ../../newsenv/bin/activate
# python3 cdx-index-client-master/cdx-index-client.py -c CC-MAIN-2019-04 $1/* --fl url -d ../../avcrawled -p 2
# deactivate
# php cdx-index-client-master/insert.php $1

id="i-043d714f99d1e8870"
#start instance.
while true
do
json=$(aws ec2 start-instances --instance-ids $id | jq '.StartingInstances[0].CurrentState.Name')
temp="${json%\"}"
temp="${temp#\"}"
jsonO="$temp"
if [ "$jsonO" = "running" ]
then 
break
fi
sleep 1
done

ip=$(aws ec2 describe-instances --instance-ids ${id} --query 'Reservations[0].Instances[0].PublicIpAddress')
temp="${ip%\"}"
temp="${temp#\"}"
ip="$temp"
ssh -o "StrictHostKeyChecking no" -t ubuntu@$ip "ssh -fNg -L 3307:127.0.0.1:3306 root@139.59.77.178"
ssh -o "StrictHostKeyChecking no" -t ubuntu@$ip "tmux new -s phpThread -d; tmux send-keys -t 'phpThread' \"php fbProxyFullThread.php $1\" C-m"
ssh -o "StrictHostKeyChecking no" -t ubuntu@$ip "tmux send-keys -t 'phpThread' \"aws ec2 stop-instances --instance-ids $id\" C-m"


# source ../../newsenv/bin/activate
# python3 avArticleDataThread.py $1
# deactivate

# mysql -u root -p8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db -D "condense" -e "CREATE INDEX dateI ON \`$1\`(\`date\`);"

# #php socialmediaAndMoz/mozApi/paBatch.php $1

# source ../../newsenv/bin/activate
# python3 socialmediaAndMoz/mozApi/checkMoz.py $1
# python3 socialmediaAndMoz/RedditEtcThread.py $1
# deactivate
