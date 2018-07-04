Banner Rotator
==============

[![Docker Build Status](https://img.shields.io/docker/build/wolnosciowiec/banner-rotator.svg)](https://hub.docker.com/r/wolnosciowiec/banner-rotator)
[![MicroBadger Layers](https://img.shields.io/microbadger/layers/wolnosciowiec/banner-rotator.svg)](https://github.com/Wolnosciowiec/banner-rotator)


Banner rotating service. Outputs a list of banners in HTML and JSON formats, has an API documentation.

###### Made for the Anarchist movement

#### Quick start

1. Install required: PHP 7.2, GNU Make, php-sqlite
2. Clone the repository
3. Do the setup: `make build setup_database_first_time`
4. Start the application: `make start_dev`

#### Endpoints documentation

Use `/public/doc` endpoint to access the documentation.
Also, there is an exported list of routes for Postman in `postman.json`
 
#### Docker

There exists a Docker image named `wolnosciowiec/news-feed-provider` at the official Docker registry.
To manually build an image use `make build@x86_64`

Please take a look at the `ENV` section of a [Dockerfile](./Dockerfile.x86_64) to see the list of possible environment variables to pass
to configure your application.

```
sudo docker run -e DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db" -e SITE_TITLE="List of grassroot, libertarian trade unions" -d --rm --name nfp wolnosciowiec/news-feed-provider 
```
