FROM ubuntu:latest@sha256:77906da86b60585ce12215807090eb327e7386c8fafb5402369e421f44eff17e

RUN apt-get -y update --fix-missing && apt-get -y upgrade
RUN apt-get -y install jython wget openssl ca-certificates openjdk-17-jdk --no-install-recommends

RUN groupadd -r burp && useradd -r -g burp burp
WORKDIR /home/burp/

RUN apt-get -y install zip

#RUN wget https://bootstrap.pypa.io/pip/2.7/get-pip.py
#RUN mkdir -p /usr/share/java/Lib/site-packages
#RUN wget https://files.pythonhosted.org/packages/b0/f3/44da7482ac6da3f36f68e253cb04de37365b3dba9036a3c70773b778b485/setuptools-44.0.0.zip
#RUN unzip setuptools-*.zip
#RUN wget http://peak.telecommunity.com/dist/ez_setup.py
#RUN mkdir -p /etc/pki/tls/certs
#RUN wget http://curl.haxx.se/ca/cacert.pem -O /etc/pki/tls/certs/ca-bundle.crt
#RUN java -jar /usr/share/java/jython.jar ez_setup.py
#RUN wget https://files.pythonhosted.org/packages/60/f3/26ff3767f099b73e0efa138a9998da67890793bfa475d8278f84a30fec77/requests-2.27.1.tar.gz
#RUN tar xfv requests-2*.tar.gz
#RUN cd requests-*/ && java -jar /usr/share/java/jython.jar setup.py install
#COPY certifi_core.py ./

# The above did not work :-/ Just copy the existing files from a working installation...
COPY ./Lib.zip /usr/share/java/
RUN cd /usr/share/java && unzip Lib.zip

COPY phuzz.py ./
COPY run.sh ./
COPY init.sh ./
COPY user.json ./
COPY carbonator.py ./
COPY burpsuite-pro.jar ./

RUN chown -R burp:burp ./
RUN chmod +x *.sh

USER burp

ENTRYPOINT bash ./init.sh
