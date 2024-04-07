import os
import re
import argparse
import sys
import codecs
import io
import json
from zipfile import ZipFile
from typing import List, Optional
from functools import reduce

codecs.register_error("strict", codecs.ignore_errors)

def match_nested_braces(s: str, start: int = 0) -> int:
    open_braces = 0
    for i, c in enumerate(s[start:]):
        if c == "{":
            open_braces += 1
        elif c == "}":
            open_braces -= 1
            if open_braces == 0:
                return start + i
    return -1

def extract_param_names(function_code: str):
    params = {}
    pattern = r"\$_(REQUEST|POST|GET|SERVER|COOKIE|FILES|SESSION)\s*\[\s*?['\"](\w+)['\"]\s*?\]"
        #r"\$_(REQUEST|POST|GET|SERVER|COOKIE|FILES|SESSION)\s*\[\n?\s*['\"](\w+)['\"]\n?\s*\]",
    matches = re.finditer(pattern, function_code, flags=re.DOTALL)
    for match in matches:
        vector = match.group(1).lower()
        if not vector in params:
            params[vector] = []
        param_name = match.group(2)
        if param_name not in params[vector]:
            params[vector].append(param_name)
    return params

def extract_function(file_content: str, function_name: str) -> Optional[str]:
    pattern = r"function[\s]+" + re.escape(function_name) + r"\s*?\([\s\S]*?\)[\s\S]*?\{"
    match = re.search(pattern, file_content, flags=re.DOTALL)
    if match:
        start = match.end() - 1
        end = match_nested_braces(file_content, start)
        if end != -1:
            return file_content[match.start(): end + 1]
    return None

def extract_function_from_zip(inmemzip, function_name):

    # We split up static functions to find them
    if '::' in function_name:
        the_function_name = function_name.split("::")[1]
    else:
        the_function_name = function_name

    for k in inmemzip.keys():
        # if re.search(r"function[\s]+" + re.escape(the_function_name),inmemzip[k]):
        #     return function_name, extract_function(inmemzip[k], the_function_name)
        function_code = extract_function(inmemzip[k], the_function_name)
        if function_code is None:
            continue
        else:
            return function_name, function_code


def _extract_wp_actions(prefix, file_content: str) -> List[str]:
    pattern = r"add_action\s*\(\s*[\'|\"]"+prefix+r"([^\'\"]+)[\'|\"],.*?[\'|\"]([^\'|\"]+)[\'|\"].*?\)"
    return re.findall(pattern, file_content, flags=re.DOTALL)

def extract_wp_priv_nopriv_actions(file_content):
    all_actions_map = _extract_wp_actions("wp_ajax_", file_content)
    priv_actions_functions = list(filter(lambda action: '_nopriv_' not in action, map(lambda y: y[1], all_actions_map)))
    nopriv_actions_functions = list(map(lambda x: x.replace("_nopriv_", ""), filter(lambda action: '_nopriv_' in action, map(lambda y: y[1], all_actions_map))))
    # if all_actions_map:
    #     print("map",all_actions_map)
    #     print("priv",priv_actions_functions)
    #     print("nopriv",nopriv_actions_functions)

    return all_actions_map, priv_actions_functions + nopriv_actions_functions


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser()
    parser.add_argument(
        "-p", "--plugin", type=str, required=True, help="Zipped plugin"
    )
    return parser.parse_args()

def read_zip_to_memory(zip_path):
    # Taken from https://stackoverflow.com/questions/10908877/extracting-a-zipfile-to-memory/10909016#10909016
    input_zip = ZipFile(zip_path)
    return {name: input_zip.read(name).decode('utf-8') for name in input_zip.namelist()}

def generate_fuzzer_config(wp_hook, params):
    config = {}
    config["target"] = "http://web/wp-admin/admin-ajax.php"
    config["methods"] = 'POST' if 'post' in params else 'GET'

    if "nopriv_" in wp_hook:
        wp_hook = wp_hook.replace("nopriv_", "")

    if 'post' in params:
        config["body_params"] = {}
        config["body_params"]['weight'] = 1
        config["body_params"]['fixed'] = []
        config["body_params"]['fuzz'] = params['post']
        config["body_params"]["data"] = []
        for param in params['post']:
            config["body_params"]["data"].append({'name': param, 'value': 'fuzz'})
            if 'nonce' in param.lower():
                config['body_params']['fixed'].append(param)
                config['body_params']['fuzz'].remove(param)

    config["query_params"] = {}
    config["query_params"]['weight'] = 1
    config["query_params"]['fixed'] = ['action']
    config["query_params"]['fuzz'] = []
    config["query_params"]["data"] = [{'name': 'action', 'value': wp_hook}]

    if 'get' in params:
        config["query_params"]['fuzz'] += params['get']
        for param in params['get']:
            config["query_params"]["data"].append({'name': param, 'value': 'fuzz'})
            if 'nonce' in param.lower():
                config['query_params']['fixed'].append(param)
                config['query_params']['fuzz'].remove(param)


    if 'request' in params:
        config["query_params"]['fuzz'] += list(params['request'])
        for param in params['request']:
            config["query_params"]["data"].append({'name': param, 'value': 'fuzz'})
            if 'nonce' in param.lower():
                config['query_params']['fixed'].append(param)
                config['query_params']['fuzz'].remove(param)

    if 'cookie' in params:
        config["cookies"] = {}
        config["cookies"]['weight'] = 1
        config["cookies"]['fixed'] = []
        config["cookies"]['fuzz'] = params['cookie']
        config["cookies"]["data"] = []
        for param in params['cookie']:
            config["cookies"]["data"].append({'name': param, 'value': 'fuzz'})
            if 'nonce' in param.lower():
                config['cookies']['fixed'].append(param)
                config['cookies']['fuzz'].remove(param)

    # We skip $_SEVER, $_FILES, $_SESSION

    config['methods'] = [config['methods']]
    return json.dumps(config, indent=2)

