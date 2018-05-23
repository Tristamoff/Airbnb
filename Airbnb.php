<?php

class Airbnb {
    protected $db;//соединение с БД
    protected $letter;//искомое слово
    protected $chars;//буквы искомого слова
    protected $coords;//координаты букв слова
    protected $matrix_size;
    protected $map;//сохраняем найденные направления слова

    protected $steps;//сколько шагов прошла рекурсивная функция поиска по матрице
    protected $first_coords;//начальные координаты, с которых начинаем поиск
    protected $result_coords;//найденные координаты слова
    protected $debug;//показывать ли процесс поиска букв
    protected $table;//имя таблицы с матрицей

    function __construct($debug = false)
    {
        $db_user = 'db_user';
        $db_name = 'db_name';
        $db_pass = 'db_pass';
        $this->table = 'airbnb';
        $db  =  new  PDO('mysql:dbname=' . $db_name . '; host=localhost', $db_user, $db_pass);
        $db->exec('SET CHARACTER SET utf8');
        $this->db = $db;
        $this->matrix_size = ['x' => 0, 'y' => 0];
        $this->debug = $debug;

        //узнаём размеры матрицы
        $this->getMatrixSize();
    }

    /**
     * Поиск координат буквы
     *
     * @param $char - символ
     * @return array - массив координат
     */
    function searchCharCoords($char) {
        $sql = 'select * from ' . $this->table . ' where letter=? order by y, x';
        $sth = $this->db->prepare($sql);
        $sth->bindParam(1, $char,PDO::PARAM_STR);
        $sth->execute();
        $res=$sth->fetchAll(PDO::FETCH_ASSOC);
        $resp = [];
        foreach ($res as $row){
            $resp[] = ['x' => $row['x'], 'y' => $row['y']];
        }
        return $resp;
    }

    /**
     * Поиск слова
     *
     * @param $letter - слово
     * @return mixed
     */
    function run($letter) {
        //сохраняем искомое слово
        $this->letter = $letter;

        //разбиваем его на символы
        $this->chars = $this->splittingLetter();

        //сохраняем координаты каждой буквы искомого слова
        $this->saveCoords();
        if ($this->result_coords) {
            // в матрице не найдены все буквы слова - прекращаем работу функции
            $this->renderResult();
            $this->result_coords = NULL;
            return NULL;
        }

        //обходим символы
        $char_i = 0;
        foreach ($this->coords[$this->chars[0]] as $coord_id => $coords) {
            $this->first_coords = ['x' => $coords['x'], 'y' => $coords['y']];
            $this->debug('Начинаем поиск слова <b>' . $this->letter . '</b> первой буквы - <b>' . $this->chars[$char_i] . '</b> с координат ' . $coords['x'] . ':' . $coords['y']);

            // поиск следующей буквы
            $next = $this->searchNextChar(1, $coords);
            if ($this->result_coords) {
                // если найдены координаты последней буквы - прекращаем работу функции
                $this->renderResult();
                $this->result_coords = NULL;
                return NULL;
            }

            if (!$next) {
                // тупиковая ветвь, откатываемся на одну букву
                $this->debug('Тупик, откатываемся на шаг назад');
                $back = count($this->map);
                while($back > 0) {
                    // смотрим направление, выбранное на предыдущем шаге
                    $dir = $this->map[$back]['dir'];
                    $coords = $this->map[($back - 1)]['coord'];
                    $prev = FALSE;
                    if ($back > 1) {
                        $prev = $this->map[($back - 2)]['coord'];
                    }
                    if ($dir == 3) {
                        // ранее было выбрано направление "налево", откатываемся ещё на один шаг
                        $back--;
                        continue;
                    }
                    $dir++;
                    if ($back == 2) {
                        $prev = $this->first_coords;
                    }
                    if ($back == 1) {
                        $prev = false;
                        $coords = $this->first_coords;
                    }

                    // обнуляем количество шагов, сделанных назад
                    $this->steps = 0;
                    $this->searchNextChar($back, $coords, $dir, $prev);
                    if ($this->result_coords) {
                        $this->renderResult();
                        $this->result_coords = NULL;
                        return NULL;
                    }
                    // сохраняем количество шагов, сделанных назад, чтобы понять какая буква будет следующей
                    $back += $this->steps;

                    $back--;
                }
            }
        }
        $this->renderResult();
    }

    /**
     * Отображение результата - координаты букв найденного слова
     */
    function renderResult() {
        if (is_array($this->result_coords)) {
            echo 'Слово <b>' . $this->letter . '</b> найдено!<br />';
            foreach ($this->result_coords  as $coord) {
                echo 'x: ' . $coord['x'] . ' y: ' . $coord['y'] . '<br />';
            }
        } else {
            echo 'Слово <b>' . $this->letter . '</b> не найдено!<br />';
            echo $this->result_coords . '<br />';
        }
        echo '<hr />';
        $this->steps = 0;
        $this->map = NULL;
    }

