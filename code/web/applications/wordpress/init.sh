#!/bin/bash
#apt-get install -y sudo less curl


cp /usr/local/etc/php/php.ini /usr/local/etc/php/php.ini.fuzzer
cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

mysql -uroot -ppassword -h db -e "status" > /dev/null 2>&1
RET=$?
while [[ RET -ne 0 ]]; do
    echo "=> Waiting for confirmation of MySQL service startup"
    sleep 5
    mysql -uroot -ppassword -h db -e "status" > /dev/null 2>&1
    RET=$?
done

cat > ./install.sh <<EOF
export FUZZER_SETUP=1;
# wget https://github.com/wp-cli/wp-cli/releases/download/v2.7.1/wp-cli-2.7.1.phar
#chmod +x wp-cli-2.7.1.phar && mv ./wp-cli-2.7.1.phar ./wp-cli.phar
chmod +x ./wp-cli.phar
export HOME=/tmp
#./wp-cli.phar core download
./wp-cli.phar config create --dbname=db --dbuser=user --dbpass=password --dbhost=db
./wp-cli.phar core install --url="http://web/" --title="Fuzzer target" --admin_user=admin --admin_email=root@localhost.local --admin_password=admin
./wp-cli.phar plugin auto-updates disable --all

# Download plugins via:  wget https://downloads.wordpress.org/plugin/<plugins-slug>.<version>.zip 

if [[ ${WP_TARGET_PLUGIN} == 'udraw' ]]; then
./wp-cli.phar plugin install ./_plugins/woocommerce.zip --activate
fi
./wp-cli.phar plugin install ./_plugins/${WP_TARGET_PLUGIN}.zip --activate

# SQLi (5/5)
#./wp-cli.phar plugin install ./_plugins/kivicare-clinic-management-system.2.3.8.zip --activate # --version=2.3.8 # SQLi https://wpscan.com//vulnerability/53f493e9-273b-4349-8a59-f2207e8f8f30
#./wp-cli.phar plugin install ./_plugins/nirweb-support.2.7.6.zip --activate # --version=2.7.6 # SQLi https://wpscan.com//vulnerability/1a8f9c7b-a422-4f45-a516-c3c14eb05161
#./wp-cli.phar plugin install ./_plugins/arprice-responsive-pricing-table.3.6.zip --activate # --version=3.6 #SQLi https://wpscan.com//vulnerability/62803aae-9896-410b-9398-3497a838e494
#./wp-cli.phar plugin install ./_plugins/ubigeo-peru.3.6.3.zip --activate # --version=3.6.3 #SQLi https://wpscan.com/vulnerability/fd84dc08-0079-4fcf-81c3-a61d652e3269
#./wp-cli.phar plugin install ./_plugins/photo-gallery.1.6.2.zip --activate # --version=1.6.2 #SQLi https://wpscan.com//vulnerability/2b4866f2-f511-41c6-8135-cf1e0263d8de


# XSS (5/5)
#./wp-cli.phar plugin install ./_plugins/show-all-comments-in-one-page.7.0.0.zip # --version=7.0.0 --activate # XSS https://wpscan.com/vulnerability/4ced1a4d-0c1f-42ad-8473-241c68b92b56
#./wp-cli.phar plugin install ./_plugins/essential-real-estate.3.9.5.zip --version=3.9.5 # --activate # XSS https://wpscan.com/vulnerability/6395f3f1-5cdf-4c55-920c-accc0201baf4
#./wp-cli.phar plugin install ./_plugins/crm-perks-forms.1.0.7.zip --activate # --version=1.0.7 # XSS https://wpscan.com/vulnerability/4b128c9c-366e-46af-9dd2-e3a9624e3a53
#./wp-cli.phar plugin install ./_plugins/rezgo.4.1.6.zip --activate # --version=4.1.6 # XSS https://wpscan.com/vulnerability/005c2300-f6bd-416e-97a6-d42284bbb093
#./wp-cli.phar plugin install ./_plugins/gallery-album.1.9.9.zip --activate # --version=1.9.9 # XSS https://wpscan.com/vulnerability/0903920c-be2e-4515-901f-87253eb30940

