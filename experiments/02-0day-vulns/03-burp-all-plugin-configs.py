import os
import re
import shutil
import subprocess
import glob
import sys
import time
import requests


def find_leaf_directories(path):
    for root, dirs, _ in os.walk(path):
        if not dirs:
            yield root

def run_docker_compose_in_directory(leaf_dir):
    compose_file = next(
        (
            file
            for file in os.listdir(leaf_dir)
            if file.endswith(".yml")
        ),
        None,
    )

    if compose_file and os.path.exists(os.path.join(leaf_dir, compose_file)):
        subprocess.run(
            [
                "docker compose", "-f",
                os.path.join(leaf_dir, compose_file),
                "up",
            ]
        )

def run_docker_command(cmd, working_dir, docker_compose_file):
    cur_dir = os.getcwd()

    os.chdir(working_dir)
    try:
        the_cmd = ["docker","compose", "-f", docker_compose_file] + cmd.split(" ")
        print("Running: ", the_cmd)
        subprocess.run(the_cmd)
    except Exception as e:
        print(e)

    os.chdir(cur_dir)



def main():
    DIRS = {
        "configs": "./configs/",
        "plugins": "./plugins/",
        "code": "../../code/",
        "wp_plugin_dir": "../../code/web/applications/wordpress/_plugins/",
        "fuzzer_configs": "../../code/fuzzer/configs/wordpress/",
        "fuzzer_output": "../../code/fuzzer/output/"
    }
    N_FUZZERS=list(range(1,10+1)) #+1 to include the upper bound
    FUZZ_TIME=180

    leaf_dirs = list(find_leaf_directories(DIRS["configs"]))

    copied_plugins = set()

    for leaf_dir in leaf_dirs:
        print("Leaf dir is: ", leaf_dir)
        docker_composes = glob.glob(f"docker-compose*.yml", root_dir=leaf_dir)
        fuzzer_configs = glob.glob(f"fuzzer-config*.json", root_dir=leaf_dir)

        fuzzed_functions = list(map(lambda s: s.replace("docker-compose.","").replace(".yml",""), docker_composes))

        assert len(docker_composes) == len(fuzzer_configs)

        for fuzzed_function in fuzzed_functions:
            print("Fuzzed function is: ", fuzzed_function)
            docker_compose_name = f"docker-compose.{fuzzed_function}.yml"
            fuzzer_config_name = f"fuzzer-config.{fuzzed_function}.json"

            assert docker_compose_name in docker_composes
            assert fuzzer_config_name in fuzzer_configs


            src_docker_compose_path = os.path.join(leaf_dir, docker_compose_name)
            src_fuzzer_config_path = os.path.join(leaf_dir, fuzzer_config_name)
            assert os.path.isfile(src_docker_compose_path)
            assert os.path.isfile(src_fuzzer_config_path)

            plugin_file_name = leaf_dir.replace(DIRS['configs'],"").split("/")[0]
            src_plugin_file_path = os.path.join(DIRS['plugins'], plugin_file_name)
            assert os.path.isfile(src_plugin_file_path)

            dst_docker_compose_path = os.path.join(DIRS['code'], docker_compose_name)
            dst_fuzzer_config_path = os.path.join(DIRS['fuzzer_configs'], fuzzer_config_name)
            dst_plugin_file_path = os.path.join(DIRS['wp_plugin_dir'], plugin_file_name)

            print("Copying files...")
            shutil.copy(src_docker_compose_path, dst_docker_compose_path)
            shutil.copy(src_fuzzer_config_path, dst_fuzzer_config_path)
            shutil.copy(src_plugin_file_path, dst_plugin_file_path)

            for f in glob.glob("*.json", root_dir=DIRS['fuzzer_output']):
                os.remove(os.path.join(DIRS['fuzzer_output'],f))

            #print("Generating fuzzer names...")
            #fuzzer_base_name = '-'.join(plugin_file_name.replace(".zip","").split("-")[1:]).split(".")[0]
            #fuzzer_names = list(map(lambda i: f"fuzzer-{i}",N_FUZZERS))
            #with open(src_docker_compose_path) as f:
            #    data = f.read()
            #    for fuzzer_name in fuzzer_names:
            #        assert fuzzer_name in data

            the_containers = ['web', 'burpsuite']
            # Kill all container
            run_docker_command(f"kill {' '.join(the_containers)}", os.path.abspath(DIRS['code']), docker_compose_name)
            #run_docker_command("up db -d", os.path.abspath(DIRS['code']), docker_compose_name)
            #time.sleep(5) # Wait for containers to stop
            #run_docker_command("build web", os.path.abspath(DIRS['code']), docker_compose_name)
            run_docker_command("up -d --force-recreate web", os.path.abspath(DIRS['code']), docker_compose_name)
            while True:
                print("Waiting for web server...")
                try:
                    r = requests.get("http://localhost:8181")
                    if r.status_code != 200:
                        raise Exception("Wrong status code")
                except Exception as e:
                    print(e)
                    time.sleep(1)
                    continue
                break
            time.sleep(5) # Give the Webserver time to come up
            #run_docker_command(f"build {' '.join(fuzzer_names)}", os.path.abspath(DIRS['code']), docker_compose_name)
            run_docker_command(f"up -d --force-recreate burpsuite", os.path.abspath(DIRS['code']), docker_compose_name) # should terminate after t=180s
            time.sleep(FUZZ_TIME) # Run fuzzers for 180s
            time.sleep(60) # Give some time for startup and tear-down
            run_docker_command(f"kill {' '.join(the_containers)}", os.path.abspath(DIRS['code']), docker_compose_name)
            
            #print("Copy result files")
            ##for f in glob.glob("*.json", root_dir=DIRS['fuzzer_output']):
            #src_dir = DIRS['fuzzer_output']
            #dest_dir = os.path.join(leaf_dir, "output")
            #if not os.path.exists(dest_dir):
            #    os.mkdir(dest_dir)
            #shutil.copytree(src_dir, dest_dir, dirs_exist_ok=True)
            
            print("Removing files...")
            os.remove(dst_docker_compose_path)
            os.remove(dst_fuzzer_config_path)
            os.remove(dst_plugin_file_path)

            assert not os.path.exists(dst_docker_compose_path)
            assert not os.path.exists(dst_fuzzer_config_path)
            assert not os.path.exists(dst_plugin_file_path)

            print("Finished. Waiting a bit")
            time.sleep(5)


if __name__ == "__main__":
    main()
