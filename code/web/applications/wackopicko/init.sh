#!/bin/bash

RET=1
while [[ RET -ne 0 ]]; do
    echo "=> Waiting for confirmation of MySQL service startup"
    sleep 5
    mysql -uroot -ppassword -h db -e "status" > /dev/null 2>&1
    RET=$?
done

mysql -uroot -ppassword -h db < ./current.sql

mysql -uroot -ppassword -h db -e "DROP USER 'admin'@'%'"
mysql -uroot -ppassword -h db -e "CREATE USER 'admin'@'%' IDENTIFIED BY 'adminadmin'"
mysql -uroot -ppassword -h db -e "GRANT ALL PRIVILEGES ON *.* TO 'admin'@'%' WITH GRANT OPTION"

# Copy special wordpress overrides
cp /var/www/html/_overrides/*.php /var/www/fuzzer/overrides.d/

cp -r ./website/* ./
chown -R www-data:www-data ./