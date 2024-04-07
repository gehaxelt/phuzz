import json
import os

import requests


def get_file_path(file_name):
    return f"{os.path.dirname(os.path.realpath(__file__))}{file_name}"


def main():
    s = requests.Session()

    r = s.post("http://web/users/login.php",
               data={"username": "scanner1", "password": "scanner1"})

    cookies = s.cookies.get_dict()

    with open(
            f"/shared-tmpfs/cookies_node{os.environ['FUZZER_NODE_ID']}.json",
        "w",
    ) as f:
        json.dump(cookies, f)


if __name__ == "__main__":
    main()