def docker_compose_get_template(application_type, coverage_path, plugin_name) -> str:
    # 2024-04-07: This is an old version of the docker-compose.yml. It would require a change from `./php-instrumentation/` to `./web/` now.
    # We also ran these experiments without the compression of the coverage.
    return f"""version: "3.7"
services:
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

  composegen:
    build:
      context: ./composegen
      dockerfile: Dockerfile
    volumes:
      - ./composegen:/app
      - ./fuzzer/configs:/configs
    environment:
      PYTHONUNBUFFERED: 1

  web:
    build:
      context: ./php-instrumentation
      dockerfile: Dockerfile
    environment:
      APPLICATION_TYPE: {application_type}
      FUZZER_COVERAGE_PATH: /var/www/html/{coverage_path}/
      WP_TARGET_PLUGIN: {plugin_name}
    volumes:
      - ./php-instrumentation/applications:/applications/
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
__BURPSUITE__
__FUZZERS__
volumes:
  shared-tmpfs:
    driver: local
    driver_opts:
      type: "tmpfs"
      device: "tmpfs"
      o: "size=10240m,uid=1000"
__SYNC_TMPFSES__
  """


def docker_compose_generate_sync_tmpfs(config_name: str) -> str:
    return f"""  sync-tmpfs:
    driver: local
    driver_opts:
      type: "tmpfs"
      device: "tmpfs"
      o: "size=20240m,uid=1000"
"""


def docker_compose_generate_sync_tmpfses(configs: List[dict]) -> str:
    return "".join(
        [
            docker_compose_generate_sync_tmpfs(config)
            for config in configs
        ]
    )


def normalize_string(str):
    return re.sub(r"[^a-z0-9]", "-", str.lower())


def docker_compose_generate_fuzzer(node_index: int, config_index: int, fuzzer_config: str, config_name: str) -> str:
    return f"""
  fuzzer-{config_index}:
    build:
      context: ./fuzzer
      dockerfile: Dockerfile
    environment:
      PYTHONUNBUFFERED: 1
      FUZZER_NODE_ID: {node_index}
      FUZZER_CONFIG: {fuzzer_config}
    volumes:
      - ./fuzzer:/app
      - shared-tmpfs:/shared-tmpfs
      - sync-tmpfs:/sync-tmpfs
"""
def docker_compose_generate_burpsuite(fuzzer_config: str, plugin_name: str) -> str:
    return f"""
  burpsuite:
    build:
      context: ./burpsuite
      dockerfile: Dockerfile
    environment:
      FUZZER_CONFIG: {fuzzer_config}
      OUTPUT_DIR: {plugin_name}
    volumes:
      - ./fuzzer:/app
      - ./burpsuite/data:/home/burp/data
      - ./burpsuite/.java:/home/burp/.java
    stdin_open: true # docker run -i
    tty: true # docker run -t
"""


def docker_compose_generate_fuzzers(node_index: int, num_fuzzers: int, fuzzer_config: str, config_name: str) -> str:
    return "".join(
        [
            docker_compose_generate_fuzzer(node_index + config_index,
                            config_index + 1, fuzzer_config, config_name)
            for config_index in range(num_fuzzers)
        ]
    )

def generate_docker_compose(zip_name, wppath_name, config_name):
    fuzzers = ""
    node_index = 1
    num_instances = 10
    fuzzers += docker_compose_generate_fuzzers(
        node_index, num_instances, "wordpress/" + config_name.replace(".json", "").split("/")[-1], wppath_name
    )
    burpsuite = docker_compose_generate_burpsuite(
        "wordpress/" + config_name.replace(".json", "").split("/")[-1],wppath_name
    )
    
    sync_tmpfses = docker_compose_generate_sync_tmpfses([wppath_name])


    docker_config = docker_compose_get_template("wordpress", os.path.join("wp-content/plugins/",wppath_name), zip_name.replace(".zip","")).replace("__FUZZERS__", fuzzers).replace("__SYNC_TMPFSES__", sync_tmpfses)
    docker_config = docker_config.replace("__BURPSUITE__", burpsuite)

    return docker_config

