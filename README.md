# Deep Space Objects
Atlas for deep space objects (Symfony 4 / Elastic Search / Vue.js)

Installation
==
### Clone project
`git clone git@github.com:HamHamFonFon/deep-space-objects.git` 
 
### Generate SSL certificate for HTTPS
```
openssl req -new -newkey rsa:2048 -nodes -out deepskyobjects_local.csr -keyout deepskyobjects_local.key -subj "/C=FR/ST=/L=Montpellier/O=/OU=Montpellier/CN=deepskyobjects.local"
``` 
Copy path of deepskyobjects_local.csr and deepskyobjects_local.key in .env file.

### Init .env files
```
 cp .env.local.dist .env.local
 cp .env.dist .env
``` 
 
### Launch docker stack
 ```
 docker-compose build
 ```

### Add hosts into hosts file
 `sudo echo $(docker network inspect bridge | grep Gateway | grep -o -E '[0-9\.]+') "deepskyobjects.local" >> /etc/hosts`


### Start Docker stack on :

```
docker-compose up -d
docker exec -ti dso_php bash
```

### Install dependencies

```
cd deep-space-objects
composer install
npm install
``` 

Symfony app :
 - http://symfony.local


Elastic Search
==

Because of [removal of mappings type in ES 6.X](https://www.elastic.co/guide/en/elasticsearch/reference/6.5/removal-of-types.html), we create two indexes, each got his own mapping. 

### Create index with mappings and import data
```
curl -X DELETE "elasticsearch:9200/deepspaceobjects"
curl -X DELETE "elasticsearch:9200/constellations"
curl -X PUT elasticsearch:9200/deepspaceobjects?pretty=true -H 'Content-Type: application/json' -d @config/elasticsearch/mappings/deepspaceobjects.mapping.json
curl -X PUT elasticsearch:9200/constellations?pretty=true -H 'Content-Type: application/json' -d @config/elasticsearch/mappings/constellations.mapping.json
```

### Create bulk from source
```
php bin/console dso:convert-bulk <type>
```
List of values for <type> : dso20, constellations

### Bulk import Data
```
curl -X POST elasticsearch:9200/_bulk?pretty=true -H 'Content-Type: application/json' --data-binary @config/elasticsearch/bulk/<type>.bulk.json
```

### Status data
- Messier : 110/110
- NGC : 7239/7840
- UGC : 261/12921
- PGC : 18/? 
- Sharpless : 99/313
- RCW : 27/182
- Index Catalog : 204/5386
- Collinder : 78/471
- Caldwell : 30/109
- Abell: 103/
- ldn: 18/?
- lbn: 29/?
- Dolidze-Dzimselejvili: 11/11

Authors
==
 Stéphane MEAUDRE <balistik.fonfon@gmail.com>

Sources
=======
Docker stack based on stacks by :
> https://github.com/maxpou/docker-symfony
> https://framagit.org/3rr0r/docker-sf4
> https://github.com/neiluJ/api-vue-boilerplate

Licences
==
