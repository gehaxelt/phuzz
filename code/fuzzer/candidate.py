import hashlib
import json
import os
import time
from pathlib import Path
from uuid import uuid4
from utils import fuzz_open


class Candidate:
    def __init__(self,  parent=None, score=0, priority=0, http_target="", http_method="GET", fixed_params={}, fuzz_params={}, fuzz_weights={}, fuzzer_id=-1, is_initial_candidate=False, mutated_param_type=None, mutated_param_name=None):
        self.coverage_id = str(int(time.time())) + "-" + str(uuid4())
        self.parent = parent
        self.score = score
        self.priority = priority
        self.http_target = http_target
        self.http_method = http_method
        self.fixed_params = {
            'headers': fixed_params.get('headers',{}),
            'cookies': fixed_params.get('cookies',{}),
            'query_params': fixed_params.get('query_params',{}),
            'body_params': fixed_params.get('body_params',{}),
        }
        self.fuzz_params = {
            'headers': fuzz_params.get('headers',{}),
            'cookies': fuzz_params.get('cookies',{}),
            'query_params': fuzz_params.get('query_params',{}),
            'body_params': fuzz_params.get('body_params',{}),
        }
        self.fuzz_weights = {
            'headers': fuzz_weights.get('headers',1.0),
            'cookies': fuzz_weights.get('cookies',1.0),
            'query_params': fuzz_weights.get('query_params',1.0),
            'body_params': fuzz_weights.get('body_params',1.0),
        }
        self.response = None
        self.vulns = []
        self.errors = None
        self.exceptions = None
        self.new_paths = {}
        self.paths = {}
        self.number_of_new_paths = 0
        self.hit_lines = {}
        self.unhit_lines = {}
        self.total_hit = 0
        self.total_unhit = 0
        self.hit_count = 0
        self.new_lines = 0
        self.is_interesting = False
        self.fuzzer_id = fuzzer_id
        self.is_initial_candidate = is_initial_candidate
        self.mutated_param_type=mutated_param_type
        self.mutated_param_name=mutated_param_name
        
        self.hash = None

    def __dict__(self):
        return {
            'coverage_id': self.coverage_id,
            'priority': self.priority,
            'http_target': self.http_target,
            'http_method': self.http_method,
            'fixed_params': self.fixed_params,
            'fuzz_params': self.fuzz_params,
            'fuzz_weights': self.fuzz_weights,
            'errors': self.errors,
            'exceptions': self.exceptions,
            'response_body': self.response.text if self.response else "",
            'response.body.length': len(self.response.text) if self.response else 0,
            'response.headers': dict(self.response.headers) if self.response else {},
            'response.status.code': self.response.status_code if self.response else 0,
            'response.time': self.response.elapsed.microseconds/1000 if self.response else 0,
            'number_of_new_paths': self.number_of_new_paths,
            'total_hit': self.total_hit,
            'total_unhit': self.total_unhit,
            'hit_count': self.hit_count,
            'new_lines': self.new_lines,
            'parent': self.parent.coverage_id if self.parent else "",
            'paths': self.paths,
            'new_paths': list(self.new_paths),
            'vulns': self.vulns,
            'score': self.score,
            'is_interesting': self.is_interesting,
            'fuzzer_id': self.fuzzer_id,
            'is_initial_candidate': self.is_initial_candidate,
            'mutated_param_type': self.mutated_param_type,
            'mutated_param_name': self.mutated_param_name
        }

    def __str__(self):
        return json.dumps(self.__dict__(), indent=2)

    def __lt__(self, other):
        return self.priority < other.priority

    def __le__(self, other):
        return self.priority <= other.priority

    def __gt__(self, other):
        return self.priority > other.priority

    def __ge__(self, other):
        return self.priority >= other.priority

    def __eq__(self, other):
        return self.priority == other.priority

    def __ne__(self, other):
        return self.priority != other.priority

    def get_paths_hash(self):
        if self.paths:
            s = '__'.join(sorted(self.paths))
            return hashlib.md5(s.encode()).hexdigest()
        return 'None'

    def get_params_hash(self):
        
        s = f"{self.http_target}:{self.http_method}"
        
        for k in self.fixed_params.keys():
            if k in ['cookies']: # May contain session values, so do not use it for the hash
                continue 
            s += '__'.join(['{}:{}'.format(*kv)
                for kv in sorted(self.fixed_params[k].items())])
        for k in self.fuzz_params.keys():
            s += '__'.join(['{}:{}'.format(*kv)
                for kv in sorted(self.fuzz_params[k].items())])
        self.hash = hashlib.md5(s.encode()).hexdigest()
        return self.hash

    def get_sync_file(self, is_interesting=False, candidate_hash=None):
        if candidate_hash:
            h = candidate_hash
        else:
            h = self.get_params_hash()

        if is_interesting:
            return os.path.join("/sync-tmpfs", "interesting_" + h + ".json")
        else:
            return os.path.join("/sync-tmpfs", h + ".json")


    def touch_sync_file(self, is_interesting=False):
        #https://stackoverflow.com/questions/1158076/implement-touch-using-python/34603829#34603829
        Path(self.get_sync_file(is_interesting=is_interesting)).touch(exist_ok=True)

    def write_sync_file(self):
        with fuzz_open(self.get_sync_file(), 'w') as f:
            f.write(str(self))
    
        if self.is_interesting:
            self.touch_sync_file(is_interesting=True)

    def load_sync_file(self, candidate_hash=None):
        with fuzz_open(self.get_sync_file(candidate_hash=candidate_hash)) as f:
            d = json.load(f)

        for k in d.keys():
            setattr(self, k, d[k])
        self.parent = None
        

    def print_candidate_info(self):
        print(str(self))
