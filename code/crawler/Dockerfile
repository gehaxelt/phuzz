FROM mcr.microsoft.com/playwright:v1.38.0-jammy@sha256:9f1ebfec94143da0084f4b8113980fafa07be4fe89181f618671b12f193334f3

WORKDIR /app

RUN apt-get update && apt-get install -y python3-pip && rm -rf /var/lib/apt/lists/*

COPY requirements.txt ./
RUN pip3 install --no-cache-dir -r requirements.txt

COPY . .

ENTRYPOINT ["bash", "/app/crawler.sh"]
