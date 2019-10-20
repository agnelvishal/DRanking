#!/usr/bin/python
import time
import requests
import mysql.connector as mariadb
import sys
import re
import json
import concurrent.futures
from stem import Signal
from stem.control import Controller


def load_url(i, text):
    try:
        url = text[0]
        if i < 300:
            proxy = "no"
        else:
            proxy = "yes"
        if proxy == "yes":
            session = requests.session()
            session.proxies['http'] = 'socks5h://127.0.0.1:9050'
            session.proxies['https'] = 'socks5h://127.0.0.1:9050'
            responseRed = session.get("https://www.reddit.com/api/info.json?&url={!s}".format(url), headers={
                                      'User-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36'})
        else:
            responseRed = requests.get("https://www.reddit.com/api/info.json?&url={!s}".format(url), headers={
                                       'User-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36'})
        data = responseRed.json()
        ups = 0
        # downs = 0
        num_crossposts = 0
        num_comments = 0
        totalR = 0
        if 'data' not in data:
            print(data, "          -------No data----------            ")
            with Controller.from_port(port=9051) as controller:
                controller.authenticate(password="password")
                controller.signal(Signal.NEWNYM)
        else:
            for child in data['data']['children']:
                ups = ups + child['data']['ups']
                # downs = downs + child['data']['downs']
                num_crossposts = num_crossposts + \
                    child['data']['num_crossposts']
                num_comments = num_comments + child['data']['num_comments']
            totalR = ups+num_crossposts+num_comments
            mariadb_connection = mariadb.connect(
                host='127.0.0.1', user='root', password='8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db', database='condense')
            cursor = mariadb_connection.cursor()
            cursor.execute(
                "UPDATE `{!s}` set reddit='{:d}' where url='{!s}'".format(domain, totalR, url))
            mariadb_connection.commit()
            mariadb_connection.close()

        # Not using this since mostly returns 0.
        # responseShare = requests.get("http://count-server.sharethis.com/v2.0/get_counts?url={!s}".format(url))
        # data = responseShare.json()
        #
        # if data['total'] > 0:
        #     print(data['total'])
        #     print(url)

        # COULD USE ACCESS TOKEN OR TOR PROXY
        # responsePin = requests.get("https://api.pinterest.com/v1/urls/count.json?access_token=AqDXm_5e6C4rFB0bfaZuZdijia2_FWg77FdWB1BFZkGlukBVbgf9ADAAACb8RWZ1OJkAXPcAAAAA&url={!s}".format(url))
        if proxy == "yes":
            session = requests.session()
            session.proxies['http'] = 'socks5h://127.0.0.1:9050'
            session.proxies['https'] = 'socks5h://127.0.0.1:9050'
            responsePin = session.get(
                "https://api.pinterest.com/v1/urls/count.json?url={!s}".format(url))
        else:
            responsePin = requests.get(
                "https://api.pinterest.com/v1/urls/count.json?access_token=AqDXm_5e6C4rFB0bfaZuZdijia2_FWg77FdWB1BFZkGlukBVbgf9ADAAACb8RWZ1OJkAXPcAAAAA&url={!s}".format(url))

        text = responsePin.text
        if not re.match(r'[_a-zA-Z]', text):
            raise ValueError("Cannot unwrap incorrect JSONP.")

        start = text.index('(') + 1
        stop = text.rindex(')')
        data = text[start:stop]
        Jdata = json.loads(data)
        if 'count' not in Jdata:
            print(Jdata, "          -------No data----------            ")
            with Controller.from_port(port=9051) as controller:
                controller.authenticate(password="password")
                controller.signal(Signal.NEWNYM)
        mariadb_connection = mariadb.connect(
            host='127.0.0.1', user='root', password='8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db', database='condense')
        cursor = mariadb_connection.cursor()
        cursor.execute("UPDATE `{!s}` set pinterest ='{:d}' where url='{!s}'".format(
            domain, Jdata['count'], url))
        mariadb_connection.commit()
        mariadb_connection.close()
    except mariadb.Error as err:
        print(err)
        mariadb_connection.close()
    # except:
    #     print(url)
    #     mariadb_connection.close()


domain = sys.argv[1]
# We can use a with statement to ensure threads are cleaned up promptly
with concurrent.futures.ThreadPoolExecutor(max_workers=30) as executor:
    try:
        # Start the load operations and mark each future with its URL
        mariadb_connection = mariadb.connect(
            host='127.0.0.1', user='root', password='8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db', database='condense')
        cursor = mariadb_connection.cursor()
        ts = time.time()
        cursor.execute(
            "SELECT url FROM `{!s}` where reddit is null and pinterest is null order by total desc,fbshares desc;".format(domain))
        data = cursor.fetchall()
        # proxy="yes"
        future_to_url = {executor.submit(
            load_url, i, text): text for i, text in enumerate(data)}
        concurrent.futures.wait(future_to_url)
    finally:
        te = time.time()
        print(te-ts)
        mariadb_connection.close()
