Все обновления производить в нерабочее время.

0. Создать бекап базы данных и всех статических данных (загруженные картинки и документы)

  > shared/homeless/web/uploads/images/client/photo
  
  > shared/homeless/web/uploads/files/
  
   И конфигурационные файлы:
  
  > docker-compose.yml
  
  > .env
  
  > shared/homeless/app/config/parameters.yml

1. Перейти в корневую директорию МКС
2. Стянуть изменения из репозитория:

    > git pull

3. Удостовериться, что все парамерты есть в файлах:
	- docker-compose.yml
	- .env
	- shared/homeless/app/config/parameters.yml

4. Запустить сборку контейнеров:

    > make humaid_build_prod # пересобрать приложение пункта выдачи (если нужно)

    > docker-compose build

5. После успешного окончания сборки, запустить контейнеры:

    > docker-compose up -d

6. Для успешного запуска приложения необходимо установить права на директорию:

    > docker-compose exec php chown -R www-data:www-data /var/www/symfony/

7. Подсоединиться к symfony-приложению с помощью команды:
    
    > ./docker/docker/docker-symfony

8. С помощью `composer` установить необходимые библиотеки, затем указать параметры подключения к БД:

    > composer install

9.  Запустить миграцию базы данных для добавления изменений в струкуре:

    > ./app/console doctrine:migrations:migrate

10. Сгенерировать необходимые assets:

    > ./app/console fos:js-routing:dump

    > ./app/console assets:install
    
    > ./app/console assetic:dump --symlink

11. После этого проверить, что МКС доступен через браузер и там есть все данные, которые были до обновления системы.

12. Возможные проблемы:

	Если после обновления системы не хватает каких-то данных (фотографий, документов), необходимо проверить, что на директорию со статическими данными `shared/homeless/web/uploads/` выставлены правильные права:

    > сhown -R homeless:homeless /opt/storage/crm.homeless.ru/mks_private/shared/homeless/web/uploads/
	
	Если после этого не хватает каких-либо файлов, необходимо скопировать их из директории, в которую была сделана их копия перед обновлением (см. п.0)

	Если после обновления системы возникли проблемы с базой данных, необходимо в mysql выгрузить дамп (из п.0)

    > mysqldump MYSQL_DATABASE -uMYSQL_USER -pMYSQL_PASSWORD -h127.0.0.1 --portMYSQL_PORT < dump.sql
    > Параметры MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD, MYSQL_PORT необходимо поменять не те, которые указаны в файле, а dump.sql - на имя файла с последним дампом базы данных

	и пройти все шаги с 9 до конца еще раз
