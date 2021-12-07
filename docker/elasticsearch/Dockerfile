FROM docker.elastic.co/elasticsearch/elasticsearch:6.8.5

RUN elasticsearch-plugin install analysis-icu
RUN elasticsearch-plugin install analysis-phonetic

EXPOSE 9200
