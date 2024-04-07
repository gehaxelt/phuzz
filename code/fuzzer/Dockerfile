FROM python:3@sha256:2e376990a11f1c1e03796d08db0e99c36eadb4bb6491372b227f1e53c3482914

WORKDIR /app/

COPY requirements.txt /tmp/
RUN pip install --no-cache-dir -r /tmp/requirements.txt
RUN rm /sync-tmpfs/vuln_found || true
ENTRYPOINT python /app/fuzzer.py
