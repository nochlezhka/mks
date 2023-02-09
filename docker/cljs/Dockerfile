FROM node:14.14

WORKDIR /tmp
ENV CLOJURE_VER=1.10.1.716

RUN apt-get update \
  && apt-get -q -y install openjdk-8-jdk curl \
  && curl -s https://download.clojure.org/install/linux-install-$CLOJURE_VER.sh | bash

WORKDIR /app

COPY shadow-cljs.edn package.json package-lock.json ./
RUN npm install
RUN npx shadow-cljs

COPY src src
COPY public/index.html public/index.html
COPY public/css public/css

RUN npx shadow-cljs release :app

CMD ["sh"]
