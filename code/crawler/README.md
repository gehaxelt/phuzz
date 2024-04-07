Crawler Component
=====================

The crawler component uses a headless Chrome browser instrumented using the playwright library to crawl the web application and output a HAR file with all discovered endpoints. The crawler performs basic interaction with the web application, e.g. following links and submitting forms.

By default, the entrypoint `http://web/` of the web container, no login scripts and a timeout of 3600s are configured (cf. `crawler.sh`). 

To run the crawler, execute `docker-compose up crawler --build --force-recreate` from the parent directory, after having brought up the `web` container and the database, if needed.

After termination, you will find a `output.har` in the `./crawler/` directory.

```
usage: crawler.py [-h] --entrypoint URL --timeout TIMEOUT --harfile FILENAME [--login-script-path PATH] [--cookie-path PATH]

Extract requests from a website.

options:
  -h, --help            show this help message and exit
  --entrypoint URL      The entrypoint URL to start scraping from.
  --timeout TIMEOUT     The timeout to use for each action.
  --harfile FILENAME    The filename to save the HAR data.
  --login-script-path PATH
                        The full path to the login script module.
  --cookie-path PATH    The path where the cookie file is stored.
```