Test Task
===

Installation
---
    # clone the repository
    $ git clone https://github.com/aliaksei-kavaliou/bet_test.git
    $ cd bet_test
    
Run
---
    $ docker-compose build && docker-compose up -d
    $ docker-compose exec php composer install
    $ docker-compose exec php bin/console migration:migrate
    
Test
---
    $ docker-compose exec php bin/console doctrine:schema:update --env test
    $ docker-compose exec php bin/run_test
    
Api
---
    curl -X POST \
      http://127.0.0.1:8888/v1/bet \
      -H 'Accept: */*' \
      -H 'Accept-Encoding: gzip, deflate' \
      -H 'Cache-Control: no-cache' \
      -H 'Connection: keep-alive' \
      -H 'Content-Length: 100' \
      -H 'Content-Type: application/json' \
      -H 'Host: 127.0.0.1:8888' \
      -H 'cache-control: no-cache' \
      -d '
    {
    	"player_id": 1,
    	"stake_amount": 100,
    	"selections": [
    		{
    			"id": 1,
    			"odds": 1.601
    		}
    	]
    }'