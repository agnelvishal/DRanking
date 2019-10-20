#!/usr/bin/python

# Benchmarks for 50 urls. 10 threads with tor. 10 threads with without tor
# import time
import requests
import mysql.connector as mariadb
import sys
import json


domain = sys.argv[1]
mozApi = "https://www.prepostseo.com/ajax/checkAuthority"
# We can use a with statement to ensure threads are cleaned up promptly
try:
        # Start the load operations and mark each future with its URL
    mariadb_connection = mariadb.connect(
        host='127.0.0.1', user='root', password='8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db', database='condense')
    cursor = mariadb_connection.cursor()
    # ts = time.time()
    cursor.execute(
        "SELECT url FROM `{!s}` where mozPA is null order by total desc,fbshares desc limit 50000;".format(domain))
    data = cursor.fetchall()
    for text in data:
        try:
            url = text[0]
            params = {"widget": "1", "url": "{!s}".format(url)}
            page = requests.post(mozApi, data=params, headers={
                                 'x-requested-with': 'XMLHttpRequest'})
            d = json.loads(page.text)['data'][0]['page_auth']
            cursor1 = mariadb_connection.cursor()
            cursor1.execute(
                "UPDATE `{!s}` set mozPa='{:d}' where url='{!s}'".format(domain, d, url))
            mariadb_connection.commit()
            # print(d)
            # print(time.ctime())
        except AttributeError:
            pass
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
        except KeyError:
            pass
            # print (url)
        except:
            print("av: Caught General exception.")
finally:
    # te = time.time()
    # print(te-ts)
    print("end")
    mariadb_connection.close()
