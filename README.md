### Clone the project from github

` git clone https://github.com/iamsuzon/news-aggregator.git `

### Then rename the following file:

Open the project directory and rename
` .env.example ` into ` .env `

### Open terminal in the project root directory and run the following commands:

` docker compose build `

Then run the following command to run the container:

` docker compose up -d `

### Now run the following command to install the composer dependencies:

1. ` docker exec -it news-aggregator-web-1 composer config process-timeout 1200 `
2. ` docker exec -it news-aggregator-web-1 composer update `

### Now run the following command to generate the application key:

1. ` docker exec -it news-aggregator-web-1 php artisan migrate `
2. ` docker exec -it news-aggregator-web-1 php artisan db:seed `


### Here is the API documentation link:

[https://app.swaggerhub.com/apis/MISUJON01/news-aggregator/1.0.0#/](https://app.swaggerhub.com/apis/MISUJON01/news-aggregator/1.0.0#/)
