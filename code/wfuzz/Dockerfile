FROM python:3@sha256:2e376990a11f1c1e03796d08db0e99c36eadb4bb6491372b227f1e53c3482914

#RUN pip install wfuzz requests
COPY requirements.txt ./
RUN pip3 install -r requirements.txt

RUN groupadd -r wfuzz && useradd -r -g wfuzz wfuzz
WORKDIR /home/wfuzz/


COPY phuzz.py ./
COPY run.py ./
COPY init.sh ./
COPY wordlist.txt ./

RUN chown -R wfuzz:wfuzz /home/wfuzz

USER wfuzz
ENTRYPOINT bash ./init.sh
