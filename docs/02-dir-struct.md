# Структура директорий

## docker

Директория, в которой хранятся `Dockerfile`-ы основных образов (`mailhog`, `node`, `php`, `nginx`) и скрипты входа в docker-контейнеры (в папке `docker/docker`).

## shared

Директория, в которой хранятся склонированные репозитории.

# Конфигурационные файлы

## .env

Файл в основном используется для конфигурации первоначальной сборки проекта.

## docker-compose.yml

См. https://docs.docker.com/compose/compose-file/compose-file-v2/

## shared/homeless/app/config/parameters.yml

См. https://symfony.com/doc/current/service_container/parameters.html


