# Deep Space Objects
Atlas for deep space objects (Symfony 4 / Elastic Search / Vue.js)

Installation
==
Clone project
 > git clone git@github.com:HamHamFonFon/deep-space-objects.git
 
Launch docker stack
 > docker-compose up -d
  
Docker stack based on [https://github.com/maxpou/docker-symfony](Maxpou/Docker-Symfony stack)

Add hosts into hosts file
 > sudo echo $(docker network inspect bridge | grep Gateway | grep -o -E '[0-9\.]+') "dso.dev" >> /etc/hosts

Install dependencies
 > cd project
 
 > composer install

Run on :

 > docker-compose up -d

Symfony app :
 - http://dso.local 

Import data in Elastic Search
==
WIP

Authors
==
 Stéphane MEAUDRE <balistik.fonfon@gmail.com>
 
Licences
==
