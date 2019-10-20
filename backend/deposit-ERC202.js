'use strict';

var express = require('express');
var app = express();


const Matic = require('maticjs').default
const config = require('./config2')

const token = config.ROPSTEN_TEST_TOKEN // test token address
const amount = '1000000000000000000' // amount in wei
var from = config.FROM_ADDRESS // from address

// Create object of Matic
const matic = new Matic({
    maticProvider: config.MATIC_PROVIDER,
    parentProvider: config.PARENT_PROVIDER,
    rootChainAddress: config.ROOTCHAIN_ADDRESS,
    syncerUrl: config.SYNCER_URL,
    watcherUrl: config.WATCHER_URL,
})

matic.wallet = config.PRIVATE_KEY // prefix with `0x`

app.get('/send', async function (req, res) {
    from = req.query.address;
    matic
        .approveERC20TokensForDeposit(token, amount, {
            from,
            onTransactionHash: (hash) => {
                // action on Transaction success
                console.log(hash) // eslint-disable-line
            },
        })
        .then(() => {
            // Deposit tokens
            matic.depositERC20Tokens(token, from, amount, {
                from,
                onTransactionHash: (hash) => {
                    // action on Transaction success
                    console.log(hash) // eslint-disable-line
                    res.json({ hash: hash })
                },
            })
        })

    });

app.listen(3000);
