import os
import glob
import gzip
import json 

BURPDIR="./burpsuite/shared-tmpfs/coverage-reports/"
PHUZZDIR = "./phuzz/shared-tmpfs/coverage-reports/"
ZAPDIR = "./zap/shared-tmpfs/coverage-reports/"
WAPITIDIR = "./wapiti/shared-tmpfs/coverage-reports/"
WFUZZDIR = "./wfuzz/shared-tmpfs/coverage-reports/"


def cov2lines(c):
	linecov = {
	}
	for f in c.keys():
		if f == "__time__":
			continue
		linecov[f] = {}
		hit_line = list(filter(lambda y: y != None, map(lambda x: x if c[f][x] > 0 else None, c[f].keys())))
		linecov[f]["hit"] = "_".join(hit_line)
	return linecov

def analyze(dir, outfile):
	total_coverage = []
	datapoints = []
	first_timestamp = 0
	for i, f in enumerate(sorted(glob.glob(dir + "*.json"))):
		try:
			print("============")
			c = json.loads(gzip.open(f).read())

			fname = f.split("/")[-1].split("-")[0]
			covid = '-'.join(f.split("/")[-1].split("-")[1:6]).split(".")[0]
			#print(c.keys())

			if i == 0:
				first_timestamp = int(fname)

			if not any(map(lambda k: "/sqli/source/fuzz.php" in k, c.keys())):
				continue

			linecov = cov2lines(c)

			new_hits = 0
			for path in linecov:
				hit_path = path + "_" + linecov[path]['hit']

				if not hit_path in total_coverage:
					total_coverage.append(hit_path)
					new_hits += 1
				else:
					continue
			if new_hits == 0:
				continue
			datapoint = (int(fname), covid, new_hits, len(total_coverage))
			datapoints.append(datapoint)
			print(f, datapoint)
		except Exception as e:
			print(e)

	with open(outfile + ".cov", "w") as f:
		f.write("id,time_rel,timestamp,covid,new_paths,total_paths\n")
		time_rel = 0
		prev_time_rel = 0
		for i,dp in enumerate(datapoints):
			if i != 0:
				time_rel = datapoints[i][0] - first_timestamp
				if prev_time_rel == time_rel:
					time_rel += i/10
				else:
					prev_time_rel = time_rel
			f.write(str(i+1)+ "," + str(time_rel) + "," + ",".join(map(lambda x: str(x), dp)) + "\n")

analyze(BURPDIR, "burp")
analyze(PHUZZDIR, "phuzz")
analyze(ZAPDIR, "zap")
analyze(WAPITIDIR, "wapiti")
analyze(WFUZZDIR, "wfuzz")