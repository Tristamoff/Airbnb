# Airbnb

## Задача
Даётся словарь слов и матрица букв. Необходимо найти в матрице, слова из словаря.

## Решение
В классе Airbnb.php

## Запуск
В методе **__construct** класса **Airbnb** укажите реквизиты доступа к БД и (если вы поменяли имя таблицы с матрицей или у вас их несколько) имя таблицы с матрицей. Если хотите видеть процесс поиска - при создании экземпляра класса передайте ему `true`.

```php
include('./Airbnb.php');

$airbnb = new Airbnb(true);
$airbnb->run('компьютер');
$airbnb->run('квадрокоптер');
$airbnb->run('кошка');
$airbnb->run('диалект');
```