    /**
     * Поиск следующей буквы
     *
     * @param int $char_i - номер искомой буквы
     * @param array $coords - координаты текущей буквы
     * @param int $dir - направление поиска (0 - вверх, 1 - направо, 2 - налево, 3 - вниз)
     * @param bool $prev - координаты предыдущей буквы
     */
    function searchNextChar($char_i = 0, $coords = [], $dir = 0, $prev = FALSE) {
        if ($char_i >= count($this->chars)) {
            // номер искомой буквы равен количеству букв в слове
            $this->result_coords = [$this->first_coords];
            foreach ($this->map as $place) {
                $this->result_coords[] = $place['coord'];
            }
            return $this->result_coords;
        }

        $this->debug('Ищем букву номер ' . $char_i.' - <b>' . $this->chars[$char_i] . '</b> вокруг координат ' . $coords['x'] . ':' . $coords['y']);
        $char = $this->chars[$char_i];
        // пробуем найти ускомую букву в указанном направлении, с учётом координат текущей и предыдущей букв
        $next_char_coords = $this->searchNextCharCoords($char, $coords, $dir, $prev);
        if (!$next_char_coords) {
            // буква не найдена в этом направлении,
            if ($dir < 3) {
                // ещё есть пути обхода
                $dir++;
                // рекурсивно вызываем текущую функцию поиска
                $next_char_coords = $this->searchNextChar($char_i, $coords, $dir, $prev);
            } else {
                //нет путей обхода
                return FALSE;
            }
        } else {
            // буква найдена в этом направлении
            $this->map[$char_i] = ['dir' => $dir, 'coord' => $next_char_coords];
            $char_i++;
            $this->steps++;
            $next_char_coords = $this->searchNextChar($char_i, $next_char_coords, 0, $coords);
        }
        return $next_char_coords;
    }

    /**
     * Поиск координат следующей буквы
     *
     * @param string $char - буква
     * @param array $coords - координаты текущей буквы
     * @param int $dir - направление поиска (0 - вверх, 1 - направо, 2 - вниз, 3 - налево)
     * @param bool $prev - координаты предыдущей буквы
     */
    function searchNextCharCoords($char, $coords = [], $dir = 0, $prev = false) {
        // координаты возможной следующей буквы, согласно направлению
        $x = $coords['x'];
        $y = $coords['y'];
        switch ($dir) {
            case 0:
                $y--;
                break;
            case 1:
                $x++;
                break;
            case 2:
                $y++;
                break;
            case 3:
                $x--;
                break;
        }

        if (($x < 1) || ($y < 1) || ($x > $this->matrix_size['x']) || ($y > $this->matrix_size['y'])) {
            // в этом направлении нельзя искать, край матрицы
            return FALSE;
        }

        if (($prev['x'] == $x) && ($prev['y'] == $y)) {
            $this->debug('Пропускаем координаты ' . $x . ':' . $y . ', совпадение с предыдущими');
            return FALSE;
        }

        // перебираем координаты буквы, сравниваем координаты с полученными
        foreach ($this->coords[$char] as $coords) {
            if (($coords['x'] == $x) && ($coords['y'] == $y)) {
                // буква найдена
                $this->debug('Буква <b>' . $char . '</b> найдена в координатах ' . $x . ':' . $y);
                return $coords;
            }
        }
        // в искомых координатах нет нужной буквы
        $this->debug('Буква <b>' . $char . '</b> не найдена в координатах ' . $x . ':' . $y);
        return FALSE;
    }

    /**
     * Определение размеров матрицы
     */
    function getMatrixSize() {
        $sql = 'select max(x) as max_x, max(y) as max_y from ' . $this->table;
        $sth = $this->db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);
        foreach ($res as $row){
            $this->matrix_size = ['x' => $row['max_x'], 'y' => $row['max_y']];
        }
        $this->debug('Размеры матрицы ' . $this->matrix_size['x'] . ' на ' . $this->matrix_size['y']);
    }

    /**
     * Сохранение координат каждого символа искомого слова
     */
    function saveCoords() {
        $this->coords = [];
        // обходим искомое слово побуквенно
        foreach (array_unique($this->chars) as $char_index => $char) {
            $tmp_coords = $this->searchCharCoords($char);
            // сохраняем координаты каждого символа искомого слова
            if ($tmp_coords) {
                $this->coords[$char] = $tmp_coords;
            }
        }

        if (count(array_unique($this->chars)) != count($this->coords)) {
            //возможно каких-то символов слова нет в матрице
            $this->result_coords = 'Не все символы искомого слова присутствуют в матрице';
            //exit('Не все символы искомого слова присутствуют в матрице');
        }
    }

    /**
     * Разбиение слова на символы
     *
     * @return array
     */
    function splittingLetter() {
        $chars = preg_split('//u', $this->letter, NULL, PREG_SPLIT_NO_EMPTY);
        return $chars;
    }

    /**
     * Вывод отладочного сообщения
     *
     * @param string $mess - сообщение
     */
    function debug($mess) {
        if ($this->debug) {
            echo $mess . '<br />';
        }
    }
}
