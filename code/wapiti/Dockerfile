FROM python:3@sha256:2e376990a11f1c1e03796d08db0e99c36eadb4bb6491372b227f1e53c3482914

COPY requirements.txt ./
RUN pip install -r requirements.txt

RUN groupadd -r wapiti && useradd -r -g wapiti wapiti
WORKDIR /home/wapiti/


COPY phuzz.py ./
COPY run.py ./
COPY init.sh ./

RUN chown -R wapiti:wapiti /home/wapiti

USER wapiti
ENTRYPOINT bash ./init.sh
