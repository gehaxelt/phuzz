import argparse
import json
import os
import queue
import subprocess
import urllib.parse

from playwright.sync_api import sync_playwright

#TIMEOUT = 3000


class RequestExtractor:
    def __init__(self, harfile: str, cookie_path: str, timeout: int) -> None:
        self.harfile = harfile
        self.browser = None
        self.context = None
        self.page = None
        self.visited = set()
        self.queue = queue.Queue()
        self.login_script_path = None
        self.cookie_path = cookie_path
        self.timeout = timeout

    def setup_browser_and_page(self) -> None:
        self.browser = self.playwright.chromium.launch(headless=True)
        self.context = self.browser.new_context(record_har_path=self.harfile)
        self.page = self.context.new_page()

    def is_context_alive(self) -> bool:
        try:
            _ = self.context.pages
            return True
        except Exception:
            return False

    def close_all_other_pages(self, main_page) -> None:
        for page in self.context.pages:
            if page != main_page:
                page.close()

    def interact_with_forms(self) -> None:
        for form in self.page.query_selector_all("form"):
            inputs = form.query_selector_all("input")
            for input_el in inputs:
                input_type = input_el.get_attribute("type")
                if input_type == "text":
                    input_el.fill("test")
                elif input_type == "password":
                    input_el.fill("test123")
                elif input_type == "checkbox":
                    input_el.click()

            buttons = form.query_selector_all("button")
            for btn in buttons:
                try:
                    btn.click()
                    self.page.wait_for_timeout(self.timeout)
                except:
                    pass

            submits = form.query_selector_all("input[type='submit']")
            for btn in submits:
                try:
                    btn.click()
                    self.page.wait_for_timeout(self.timeout)
                except:
                    pass

    def interact_with_clickables(self, original_url: str) -> None:
        clickable_selectors = ["a", "button", "input[type='submit']"]
        main_page = self.page

        for selector in clickable_selectors:
            elements = main_page.query_selector_all(selector)
            for index, _ in enumerate(elements):
                try:
                    self.page.evaluate(
                        f"document.querySelectorAll('{selector}')" f"[{index}].click();"
                    )
                    self.page.wait_for_timeout(self.timeout)
                    self.interact_with_forms()
                except:
                    pass

            self.interact_with_forms()
            self.scroll_page()

        self.close_all_other_pages(main_page)

    def scroll_page(self) -> None:
        try:
            self.page.evaluate(
                "() => { window.scrollTo(0, document.body.scrollHeight); }")
            self.page.wait_for_timeout(self.timeout)
        except:
            pass


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

    def extract_requests(self, url: str) -> None:
        with sync_playwright() as playwright:
            self.playwright = playwright
            self.setup_browser_and_page()

            if self.login_script_path:
                cookies = self.login()
                if cookies:
                    self.set_cookies(cookies)

            self.queue.put(url)

            try:
                while not self.queue.empty():
                    current_url = self.queue.get()
                    if current_url in self.visited or not is_same_domain(
                        url, current_url
                    ):
                        continue
                    self.visited.add(current_url)

                    try:
                        self.page.goto(current_url, timeout=self.timeout)
                        self.interact_with_forms()
                        self.interact_with_clickables(url)
                        self.scroll_page()
                        if not self.is_context_alive():
                            print("Context was destroyed, breaking out...")
                            break
                    except Exception as e:
                        print(f"Error navigating to {current_url}: {e}")

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
    request_extractor = RequestExtractor(args.harfile, args.cookie_path, args.timeout)
    request_extractor.login_script_path = args.login_script_path
    request_extractor.extract_requests(args.entrypoint)


if __name__ == "__main__":
    main()
