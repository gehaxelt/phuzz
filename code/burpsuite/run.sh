#!/bin/bash
set -x

mkdir -p ./data/output

"$JAVA_HOME/bin/java" \
	"--add-opens" "java.base/java.lang=ALL-UNNAMED" \
	"--add-opens" "java.base/javax.crypto=ALL-UNNAMED" \
	"--add-opens" "java.desktop/javax.swing=ALL-UNNAMED" \
	"--illegal-access=permit" \
	"-Xmx1024m" \
	"-Djava.awt.headless=true" \
	"-jar" "./burpsuite-pro.jar"\
	"--data-dir=./data/" \
	"--user-config-file=./user.json"\
	"$@"