import argparse
import json
import os
import queue
import subprocess
import time
import urllib.parse

from playwright.sync_api import sync_playwright


class RequestExtractor:
    def __init__(self, harfile: str, cookie_path: str, timeout: int, baseurl: str) -> None:
        self.harfile = harfile
        self.browser = None
        self.context = None
        self.page = None
        self.visited = set()
        self.queue = [] #queue.Queue()
        self.login_script_path = None
        self.cookie_path = cookie_path
        self.timeout = timeout
        self.baseurl = baseurl

    def setup_browser_and_page(self) -> None:
        self.browser = self.playwright.chromium.launch(headless=True)
        self.context = self.browser.new_context(record_har_path=self.harfile)
        self.page = self.context.new_page()

    def is_context_alive(self) -> bool:
        try:
            _ = self.context.pages
            return True
        except Exception as e:
            print(f"Error checking context state: {e}")
            return False

    def close_all_other_pages(self, main_page) -> None:
        print("Closing all other pages...")
        for page in self.context.pages:
            if page != main_page:
                page.close()
        print("All other pages closed.")

    def interact_with_forms(self) -> None:
        print("Interacting with forms...")
        print(f"Context alive before interacting with forms: {self.is_context_alive()}")
        for form in self.page.query_selector_all("form"):
            print(f"Found form: {form}")
            inputs = form.query_selector_all("input")
            for input_el in inputs:
                input_type = input_el.get_attribute("type")
                print(f"Found input of type: {input_type}")
                if input_type == "text":
                    input_el.fill("test")
                elif input_type == "password":
                    input_el.fill("test123")
                elif input_type == "checkbox":
                    input_el.click()

            buttons = form.query_selector_all("button")
            for btn in buttons:
                print(f"Found button: {btn}")
                try:
                    btn.click()
                    self.page.wait_for_timeout(self.timeout)
                except Exception as e:
                    print(f"Error clicking button: {e}")
                    self.page.screenshot(path=f"screenshots/{time.time()}_button_click_error.png")
                    print("Screenshot captured for button click error.")

            submits = form.query_selector_all("input[type='submit']")
            for btn in submits:
                print(f"Found submit button: {btn}")
                try:
                    btn.click()
                    self.page.wait_for_timeout(self.timeout)
                except Exception as e:
                    print(f"Error clicking submit: {e}")
                    self.page.screenshot(path=f"screenshots/{time.time()}_submit_click_error.png")
                    print("Screenshot captured for submit click error.")
        print(f"Context alive after interacting with forms: {self.is_context_alive()}")

    def scroll_page(self) -> None:
        print("Scrolling page...")
        print(f"Context alive before scrolling page: {self.is_context_alive()}")
        try:
            self.page.evaluate(
                "() => { window.scrollTo(0, document.body.scrollHeight); }")
            self.page.wait_for_timeout(self.timeout)
        except Exception as e:
            print(f"Error scrolling page: {e}")
            self.page.screenshot(path=f"screenshots/{time.time()}_scroll_error.png")
            print("Screenshot captured for scroll error.")
        print(f"Context alive after scrolling page: {self.is_context_alive()}")


    def set_cookies(self, cookies):
        formatted_cookies = []
        for key, value in cookies.items():
            formatted_cookies.append(
                {"name": key, "value": value, "domain": "localhost", "path": "/"}
            )
        self.context.add_cookies(formatted_cookies)

    def login(self):
        if not self.login_script_path:
            return []

        os.environ["FUZZER_COOKIE_PATH"] = self.cookie_path
        subprocess.run(["python3", self.login_script_path])
        print("Ran login script")

        #cookie_filename = (
        #    "cookies.json"
        #    if not os.environ.get("FUZZER_NODE_ID")
        #    else f"cookies_node{os.environ['FUZZER_NODE_ID']}.json"
        #)
        cookie_filename = "cookies.json"
        with open(os.path.join(self.cookie_path, cookie_filename), "r") as f:
            login_cookies = json.load(f)

        print("Found login cookies", login_cookies)
        return login_cookies

    def collect_links(self):
        print("Collecting links...")
        print(f"Context alive before interacting with clickables: {self.is_context_alive()}")
        main_page = self.page
        ahrefs = main_page.query_selector_all("a")
        #print(f"Found {len(ahrefs)} ahrefs")
        for ahref in ahrefs:
            try:
                href = ahref.get_attribute('href')
                if href.startswith("#"):
                    # Ignore links to elements on the same page
                    continue
                new_url = urllib.parse.urljoin(self.baseurl, href)
                if not new_url.startswith(self.baseurl):
                    continue
                if new_url in self.visited or new_url in self.queue:
                    continue
                print(f"Adding new URL to queue: {new_url}")
                self.queue.append(new_url)
            except Exception as e:
                print(f"Error getting href")
                self.page.screenshot(path=f"screenshots/{time.time()}_clickable_interaction_error.png")
                print("Screenshot captured for clickable interaction error.")

        print(f"Context alive after collecting links: {self.is_context_alive()}")

    def extract_requests(self, url: str) -> None:
        with sync_playwright() as playwright:
            self.playwright = playwright
            self.setup_browser_and_page()

            if self.login_script_path:
                cookies = self.login()
                if cookies:
                    self.set_cookies(cookies)

            self.queue.append(url)

            try:
                while len(self.queue):
                    current_url = self.queue.pop(0)
                    if current_url in self.visited or not is_same_domain(
                        url, current_url
                    ):
                        continue
                    print(f"The queue has {len(self.queue)} URLs left.")
                    print(f"I will work on URL: {current_url}")
                    self.visited.add(current_url)

                    try:
                        print(f"Navigating to: {current_url}")
                        self.page.goto(current_url, timeout=self.timeout)
                        print(f"Page loaded: {current_url}")
                        print(f"Current URL: {self.page.url}")
                        print(f"Current page title: {self.page.title()}")
                        self.scroll_page()
                        self.collect_links()
                        self.interact_with_forms()
                        # self.interact_with_clickables(url)
                        if not self.is_context_alive():
                            print("Context was destroyed, breaking out...")
                            break
                    except Exception as e:
                        print(f"Error navigating to {current_url}: {e}")
                        self.page.screenshot(path=f"screenshots/{time.time()}_navigation_error.png")
                        print("Screenshot captured for navigation error.")

            finally:
                if self.is_context_alive():
                    self.context.close()
                    self.browser.close()


