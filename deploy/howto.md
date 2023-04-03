## Обзор

Файл `deploy/docker-compose.yml` предназначен для промышленного развертывания MKS. Конфигурация поддерживает два профиля (их можно использовать как вместе, так и отдельно):
* `local`
  * в случае использования данного профиля развертывание будет дополнительно содержать докер контейнер с MySQL базой данных
* `certbot`
  * в случае использования данного профиля развертывание будет дополнительно содержать `certbot` компонент предназначенный для выписывания и обновления SSL сертификатов с помощью сервиса `Let's Encrypt`

## Предварительные шаги
0. Создайте в вашем DNS домене запись типа А, которая будет указывать на IP сервера.

## Как запустить
1. создайте на сервере папку `deploy` и поместить туда файл `docker-compose.yml` из папки `deploy`
2. создайте копию файла `.env.dist` под названием `.env` и заполните его в соответствии с вашей организацией и поместите в папку `deploy`
3. создайте подпапки `./storage/data/uploads` `/storage/data/letsencrypt` `./storage/data/certbot` `./storage/mysql_data` в папке `deploy`

### MKS в режиме HTTP
4. сделайте экспорт переменной содержащей версию MKS (актуальную версию можно взять со страницы с релизами: https://github.com/nochlezhka/mks/releases)
```sh
export MKS_VERSION="<VERSION>"
```
5. Запустите MKS в режиме HTTP (если вы хотите использовать локальную базу, то добавьте `--profile local`)
```sh

docker-compose up -d
```

### MKS в режиме HTTPS
4. сделайте экспорт следующих переменных
```sh
export MKS_VERSION="<VERSION>"
export MKS_DOMAIN="<YOUR_DOMAIN>"
export MKS_SUPPORT_EMAIL="<YOUR_EMAIL>"
export NGINX_MODE=https_init
```

5. запустите `nginx`: `docker-compose up nginx -d`
6. запустите `certbot: `docker-compose --profile certbot up -d`
7. дождитесь завершения выписывания сертификатов (за процессом можно наблюдать через `docker logs -f certbot`)
8. сделайте экспорт переменной: `export NGINX_MODE=https`
9. запустите MKS в режиме HTTPS (если вы хотите использовать локальную базу, то добавьте `--profile local`)
```sh
docker-compose --profile certbot up -d
```

## Конфигурация
1. выполните следующую команду, чтобы выполнить миграцию базы данных
```sh
docker exec mks-app ./bin/console doctrine:migrations:migrate --no-interaction --env=prod
```
2. сконфигурируйте пароль
```sh
docker exec mks-app ./bin/console fos:user:change-password admin "<PASSWORD>" --env=prod
```
