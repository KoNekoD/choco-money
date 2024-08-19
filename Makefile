include .env
include .env.local
export

OPENAPI_GEN_LINK="https://repo1.maven.org/maven2/org/openapitools/openapi-generator-cli/5.0.0/"$(OPENAPI_GEN_LINK_FILENAME)
OPENAPI_GEN_LINK_FILENAME="openapi-generator-cli-5.0.0.jar"

##################
# Run project
##################
START_FRONTEND:
	@printf "NPM: " && npm --version
	@printf "NODE: " && node --version
	cd assets && npm run dev

START_BACKEND:
	symfony serve

START_BACKEND_JOB_HANDLER:
	bin/console messenger:consume

START_BACKEND_WATCHDOG_SNAPSHOTS_COLLECT:
	bin/exchange/watchdog-snapshots-collect.sh

START_BACKEND_WATCHDOG_CHECK_MONEY_RECEIVED:
	bin/exchange/transfers/watchdog-check-money-received.sh

START_BACKEND_WATCHDOG_FORCE_FINALIZE_OLD_TRANSFERS:
	bin/exchange/transfers/watchdog-force-finalize-old-transfers.sh

START_MONERO_1_DAEMON:
	if ! pgrep monerod > /dev/null; then monerod --data-dir=$(MONERO_STORAGE_DIR); fi

START_MONERO_2_RPC:
	monero-wallet-rpc --rpc-bind-port $(MONERO_WALLET_PORT) --log-level 1 \
    	--wallet-file var/cryptocurrency/wallets/Monero/ChocoMoney.keys \
    	--disable-rpc-login --daemon-address 127.0.0.1:18081 --password 12345678 \
    	--log-file var/log/monero-wallet-rpc.log --max-log-files 3

START_BITCOIN:
	bitcoind -connect=electrum.bot.nu:50002 -prune=550 \
	-rpcuser=choco -rpcpassword=choco -rpcport=$(BITCOIN_WALLET_PORT) \
	-datadir=$(BITCOIN_STORAGE_DIR)


##################
# Utilities
##################
fix_local_problems:
	-bin/console do:da:dr --force
	bin/console do:da:cr
	bin/console do:mi:mi --no-interaction
	-bin/console do:fi:lo --no-interaction

swagger_codegen_regenerate:
	@if [ ! -f "./var/"$(OPENAPI_GEN_LINK_FILENAME) ]; then \
		cd var/ && \
		wget $(OPENAPI_GEN_LINK) && \
		cd ../ \
		true;\
	fi
	bin/console nelmio:apidoc:dump > ./var/generated.json
	-rm -r assets/src/api-client/gen
	java -jar "./var/"$(OPENAPI_GEN_LINK_FILENAME) generate -i var/generated.json \
	-g typescript-axios -o assets/src/api-client/gen/
	rm var/generated.json


##################
# Static code analysis
##################

code_phpstan:
	vendor/bin/phpstan analyse src tests -c phpstan.neon

code_deptrac:
	vendor/bin/deptrac analyze --config-file=deptrac-layers.yaml
	vendor/bin/deptrac analyze --config-file=deptrac-modules.yaml

code_cs_fix:
	vendor/bin/php-cs-fixer fix

code_cs_fix_diff:
	vendor/bin/php-cs-fixer fix --dry-run --diff
