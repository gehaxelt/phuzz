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
from collections import Counter

codecs.register_error("strict", codecs.ignore_errors)

def read_zip_to_memory(zip_path):
    # Taken from https://stackoverflow.com/questions/10908877/extracting-a-zipfile-to-memory/10909016#10909016
    input_zip = ZipFile(zip_path)
    return {name: input_zip.read(name).decode('utf-8') for name in input_zip.namelist()}

def main():
    import glob
    counters = {
        'str_contains': set(),
        'str_starts_with': set(),
        'str_ends_with': set(),
        'fdiv': set(),
        'get_resource_id': set(),
        'get_debug_type': set(),
        'preg_last_error_msg': set()
    }
    for plugin_zip_path in glob.glob("plugins/*.zip"):
        plugin_zip_name = plugin_zip_path.split("/")[1]

        if not os.path.exists(plugin_zip_path):
            sys.exit("Plugin file was not found")

        inmemzip = read_zip_to_memory(plugin_zip_path)

        plugin_wppath_name = list(inmemzip.keys())[0].split("/")[0] 

        for file in inmemzip.keys():
            # inmemzip[file]
            for func in counters.keys():
                if func in inmemzip[file]:
                    counters[func].update([plugin_zip_name])

    print(counters)
    total = set()
    for counter in counters:
        print(counter, len(counters[counter]))
        total |= counters[counter]
    print(total, len(total))

if __name__ == "__main__":
    main()
