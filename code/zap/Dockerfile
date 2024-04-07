FROM docker.io/softwaresecurityproject/zap-stable:latest@sha256:aec6c9f65d69570aadec0d15ad0d3a24ffd2c7de5a262436c34e5009c5aa2e66
USER root
RUN apt-get update -y && apt-get install python3 python3-pip

USER zap
WORKDIR /zap

RUN pip install requests

COPY phuzz.py ./
COPY run.py ./
COPY init.sh ./

RUN mkdir -p /tmp/zap/wrk
RUN ln -s /tmp/zap/wrk /zap/wrk

ENTRYPOINT bash ./init.sh