# Path Traversal (5/5)
#./wp-cli.phar plugin install ./_plugins/usc-e-shop.2.8.4.zip --activate # --version=2.8.4 # File access https://wpscan.com/vulnerability/0d649a7e-3334-48f7-abca-fff0856e12c7
#./wp-cli.phar plugin install ./_plugins/udraw.3.3.2.zip --activate # --version=3.3.2 # File access https://wpscan.com/vulnerability/925c4c28-ae94-4684-a365-5f1e34e6c151
#./wp-cli.phar plugin install ./_plugins/seo-local-rank.2.2.2.zip --activate # --version=2.2.2 # File access https://wpscan.com/vulnerability/d48e723c-e3d1-411e-ab8e-629fe1606c79
#./wp-cli.phar plugin install ./_plugins/hypercomments.1.2.1.zip --activate # --version=1.2.1 # File deletion https://wpscan.com/vulnerability/d0ff458f-0a1a-4bae-be50-c11d55813458
#./wp-cli.phar plugin install ./_plugins/nmedia-user-file-uploader.21.2.zip --activate # File renaming https://wpscan.com/vulnerability/00f76765-95af-4dbc-8c37-f1b15a0e8608 use curl -i -s -k -X 'POST' --data 'fileid=1&filename=../../../wp-config.php' 'http://localhost:8181/?rest_route=/wpfm/v1/file-rename'


# Deserialization (2/5)
#./wp-cli.phar plugin install ./_plugins/joomsport-sports-league-results-management.5.1.7.zip --activate # --version=5.1.7 # Bas464, but works https://wpscan.com/vulnerability/fb6c407c-713c-4e83-92ce-4e5f791be696
#./wp-cli.phar plugin install ./_plugins/totop-link.1.7.zip --activate # --version=1.7 # https://wpscan.com/vulnerability/518204d8-fbf5-4bfa-9db5-835f908f8d8e

# Open Redirect (5/5)
#./wp-cli.phar plugin install ./_plugins/newsletter-optin-box.1.6.4.zip --activate # https://wpscan.com/vulnerability/c2d2384c-41b9-4aaf-b918-c1cfda58af5c
#./wp-cli.phar plugin install ./_plugins/webp-converter-for-media.4.0.2.zip --activate # https://wpscan.com/vulnerability/f3c0a155-9563-4533-97d4-03b9bac83164
#./wp-cli.phar plugin install ./_plugins/phastpress.1.110.zip --activate # https://wpscan.com/vulnerability/9b3c5412-8699-49e8-b60c-20d2085857fb
#./wp-cli.phar plugin install ./_plugins/all-in-one-wp-security-and-firewall.4.4.0.zip --activate # https://wpscan.com/vulnerability/467673ad-d0ad-46a3-80c7-8ebb3813a4b3
#./wp-cli.phar plugin install ./_plugins/pie-register.3.7.2.3.zip --activate # https://wpscan.com/vulnerability/f6efa32f-51df-44b4-bbba-e67ed5785dd4

# RCE (0/5)
# Hmm, there are not as many RCEs out there, so none match our criteria :-/

# XXE (0/5)
# Same here, XXE do not appear to be common in WP plugins.

# Disable all auto-updates again
./wp-cli.phar plugin auto-updates disable --all
sed -i '/<?php/a define\(\'\''WP_AUTO_UPDATE_CORE\'\'', false\);\ndefine\(\'\''WP_AUTO_UPDATE_PLUGIN\'\'', false\);' /var/www/html/wp-config.php

EOF
chmod +x ./install.sh

# Copy special wordpress overrides
cp /var/www/html/_overrides/*.php /var/www/fuzzer/overrides.d/

chown -R www-data:www-data /var/www/html/
sudo -u www-data -s /bin/bash -c 'bash -c ./install.sh'

cp /usr/local/etc/php/php.ini.fuzzer /usr/local/etc/php/php.ini

a2enmod proxy_http proxy_html headers rewrite
cat > /etc/apache2/sites-enabled/001-wordpress.conf <<EOF
<VirtualHost *:8181>
        ServerAdmin webmaster@localhost
        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

		ProxyPass / http://web/
		ProxyPassReverse / http://web/

		ProxyHTMLURLMap http://web/ /

		<Location />
		   ProxyPassReverse /
		   SetOutputFilter  proxy-html
		   ProxyHTMLURLMap http://web/ /
		   RequestHeader    unset  Accept-Encoding
		</Location>
</VirtualHost>
EOF

grep -qxF 'Listen 8181' /etc/apache2/ports.conf || echo 'Listen 8181' >> /etc/apache2/ports.conf

