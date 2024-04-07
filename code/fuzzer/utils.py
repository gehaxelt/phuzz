import os
import random
import json
import gzip

def fuzz_open(path, mode="r"):
    if os.environ["FUZZER_COMPRESS"] == "1":
        return gzip.open(path, mode + "t")
    else:
        return open(path, mode)

def string_is_number(string):
    # checks if string is a number
    try:
        int(string)
        return True
    except ValueError:
        return False


def sort_by_sublist_length(list_of_lists):
    return sorted(list_of_lists, key=lambda x: (len(x), x[0]["name"], x[0]['value']), reverse=True)


def strip_quotes(strings):
    return [string.strip('"\'') for string in strings]


def get_file_path(file_name):
    return f"{os.path.dirname(os.path.realpath(__file__))}{file_name}"


def get_path_growth(paths_previous, paths_current):
    return len(paths_current) - len(paths_previous)


def coverage_report_has_functions(coverage_report_for_file):
    return "functions" in coverage_report_for_file and type(coverage_report_for_file["functions"]) == dict


def coverage_report_has_lines(coverage_report_for_file):
    return "lines" in coverage_report_for_file and type(coverage_report_for_file["lines"]) == dict


def get_executed_lines(coverage_report, file_name):
    for x in coverage_report[file_name]["lines"].keys():
        if coverage_report[file_name]["lines"][x] > 0:
            yield x

def stringify_hit_or_line(file, path):
    try:
        return f'{file}::::{"_".join([str(x) for x in path["path"]])}'
    except:
        return f'{file}::::{"_".join([str(x) for x in path["lines"]])}'


def stringify_hit_paths(hit_paths):
    return [
        stringify_hit_or_line(file, hit)
        for path in hit_paths
        for file in path
        for hit in path[file]
    ]

def lines_count_dict(hit_paths):
    d = {}
    for path in hit_paths:
        for file in path:
            for hit in path[file]:
                for hp in hit['path']:
                    key = f"{file}:{hp}"
                    if not key in d:
                        d[key] = 1
                    else:
                        d[key] += 1
    return d


def add_paths(paths, new_paths):
    return paths + [p for p in new_paths if p not in paths]


def get_executed_paths(coverage_report, file_name, function):
    for x in coverage_report[file_name]["functions"][function]["paths"]:
        if x["hit"] > 0:
            yield x


def extract_hit_paths(coverage_report):
    hit_paths = []
    for file in coverage_report.keys():
        if "__fuzzer__" in file:
            continue
        if file == "__time__":
            continue

        # XDEBUG coverage
        if coverage_report_has_functions(coverage_report[file]):
            for function in coverage_report[file]["functions"]:
                paths = list(get_executed_paths(coverage_report, file, function))
                hit_paths.append({file: paths})
        elif coverage_report_has_lines(coverage_report[file]):
            lines = list(get_executed_lines(coverage_report, file))
            paths = [{"lines": [int(x) for x in lines], "hit": 1}]
            hit_paths.append({file: paths})

        # PCOV coverage
        else:
            #       x = (line_no, hit_info) -> (49, -1|1) -> We only want hit lines with 1
            lines = sorted(map(lambda y: y[0], filter(lambda x: x[1] > 0, coverage_report[file].items())))
            paths = [{"lines": [int(x) for x in lines], "hit":1 }]
            hit_paths.append({file: paths})

    return hit_paths


def sort_by_length(list_of_dicts):
    return sorted(list_of_dicts, key=lambda x: len(x))

def read_har_file(file_path):
    with fuzz_open(file_path, "r") as f:
        return json.load(f)


def parse_requests(data):
    requests = data["log"]["entries"]
    request_info = []
    for request in requests:
        method = request['request']['method']
        cookies = request["request"]["cookies"]
        query_string = request["request"]["queryString"]
        headers = request["request"]["headers"]
        try:
            payload = request["request"]["postData"]["text"]
        except KeyError:
            payload = []
        try:
            form_data = request["request"]["postData"]["params"]
        except KeyError:
            form_data = []
        info = {
            "url": request["request"]["url"],
            'method': method,
            "cookies": cookies,
            "query_string": query_string,
            "headers": headers,
            "payload": payload,
            "form_data": form_data,
        }
        request_info.append(info)
    return request_info


def filter_requests_by_domain(requests, domain):
    return [
        request
        for request in requests
        if domain in request["url"]
    ]


def extract_input_vectors_from_har(file_path, domain=None):
    data = read_har_file(file_path)
    requests = parse_requests(data)
    if domain:
        return filter_requests_by_domain(requests, domain)
    else:
        return requests
