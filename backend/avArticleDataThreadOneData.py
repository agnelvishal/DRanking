
from newspaper import Article
import mysql.connector as mariadb
from newspaper.article import ArticleException, ArticleDownloadState
import articleDateExtractor
import sys
from time import sleep
import concurrent.futures


def article(text):
    try:
        try:
            url = text[0]
            article = Article(url)
            article.download()
            slept = 0
            while article.download_state == ArticleDownloadState.NOT_STARTED:
                # Raise exception if article download state does not change after 10 seconds
                if slept > 9:
                    raise ArticleException('Download never started')
                sleep(1)
                slept += 1
            article.parse()
            article.nlp()
            mariadb_connectionT = mariadb.connect(
                host='127.0.0.1', user='root', password='8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db', database='condense')
            cursor = mariadb_connectionT.cursor()
            # if article.canonical_link and article.canonical_link != url:
            #     cursor.execute("SELECT fbshares,url FROM `{!s}` where url='{!s}'".format(
            #         domain, article.canonical_link))
            #     data0 = cursor.fetchone()
            #     if data0:
            #         cursor.execute(
            #             "SELECT fbshares  FROM `{!s}` where url='{!s}'".format(domain, url))
            #         data1 = cursor.fetchone()
            #         if int(data1[0] or 0) < int(data0[0] or 0):
            #             cursor.execute(
            #                 "delete FROM `{!s}` where url='{!s}'".format(domain, url))
            #             mariadb_connectionT.commit()
            #             return
            #         else:
            #             cursor.execute("delete FROM `{!s}` where url='{!s}'".format(
            #                 domain, article.canonical_link))
            #             mariadb_connectionT.commit()
            #     else:
            #         cursor.execute("update `{!s}` set url='{!s}' where url='{!s}'".format(
            #             domain, article.canonical_link, url))
            #         mariadb_connectionT.commit()
            article.nlpEntropy()
            keywords = article.keywords
            keywords = ' '.join(keywords)
            d = article.publish_date
            author = "".join(article.authors)
            if len(author) > 30 or not author:
                author = ""
            img = article.top_image
            if not d:
                d = articleDateExtractor.extractArticlePublishedDate(
                    url, article.html)
            if not d:
                return
            cursor.execute("UPDATE `{!s}` set isArticleData = '1', keywords = {!a}, image = {!a}, author={!a} , charCount='{:d}',wordCount='{:d}',stopWords='{:d}',titleCount='{:d}', imgCount = '{:d}', title={!a}, date='{:%Y-%m-%d}' where url='{!s}'".format(
                domain, keywords, img, author, len(article.text), article.totalWords, article.stopWords, len(article.title), len(article.imgs), article.title, d, url))
            mariadb_connectionT.commit()
        except mariadb.Error as err:
            print("db error", err)
        except ValueError as err:
            print("Value Error", url)
            print(err)
        except TypeError as err:
            print("Type Error", url)
            print(err)
        except ArticleException:
            print("Article exception", url)
            return
    finally:
        if cursor:
            cursor.close()
        mariadb_connectionT.close()


domain = sys.argv[1]

with concurrent.futures.ThreadPoolExecutor(max_workers=10) as executor:
    try:
        # Start the load operations and mark each future with its URL
        mariadb_connection = mariadb.connect(
            host='127.0.0.1', user='root', password='8986aeasdf34m88925f1dvpi1691fcd47fcad57fnb88db', database='condense')
        cursor = mariadb_connection.cursor()
        cursor.execute(
            "SELECT url FROM `{!s}` order by total desc ,fbshares desc".format(domain))
        data = cursor.fetchall()
        future_to_url = {executor.submit(article, text): text for text in data}
        concurrent.futures.wait(future_to_url)
    finally:
        print("end")
        mariadb_connection.close()
