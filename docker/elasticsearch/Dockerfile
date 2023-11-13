FROM docker.elastic.co/elasticsearch/elasticsearch:7.17.14

RUN elasticsearch-plugin install analysis-icu
RUN elasticsearch-plugin install analysis-phonetic

EXPOSE 9200
