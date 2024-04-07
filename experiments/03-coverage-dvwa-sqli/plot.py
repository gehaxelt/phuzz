

import pandas as pd
import seaborn as sns
import matplotlib.pyplot as plt
from matplotlib import rcParams

sns.set_context("paper")
sns.set_theme(style="whitegrid")

myparams={
"axes.labelsize": 8.8,
"axes.titlesize": 0,
"figure.figsize": [5, 2.8],
"grid.linewidth": 0.8,
"legend.fontsize": 9.0,
"lines.linewidth": 1.4,
"lines.markeredgewidth": 0.0,
"lines.markersize": 5.6,
"patch.linewidth": 0.24,
"xtick.labelsize": 8.0,
"xtick.major.pad": 0,
"xtick.major.width": 0.8,
"xtick.minor.width": 0.4,
"ytick.labelsize": 8.0,
"ytick.major.pad": 0,
"ytick.major.width": 0.8,
"ytick.minor.width": 0.4
}

new_params = rcParams | myparams
sns.set(rc=new_params)

burp = pd.read_csv("burp.cov")
phuzz = pd.read_csv("phuzz.cov")
zap = pd.read_csv("zap.cov")
wapiti = pd.read_csv("wapiti.cov")
wfuzz = pd.read_csv("wfuzz.cov")

new_dataset = [(0.0, 'Burp', 6, 6), (0.0, 'Phuzz', 6, 6), (0.0, 'ZAP', 6, 6), (0.0, 'Wapiti', 6, 6), (0.0, 'WFuzz', 6, 6), (0.1, 'Burp', 6, 6), (0.1, 'Phuzz', 6, 6), (0.1, 'ZAP', 6, 6), (0.1, 'Wapiti', 6, 6), (0.1, 'WFuzz', 1, 7), (1.0, 'Burp', 6, 6), (1.0, 'Phuzz', 6, 6), (1.0, 'ZAP', 6, 6), (1.0, 'Wapiti', 1, 7), (1.0, 'WFuzz', 1, 7), (3.0, 'Burp', 1, 7), (3.0, 'Phuzz', 6, 6), (3.0, 'ZAP', 6, 6), (3.0, 'Wapiti', 1, 7), (3.0, 'WFuzz', 1, 7), (4.0, 'Burp', 1, 7), (4.0, 'Phuzz', 1, 7), (4.0, 'ZAP', 6, 6), (4.0, 'Wapiti', 1, 7), (4.0, 'WFuzz', 1, 7), (5.0, 'Burp', 1, 7), (5.0, 'Phuzz', 1, 7), (5.0, 'ZAP', 6, 6), (5.0, 'Wapiti', 1, 7), (5.0, 'WFuzz', 1, 8), (5.3, 'Burp', 1, 7), (5.3, 'Phuzz', 1, 7), (5.3, 'ZAP', 6, 6), (5.3, 'Wapiti', 1, 7), (5.3, 'WFuzz', 1, 9), (5.4, 'Burp', 1, 7), (5.4, 'Phuzz', 1, 7), (5.4, 'ZAP', 6, 6), (5.4, 'Wapiti', 1, 7), (5.4, 'WFuzz', 1, 10), (5.5, 'Burp', 1, 7), (5.5, 'Phuzz', 1, 7), (5.5, 'ZAP', 6, 6), (5.5, 'Wapiti', 1, 7), (5.5, 'WFuzz', 1, 11), (5.6, 'Burp', 1, 7), (5.6, 'Phuzz', 1, 7), (5.6, 'ZAP', 6, 6), (5.6, 'Wapiti', 1, 7), (5.6, 'WFuzz', 3, 14), (5.7, 'Burp', 1, 7), (5.7, 'Phuzz', 1, 7), (5.7, 'ZAP', 6, 6), (5.7, 'Wapiti', 1, 7), (5.7, 'WFuzz', 1, 15), (7.0, 'Burp', 1, 7), (7.0, 'Phuzz', 1, 7), (7.0, 'ZAP', 6, 6), (7.0, 'Wapiti', 1, 8), (7.0, 'WFuzz', 1, 15), (33.0, 'Burp', 1, 7), (33.0, 'Phuzz', 1, 8), (33.0, 'ZAP', 6, 6), (33.0, 'Wapiti', 1, 8), (33.0, 'WFuzz', 1, 15), (61.0, 'Burp', 1, 7), (61.0, 'Phuzz', 1, 8), (61.0, 'ZAP', 1, 7), (61.0, 'Wapiti', 1, 8), (61.0, 'WFuzz', 1, 15), (63.0, 'Burp', 1, 7), (63.0, 'Phuzz', 1, 8), (63.0, 'ZAP', 1, 8), (63.0, 'Wapiti', 1, 8), (63.0, 'WFuzz', 1, 15), (350.0, 'Burp', 1, 7), (350.0, 'Phuzz', 1, 9), (350.0, 'ZAP', 1, 8), (350.0, 'Wapiti', 1, 8), (350.0, 'WFuzz', 1, 15), (791.0, 'Burp', 1, 7), (791.0, 'Phuzz', 1, 10), (791.0, 'ZAP', 1, 8), (791.0, 'Wapiti', 1, 8), (791.0, 'WFuzz', 1, 15), (15322.0, 'Burp', 1, 7), (15322.0, 'Phuzz', 1, 11), (15322.0, 'ZAP', 1, 8), (15322.0, 'Wapiti', 1, 8), (15322.0, 'WFuzz', 1, 15), (15328.0, 'Burp', 1, 7), (15328.0, 'Phuzz', 1, 12), (15328.0, 'ZAP', 1, 8), (15328.0, 'Wapiti', 1, 8), (15328.0, 'WFuzz', 1, 15), (15331.0, 'Burp', 1, 7), (15331.0, 'Phuzz', 3, 15), (15331.0, 'ZAP', 1, 8), (15331.0, 'Wapiti', 1, 8), (15331.0, 'WFuzz', 1, 15)]
burp_row_last = None
phuzz_row_last = None
zap_row_last = None
wapiti_row_last = None
wfuzz_row_last = None

