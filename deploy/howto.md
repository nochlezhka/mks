## Обзор

Файл `deploy/docker-compose.yml` предназначен для промышленного развертывания MKS. Веб-сервер — `Caddy`, он же сам выписывает и обновляет SSL сертификаты в `Let's Encrypt`. Конфигурация поддерживает профиль `local`: в этом случае развертывание дополнительно содержит докер контейнер с MySQL базой данных.

## Предварительные шаги
0. Создайте в вашем DNS домене запись типа А, которая будет указывать на IP сервера.

## Как запустить
1. создайте на сервере папку `deploy` и поместить туда файл `docker-compose.yml` из папки `deploy`
2. создайте копию файла `.env.dist` под названием `.env` и заполните его в соответствии с вашей организацией и поместите в папку `deploy`
3. создайте подпапки `./storage/data/uploads` `./storage/data/caddy` `./storage/mysql_data` в папке `deploy`. Контейнеры работают от пользователя с UID 1000, поэтому он должен иметь право записи в `./storage/data/caddy` (там хранятся сертификаты): `chown 1000:1000 ./storage/data/caddy`

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
Для выписывания сертификата порты 80 и 443 сервера должны быть доступны из интернета и вести на контейнер (`HTTP_PORT=80`, `HTTPS_PORT=443`), а DNS-запись из «Предварительных шагов» уже должна указывать на сервер.

4. сделайте экспорт следующих переменных
```sh
export MKS_VERSION="<VERSION>"
export MKS_DOMAIN="<YOUR_DOMAIN>"
export MKS_SUPPORT_EMAIL="<YOUR_EMAIL>"
export CADDY_MODE=https
export HTTP_PORT=80
export HTTPS_PORT=443
```

5. запустите MKS в режиме HTTPS (если вы хотите использовать локальную базу, то добавьте `--profile local`)
```sh
docker-compose up -d
```
6. сертификат будет выписан при первом запуске (за процессом можно наблюдать через `docker logs -f caddy`), обновляться он будет автоматически

## Конфигурация
1. выполните следующую команду, чтобы выполнить миграцию базы данных
```sh
docker exec mks-app ./bin/console doctrine:migrations:migrate --no-interaction --env=prod
```
2. сконфигурируйте пароль
```sh
docker exec mks-app ./bin/console fos:user:change-password admin "<PASSWORD>" --env=prod
```

## Обновление с версии на nginx и certbot
Сервисы `nginx` и `certbot` заменены одним `caddy`, переменная `NGINX_MODE` — на `CADDY_MODE` (значения `http` и `https`, режима `https_init` больше нет).
1. обновите `docker-compose.yml`, остановите старые контейнеры: `docker rm -f nginx certbot`
2. создайте `./storage/data/caddy` (см. шаг 3 выше), замените `NGINX_MODE` на `CADDY_MODE` и запустите `docker-compose up -d`
3. Caddy выпишет новый сертификат сам; каталоги `./storage/data/letsencrypt` и `./storage/data/certbot` можно удалить