def generate_config(plugin_zip_name,plugin_wppath_name,hook_func,hook_names,params):
    configs_dir = "configs"
    if not os.path.exists(configs_dir):
        os.mkdir(configs_dir)
    
    plugin_config_dir = os.path.join(configs_dir, plugin_zip_name)
    if not os.path.exists(plugin_config_dir):
        os.mkdir(plugin_config_dir)

    hook_func_config_dir = os.path.join(plugin_config_dir, hook_func)
    if not os.path.exists(hook_func_config_dir):
        os.mkdir(hook_func_config_dir)

    for hook_name in hook_names:
        config_path = os.path.join(hook_func_config_dir, f"fuzzer-config.{hook_name}.json")
        docker_path = os.path.join(hook_func_config_dir, f"docker-compose.{hook_name}.yml")
        with open(config_path, "w") as f:
            f.write(generate_fuzzer_config(hook_name, params))
        with open(docker_path, "w") as f:
            f.write(generate_docker_compose(plugin_zip_name, plugin_wppath_name, config_path))



def main():
    args = parse_args()
    plugin_zip_path = args.plugin
    plugin_zip_name = plugin_zip_path.split("/")[1]

    if not os.path.exists(plugin_zip_path):
        sys.exit("Plugin file was not found")

    inmemzip = read_zip_to_memory(plugin_zip_path)

    sio = io.StringIO()
    # try:
    extracted_priv_nopriv_actions_map = set()
    extracted_priv_nopriv_actions_functions = set()
    plugin_wppath_name = list(inmemzip.keys())[0].split("/")[0] # All plugins have a specific dir prefix, i.e. "fooplugin/", so any path is ok.

    for file in inmemzip.keys():
        action_map, action_func_list = extract_wp_priv_nopriv_actions(
            inmemzip[file])
        if not action_map or not action_func_list:
            continue
        extracted_priv_nopriv_actions_map.update(action_map)
        extracted_priv_nopriv_actions_functions.update(action_func_list)

    print("Found wp_ajax* hooks: ", len(extracted_priv_nopriv_actions_map))
    print("Found functions: ", len(extracted_priv_nopriv_actions_functions))

    extracted_functions = []
    extracted_param_names = {}
    for function in list(extracted_priv_nopriv_actions_functions):
        ex_func = extract_function_from_zip(inmemzip, function)
        if ex_func is None:
            continue
        function_code = ex_func[1]
        param_names = extract_param_names(function_code)
        extracted_functions.append(ex_func)

        # Make sure not to overwrite functions
        assert function not in extracted_param_names
        extracted_param_names[function] = param_names

    extracted_functions_map = {}
    for func in extracted_functions:
        extracted_functions_map[func[0]] = func[1]

    print("Extracted functions: ", len(extracted_functions_map))

    if len(extracted_functions_map) != len(extracted_priv_nopriv_actions_functions):
        print(f"Mismatch in extracted functions: f map: {len(extracted_functions_map)} vs ex funcs: {len(extracted_priv_nopriv_actions_functions)}")

    swap_map = {}
    for func_name in extracted_priv_nopriv_actions_functions:
        swap_map[func_name] = set()
        for func_map in extracted_priv_nopriv_actions_map:
            if func_map[1] == func_name:
                swap_map[func_name].add(func_map[0])

    total_param_names = sum(
        len(params)
        for params in extracted_param_names.values()
    )
    print("Extracted params: ", total_param_names)

    sio.write("<?php\n")
    sio.write(
        "/***\n"
        "*\n"
        f"*Found actions: {len(extracted_priv_nopriv_actions_map)}\n"
        f"*Found functions:{len(extracted_priv_nopriv_actions_functions)}\n"
        f"*Extracted functions:{len(extracted_functions_map)}\n"
        f"*Total parameter names extracted: {total_param_names}\n"
        f"*Overview: {swap_map}\n"
        "*\n"
        "***/\n\n"
    )
    for hook_func, hook_names in swap_map.items():
        sio.write(
            f"/** Function {hook_func}() called by wp_ajax hooks: {hook_names} **/\n"
        )
        if not hook_func in extracted_functions_map.keys():
            sio.write("/** No function found :-/ **/")
        elif not hook_func in extracted_param_names or not extracted_param_names[hook_func]:
            sio.write("/** No params detected :-/ **/")
        else:
            params = extracted_param_names[hook_func]
            sio.write(f"/** Parameters found in function {hook_func}(): {json.dumps(params)} **/\n")
            sio.write(str(extracted_functions_map[hook_func]))
            generate_config(plugin_zip_name,plugin_wppath_name,hook_func,hook_names,params)
        sio.write("\n\n\n")

    with open(args.plugin.replace("plugins/", "analysis/") + "-analysis.php", "w") as f:
        f.write(sio.getvalue())


if __name__ == "__main__":
    main()
