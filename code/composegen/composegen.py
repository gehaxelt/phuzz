import argparse
import glob
import os
import re
from typing import List


def get_template(application_type, coverage_path) -> str:
    return f"""
services:
  composegen:
    build:
      context: ./composegen
      dockerfile: Dockerfile
    volumes:
      - ./composegen:/app
      - ./fuzzer/configs:/configs
    environment:
      PYTHONUNBUFFERED: 1

  hargen:
    build:
      context: ./hargen
      dockerfile: Dockerfile
    volumes:
      - ./hargen:/app:ro
      - ./fuzzer/resources:/resources:ro
      - ./fuzzer/configs:/configs
    environment:
      PYTHONUNBUFFERED: 1
    stdin_open: true # docker run -i
    tty: true # docker run -t

  crawler:
    build:
      context: ./crawler
      dockerfile: Dockerfile
    volumes:
      - ./crawler:/app:rw
      - ./fuzzer/automated_logins:/automated_logins:ro
    environment:
      PYTHONUNBUFFERED: 1
    stdin_open: true # docker run -i
    tty: true # docker run -t

  web:
    build:
      context: ./web
      dockerfile: Dockerfile
    environment:
      APPLICATION_TYPE: {application_type}
      FUZZER_COVERAGE_PATH: /var/www/html/{coverage_path}
      FUZZER_COMPRESS: 1
      REQUIRES_DB: 1
      #APPLICATION_TYPE: dvwa
      #FUZZER_COVERAGE_PATH: /var/www/html/
      #WP_TARGET_PLUGIN: show-all-comments-in-one-page
    volumes:
      - ./web/applications:/applications/
      - shared-tmpfs:/shared-tmpfs
    ports:
      - 8080:80
      - 8181:8181

  db:
    image: mysql:5
    command: mysqld
    environment:
      MYSQL_DATABASE: db
      MYSQL_USER: user
      MYSQL_PASSWORD: password
      MYSQL_ROOT_PASSWORD: password
      MYSQL_ROOT_HOST: "%"
      MYSQL_USER_HOST: "%"

  burpsuite:
    build:
      context: ./burpsuite
      dockerfile: Dockerfile
    environment:
      FUZZER_CONFIG: dvwa/sqli_low
    volumes:
      - ./fuzzer:/app
      - ./burpsuite/data:/home/burp/data
      - ./burpsuite/.java:/home/burp/.java
    stdin_open: true # docker run -i
    tty: true # docker run -t

  zap:
    build:
      context: ./zap
      dockerfile: Dockerfile
    environment:
      FUZZER_CONFIG: dvwa/sqli_low
    volumes:
      - ./fuzzer:/app
      - ./zap/data:/zap/data
    stdin_open: true # docker run -i
    tty: true # docker run -t

  wapiti:
    build:
      context: ./wapiti
      dockerfile: Dockerfile
    environment:
      FUZZER_CONFIG: dvwa/sqli_low
    volumes:
      - ./fuzzer:/app
      - ./wapiti/data:/home/wapiti/data
    stdin_open: true # docker run -i
    tty: true # docker run -t

  wfuzz:
    build:
      context: ./wfuzz
      dockerfile: Dockerfile
    environment:
      FUZZER_CONFIG: dvwa/sqli_low
    volumes:
      - ./fuzzer:/app
      - ./wfuzz/data:/home/wfuzz/data
    stdin_open: true # docker run -i
    tty: true # docker run -t

__FUZZERS__

volumes:
  shared-tmpfs:
    driver: local
    driver_opts:
      type: "tmpfs"
      device: "tmpfs"
      o: "size=1024m,uid=1000"
__SYNC_TMPFSES__
  """


def generate_sync_tmpfs(config_name: str) -> str:
    return f"""  sync-tmpfs-{config_name.replace("/","-")}:
    driver: local
    driver_opts:
      type: "tmpfs"
      device: "tmpfs"
      o: "size=1024m,uid=1000"
"""


def generate_sync_tmpfses(configs: List[dict]) -> str:
    return "".join(
        [
            generate_sync_tmpfs(config["config_name"])
            for config in configs
        ]
    )


def normalize_string(str):
    return re.sub(r"[^a-z0-9]", "-", str.lower())


def generate_fuzzer(node_index: int, config_index: int, config_name: str) -> str:
    return f"""
  fuzzer-{normalize_string(config_name)}-{config_index}:
    build:
      context: ./fuzzer
      dockerfile: Dockerfile
    environment:
      PYTHONUNBUFFERED: 1
      FUZZER_NODE_ID: {node_index}
      FUZZER_CONFIG: {config_name}
      FUZZER_CLEANUP: 1
      FUZZER_COMPRESS: 1
    volumes:
      - ./fuzzer:/app
      - shared-tmpfs:/shared-tmpfs
      - sync-tmpfs-{config_name.replace("/","-")}:/sync-tmpfs
"""


def generate_fuzzers(node_index: int, num_fuzzers: int, config_name: str) -> str:
    return "".join(
        [
            generate_fuzzer(node_index + config_index,
                            config_index + 1, config_name)
            for config_index in range(num_fuzzers)
        ]
    )


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Generate Docker Compose file")
    parser.add_argument(
        "--output-dir",
        type=str,
        default="/app/",
        required=True,
        help="Directory to save the generated Docker Compose file",
    )

    parser.add_argument(
        "--configs",
        type=str,
        nargs="+",
        help="List of configurations, specified as config_name:num_fuzzers",
    )
    parser.add_argument(
        "--application-type",
        type=str,
        default="dvwa",
        help="The application type",
    )
    parser.add_argument(
        "--config-dir",
        type=str,
        default='/configs/',
        help="Directory where JSON config files are stored",
    )
    parser.add_argument(
        "--num-instances",
        type=int,
        default=4,
        help="Number of instances for all configs in the config directory",
    )
    parser.add_argument(
        "--coverage-path",
        type=str,
        default='',
        help="Path relativ to /var/www/html/ to capture coverage from"
    )
    return parser.parse_args()


def main():
    args = parse_args()

    if args.configs:
        configs = [
            {
                "config_name": c.split(":")[0],
                "num_instances": int(c.split(":")[1])
            }
            for c in args.configs
        ]
    elif args.config_dir:
        if not args.num_instances:
            print(
                "Error: --num-instances must be provided when --config-dir is provided"
            )
            return
        config_files = glob.glob(f"{args.config_dir}/*.json")
        configs = [
            {
                "config_name": os.path.splitext(os.path.basename(f))[0],
                "num_instances": args.num_instances
            }
            for f in config_files
        ]
    else:
        print("Please provide the required parameters.")
        return

    fuzzers = ""
    node_index = 1
    for config in configs:
        num_instances = int(config["num_instances"])
        print(
            f"Generating {num_instances} fuzzer instances for config {config['config_name']}"
        )
        fuzzers += generate_fuzzers(
            node_index, num_instances, config["config_name"]
        )
        node_index += num_instances

    sync_tmpfses = generate_sync_tmpfses(configs)

    output_file = os.path.join(args.output_dir, "docker-compose.yml")
    with open(output_file, "w") as f:
        print(f"Writing {output_file}")
        f.write(
            get_template(args.application_type, args.coverage_path)
            .replace("__FUZZERS__", fuzzers)
            .replace("__SYNC_TMPFSES__", sync_tmpfses)
        )


if __name__ == "__main__":
    main()
