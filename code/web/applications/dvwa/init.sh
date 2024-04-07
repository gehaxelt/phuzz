#!/bin/bash

# Copy special wordpress overrides
cp /var/www/html/_overrides/*.php /var/www/fuzzer/overrides.d/
chown -R www-data:www-data /var/www/html/