def is_same_domain(original_url: str, new_url: str) -> bool:
    original_domain = urllib.parse.urlparse(original_url).netloc
    new_domain = urllib.parse.urlparse(new_url).netloc
    return original_domain == new_domain


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Extract requests from a website.")
    parser.add_argument(
        "--baseurl",
        metavar="URL",
        type=str,
        required=True,
        help="The baseurl URL to use for URLs",
    )
    parser.add_argument(
        "--entrypoint",
        metavar="URL",
        type=str,
        required=True,
        help="The entrypoint URL to start scraping from.",
    )
    parser.add_argument(
        "--timeout",
        metavar="TIMEOUT",
        type=int,
        required=True,
        help="The timeout to use for each action.",
    )
    parser.add_argument(
        "--harfile",
        metavar="FILENAME",
        type=str,
        required=True,
        help="The filename to save the HAR data.",
    )
    parser.add_argument(
        "--login-script-path",
        metavar="PATH",
        type=str,
        help="The full path to the login script module.",
    )
    parser.add_argument(
        "--cookie-path",
        metavar="PATH",
        type=str,
        help="The path where the cookie file is stored.",
    )
    return parser.parse_args()


def main() -> None:
    args = parse_args()
    request_extractor = RequestExtractor(args.harfile, args.cookie_path, args.timeout, args.baseurl)
    request_extractor.login_script_path = args.login_script_path
    request_extractor.extract_requests(args.entrypoint)


if __name__ == "__main__":
    main()