if not new_dataset:
    for i in range(0,15332 * 10,1):
        if i % 1000 == 0:
            print(i/10)

        burp_row = burp.loc[burp['time_rel'] == i/10]
        phuzz_row = phuzz.loc[phuzz['time_rel'] == i/10]
        zap_row = zap.loc[zap['time_rel'] == i/10]
        wapiti_row = wapiti.loc[wapiti['time_rel'] == i/10]
        wfuzz_row = wfuzz.loc[wfuzz['time_rel'] == i/10]

        if not burp_row.empty:
            burp_row_last = burp_row

        if not phuzz_row.empty:
            phuzz_row_last = phuzz_row

        if not zap_row.empty:
            zap_row_last = zap_row

        if not wapiti_row.empty:
            wapiti_row_last = wapiti_row

        if not wfuzz_row.empty:
            wfuzz_row_last = wfuzz_row

        if burp_row.empty and phuzz_row.empty and zap_row.empty and wapiti_row.empty and wfuzz_row.empty:
            continue

        row = (
            i/10,
            "Burp",
            int(burp_row_last["new_paths"].iloc[0]) if burp_row_last is not None else 0,
            int(burp_row_last["total_paths"].iloc[0]) if burp_row_last is not None else 0,
            )
        new_dataset.append(row)

        row = (
            i/10,
            "Phuzz",
            int(phuzz_row_last["new_paths"].iloc[0]) if phuzz_row_last is not None else 0,
            int(phuzz_row_last["total_paths"].iloc[0]) if phuzz_row_last is not None else 0,
            )
        new_dataset.append(row)

        row = (
            i/10,
            "ZAP",
            int(zap_row_last["new_paths"].iloc[0]) if zap_row_last is not None else 0,
            int(zap_row_last["total_paths"].iloc[0]) if zap_row_last is not None else 0,
            )
        new_dataset.append(row)

        row = (
            i/10,
            "Wapiti",
            int(wapiti_row_last["new_paths"].iloc[0]) if wapiti_row_last is not None else 0,
            int(wapiti_row_last["total_paths"].iloc[0]) if wapiti_row_last is not None else 0,
            )
        new_dataset.append(row)

        row = (
            i/10,
            "WFuzz",
            int(wfuzz_row_last["new_paths"].iloc[0]) if wfuzz_row_last is not None else 0,
            int(wfuzz_row_last["total_paths"].iloc[0]) if wfuzz_row_last is not None else 0,
            )
        new_dataset.append(row)

print(new_dataset)

data = pd.DataFrame(new_dataset, columns=['time','Fuzzer', 'new_paths', 'total_paths'])

print(data)
plot = sns.lineplot(x="time", y="total_paths", hue="Fuzzer",
             data=data, linestyle="--", marker="o")


plot.set(xlabel='log(Time) in s', ylabel='Total # of unique paths')
plt.xscale("symlog")
plt.savefig("sqli_fuzz_cov.pdf", bbox_inches="tight", pad_inches=0.05)
plt.show()