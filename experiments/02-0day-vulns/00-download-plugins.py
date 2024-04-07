import requests
import time
import random
import urllib.request
import zipfile
import os

MAX_PAGES = 350
CURRENT_PAGE = 1
INSTALL_THRESHOLD = 250 * 1000


DESTDIR = "./plugins/"
DESTFILE = DESTDIR + "download.zip"

if not os.path.exists(DESTDIR):
    os.mkdir(DESTDIR)

while CURRENT_PAGE <= MAX_PAGES:
    try:
        print(f"[*] Sending request to API for page {CURRENT_PAGE}")
        r = requests.get(f"https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[per_page]=3000&request[page]={CURRENT_PAGE}")
        result = r.json()
        MAX_PAGES = result['info']['pages']
        print(f"[*] Obtained page {CURRENT_PAGE}")
        for plugin in result['plugins']:
            if plugin['active_installs'] < INSTALL_THRESHOLD:
                #print(f"[-] Skipping plugin due to too few installs: {plugin['name']}")
                continue
            #if plugin['active_installs'] > 250 * 1000:
            #    continue
            with open("plugin-download.log", "a") as f:
                f.write(f"{plugin['active_installs']},{plugin['name']}\n")
            print(f"[+] Found plugin: {plugin['name']} / {plugin['active_installs']}")
            try:
                print(f"[*] Starting download: {plugin['download_link']}")
                #urllib.request.urlretrieve(plugin['download_link'], DESTFILE)
                urllib.request.urlretrieve(plugin['download_link'], DESTDIR + str(plugin['active_installs']) + "-" +  plugin['download_link'].replace("https://downloads.wordpress.org/plugin/", ""))
                print(f"[+] Downloaded")

            except Exception as e:
                print(e)

        CURRENT_PAGE += 1
        time.sleep(random.randint(1000, 5000)/1000)
    except Exception as e:
        print(e)
