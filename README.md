# D-Ranking
Checkout the website live at http://139.59.76.192/

## Problem Statement:

As of now Facebook and Google decide the ranking algorithms in their platform. This causes too much centralization. This centralization causes data manipulation and monopoly of data. 

## Solution:

When cryptocurrencies are used to Upvote an article/post, it gets recorded in the blockchain. So other apps can use that data with different ranking algorithms. 

Apps can use ranking parameters like
1. Number of upvotes/cryptos for a post
2. Time since posted
3. Spam content on the post
4. Content-based filtering recommendation system. Users are shown post   similar to what they have upvoted.
5. Collaborative based filtering recommendation system. Users are shown posts which their friends have liked. If two people have transacted, they can be assumed to have had friends.

## Deployment guide:
1. It uses both php and mysql for displaying the website and interacting with the database.So install php and mysql.
2. It uses nodejs for using matic sdk and web3js. So install node and npm. Then go to https://github.com/agnelvishal/DRanking/tree/master/backend and run "npm install" and "node deposit-ERC202.js". This will install and run the code
3. When the code is deployed in the apache server, you will be able to access the website
