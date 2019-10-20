#!/usr/bin/python

# Benchmarks for 50 urls. 10 threads with tor. 10 threads with without tor
# import time
import requests
import mysql.connector as mariadb
import sys
from bs4 import BeautifulSoup
from stem import Signal
from stem.control import Controller
import concurrent.futures


def mozPa(text):
    try:
        proxy = False
        while True:
            url = text[0]
            params = {"url_form": "{!s}".format(url)}
            if proxy:
                session = requests.session()
                session.proxies['http'] = 'socks5h://127.0.0.1:9050'
                session.proxies['https'] = 'socks5h://127.0.0.1:9050'
                page = session.post(mozApi, data=params)
            else:
                page = requests.post(mozApi, data=params)
            soup = BeautifulSoup(page.text, 'html.parser')

            d = soup.find(
                id="tblstats").tbody.tr.td.next_sibling.next_sibling.next_sibling.string
            mariadb_connection = mariadb.connect(
                host='127.0.0.1', user='root', password='8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db', database='condense')
            cursor = mariadb_connection.cursor()
            cursor.execute(
                "UPDATE `{!s}` set mozPa='{:d}' where url='{!s}'".format(domain, int(d), url))
            mariadb_connection.commit()
            # mariadb_connection.close()
            # print(d)
            # print(time.ctime())
    except AttributeError:
        proxy = True
        with Controller.from_port(port=9051) as controller:
            controller.authenticate(password="password")
            controller.signal(Signal.NEWNYM)
            # print(url)
            # print(soup.body)
    except mariadb.Error as err:
        print("mariadb error", err)
    except ValueError as err:
        print("Value Error", url)
        print(err)
    except TypeError as err:
        print("Type Error", url)
        print(err)
    except:
        print("av: Caught General exception.")
    finally:
        mariadb_connection.close()


domain = sys.argv[1]
mozApi = "https://www.checkmoz.com/"
# We can use a with statement to ensure threads are cleaned up promptly
with concurrent.futures.ThreadPoolExecutor(max_workers=3) as executor:
    try:
        # Start the load operations and mark each future with its URL
        mariadb_connection = mariadb.connect(
            host='127.0.0.1', user='root', password='8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db', database='condense')
        cursor = mariadb_connection.cursor()
        # ts = time.time()
        cursor.execute(
            "SELECT url FROM `{!s}` where mozPA is null order by total desc,fbshares desc limit 50000;".format(domain))
        data = cursor.fetchall()
        future_to_url = {executor.submit(mozPa, text): text for text in data}
        concurrent.futures.wait(future_to_url)
    finally:
        # te = time.time()
        # print(te-ts)
        print("end")
        mariadb_connection.close()
