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
    $ docker-compose exec php bin/console doctrine:migration:migrate --no-interaction
    $ docker-compose exec php bin/console messenger:consume async -vv
    
Test
---
    $ docker-compose exec php bin/console doctrine:schema:update --env test
    $ docker-compose exec php bin/run_test
    
Api
---
    curl -X POST \
      http://127.0.0.1:8888/v1/bet \
      -H 'Content-Type: application/json' \
      -H 'Host: 127.0.0.1:8888' \
      -d '
    {
    	"player_id": 1,
    	"stake_amount": "6",
    	"selections": [
    		{
    			"id": 1,
    			"odds": "1.003"
    		}
    	]
    }'
