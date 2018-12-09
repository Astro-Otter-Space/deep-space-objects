# Deep Space Objects
Atlas for deep space objects (Symfony 4 / Elastic Search / Vue.js)

Installation
==
### Clone project
 > git clone git@github.com:HamHamFonFon/deep-space-objects.git
 
### Launch docker stack
 > docker-compose up -d

### Add hosts into hosts file
 > sudo echo $(docker network inspect bridge | grep Gateway | grep -o -E '[0-9\.]+') "symfony.local" >> /etc/hosts

### Install dependencies
 > cd project
 
 > composer install

 > npm install

### Run on :

 > docker-compose up -d

Symfony app :
 - http://symfony.local

Use
==

### Install symfony components
 > composer require <components>

### Use nodeJs and NPM
 > WIP


Elastic Search
==
### Create index with mappings and import data
```
curl -X DELETE "elasticsearch:9200/constellations"
curl -X DELETE "elasticsearch:9200/deepspaceobjects"
curl -X PUT elasticsearch:9200/constellations?pretty=true -H 'Content-Type: application/json' -d @config/elasticsearch/mappings/constellations.mapping.json
curl -X PUT elasticsearch:9200/deepspaceobjects?pretty=true -H 'Content-Type: application/json' -d @config/elasticsearch/mappings/dso.mapping.json
```

### Bulk import Data
```
curl -X POST elasticsearch:9200/_bulk?pretty=true -H 'Content-Type: application/json' --data-binary @config/elasticsearch/data/constellations.bulk.json
```

Authors
==
 Stéphane MEAUDRE <balistik.fonfon@gmail.com>

Sources
=======
Docker stack based on stack by :
https://github.com/maxpou/docker-symfony
https://framagit.org/3rr0r/docker-sf4


Licences
==
