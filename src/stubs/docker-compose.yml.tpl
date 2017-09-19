version: '2.1'
services:
  app:
    build:
      context: docker/app
    image: <?=$projectName?>/app:latest
    ports:
      - 80:80
      - 443:443
    volumes:
      - .:/var/www/html:cached
    networks:
      - <?=$projectName?>_net

  redis:
    build:
      context: ./docker/redis
    image: <?=$projectName?>/redis:latest
    volumes:
      - <?=$projectName?>_redisdata:/data
    networks:
      - <?=$projectName?>_net

  mysql:
    build:
      context: ./docker/mysql
    image: <?=$projectName?>/mysql:latest
    ports:
      - 3306:3306
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: <?=$projectName . "\n"?>
      MYSQL_USER: homestead
      MYSQL_PASSWORD: secret
    volumes:
      - <?=$projectName?>_mysqldata:/var/lib/mysql
    networks:
      - <?=$projectName?>_net

  node:
    build:
      context: ./docker/node
    image: <?=$projectName?>/node:latest
    volumes:
      - .:/var/www/html
<?php if ($withBlackfire): ?>

  blackfire:
    image: blackfire/blackfire
    environment:
      BLACKFIRE_SERVER_ID: SERVER-ID
      BLACKFIRE_SERVER_TOKEN: SERVER-TOKEN
    networks:
    - <?=$projectName?>_net

<?php endif; ?>
volumes:
  <?=$projectName?>_redisdata:
    driver: local
  <?=$projectName?>_mysqldata:
    driver: local

networks:
  <?=$projectName?>_net:
    driver: bridge
