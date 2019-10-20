#!/bin/bash

#
# id="$(aws ec2 run-instances --image-id ami-0bbe6b35405ecebdb --security-group-ids sg-0737513a7a2e0c8ba --count 1 --instance-type m5.2xlarge --query 'Instances[0].InstanceId')"
# temp="${id%\"}"
# temp="${temp#\"}"
# id="$temp"
# ip=$(aws ec2 describe-instances --instance-ids ${id} --query 'Reservations[0].Instances[0].PublicIpAddress')
# temp="${ip%\"}"
# temp="${temp#\"}"
# ip="$temp"
#
#
# # Another
# id="i-0f50b356829542ed9"
# ip="34.222.152.114"
# aws ec2 start-instances --instance-ids $id
# ssh ubuntu@$ip
#
# aws ec2 stop-instances --instance-ids $id

id="i-043d714f99d1e8870"
aws ec2 start-instances --instance-ids $id
#sleep 60
ip=$(aws ec2 describe-instances --instance-ids ${id} --query 'Reservations[0].Instances[0].PublicIpAddress')
temp="${ip%\"}"
temp="${temp#\"}"
ip="$temp"
ssh -o "StrictHostKeyChecking no" -t ubuntu@$ip "ssh -fNg -L 3307:127.0.0.1:3306 root@139.59.77.178"
ssh -o "StrictHostKeyChecking no" -t ubuntu@$ip "tmux new -s phpThread -d; tmux send-keys -t 'phpThread' \"php fbProxyFullThread.php $1\" C-m"
ssh -o "StrictHostKeyChecking no" -t ubuntu@$ip "tmux send-keys -t 'phpThread' \"aws ec2 stop-instances --instance-ids $id\" C-m"
