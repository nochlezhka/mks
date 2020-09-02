## Лайфхаки и важные заметки

Symfony предкомпилирует большое количество скриптов в несколько файлов, чтобы быстрее сайт загружался. А девелоперская - вроде бы тоже, но хотя бы пересчитывает кеш при изменении файлов.

Иногда после обновления кода нужно сбросить кеш в контейнере с приложением. Чтобы это сделать, нужно зайти в контейнер с приложением и выполнить команду:

``` bash
./app/console cache:clear --env=prod
```

### Генерация новых миграций

Doctrine в составе Symfony умеет генерировать миграции для БД после изменения моделей, это делается такой командой:
```shell script
./app/console doctrine:migrations:diff
```
Но по какой-то причине в МКС генератор миграций добавляет много строк, которые по факту ничего не меняют в схеме БД.
Возможно сказывается возраст библиотеки Doctrine, которую сейчас использует МКС.

Немного облегчить ситуацию поможет такой патч в `shared/homeless/vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/MySqlSchemaManager.php`:
```patch
@@ -182,7 +182,7 @@ class MySqlSchemaManager extends AbstractSchemaManager
             'length'        => $length,
             'unsigned'      => (bool) (strpos($tableColumn['type'], 'unsigned') !== false),
             'fixed'         => (bool) $fixed,
-            'default'       => isset($tableColumn['default']) ? $tableColumn['default'] : null,
+            'default'       => isset($tableColumn['default']) && $tableColumn['default'] !== 'NULL' ? $tableColumn['default'] : null,
             'notnull'       => (bool) ($tableColumn['null'] != 'YES'),
             'scale'         => null,
             'precision'     => null,
```
С ним лишние альтеры пропадают, но, к сожалению, остаётся ещё существенное количество изменений,
которые образовались, видимо, из-за того, что часть миграций была написана вручную.
