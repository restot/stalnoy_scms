<?php
#Fast SQL
/**
* Класс для работы с Mysql базой данных.
* необходим для быстрой загрузки больших массивов данных в базу.
* осуществляется быстроействие за счет добавления запросов в файл, а он затем отправляется в базу.
* быстродействие увеличивается минимум в х20 раз. даже объедененные запросы по 50к 100к или 500к символов,
* в зависимости от того на какие запросы по обьему обеспеченна поддержка вашей базой данных, проигрывают по быстродействию
* если выполнять запросы из файла.
*
* Пользуйтесь, старался restot.
*
* Класс SafeMySQL необходим для работы
* @link http://phpfaq.ru/safemysql
* NOTE: only INSERT mode work yet.
* Пример:
* $table='last_export'; какая то таблица
* $db = new SafeMysql(array('user' => $db_user, 'pass' => $db_pass, 'db' => $db_database, 'charset' => 'utf8')); экземпляр класса с авторизацией
* $test =new fastsql; экземпляр класса
* $q=$test->getCOls($db,$table,'hash_key'); получаем все столбцы кроме hash_key
* foreach ($array as $a => $b) { входящий массив вносим в запрос
* $test->addCol($v2);
* }
*
* $test->addRow(); завершаем текущий запрос подготавливаем новый...
* $test->sendData($db,$table,$parseCol); завершаем последний запрос, записываем в файл и отправляем в базу.
 * NOTE:P.s  перепроверяйте работу класса во время знакомства, класс очень дружелюбен к ошибкам, и грузит данные в любом виде...
*/
class fastsql
{
    private $data_file = __DIR__."\data.txt"; #создается файл для последующей отправки его в базу.
    private $data; # образ файла в памяти, все информация в последствии добавляется в файл.

/**
* создает или в текущих запрос добавляет необходимые значения, только для INSERT MODE
* @param (string) $value
*/

  public function addCol(string $value)
  {
    $this->string .= $value.',,';
  }

  /**
  * Функция для разделения запросов, подготавливает условия для создания нового запроса только для INSERT MODE
  * @param no args()
  */
  public function addRow()
    {
        $this->string = substr($this->string, 0, -2);
        $this->string .= '%%';
        $this->data .= $this->string;

        unset($this->string);
    }
/**
 * Функция получает необходимые столбцы для отновления или вставки, необходима для UPDATE / INSERT MODEs.
 * @param   SafeMySQL class($db) экземпляр класса
 * @param   strind($table)
 * @param   strind / array($filter)
*/
    public function getCOls( $db, string $table,$filter=NULL)
    {

        $cols = $db->getCol('SELECT COLUMN_NAME
	         FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE()
	          AND TABLE_NAME = ?s
	           ORDER BY ORDINAL_POSITION', $table);

            $parseCol = '';

            foreach ($cols as $c1 => $c2) {

              if ( $filter!=NULL && in_array($c2,(is_array($filter)?$filter:(array)$filter))){
                    echo $c2." col ignored\n";
                    continue;
                  }
                  $parseCol.=$db->parse("?n,", $c2);
                }

                $parseCol=substr($parseCol, 0, -1);
                return $parseCol;
    }

/*
Функция подготавливает данные для записи в файл, и затем файл отправляется в базу и удаляется.

@param  SafeMySQL class($db) экземпляр класса
@param   strind($table)
@param   strind($cols)
*/
    public function sendData($db,$table,$cols)
    {
        $this->data = substr($this->data, 0, -2);

        file_put_contents($this->data_file, $this->data);

        $qu = $db->query("LOAD DATA LOCAL INFILE ?p INTO TABLE ?n FIELDS TERMINATED BY ',,' LINES TERMINATED BY '%%' ($cols)", '"'.str_replace('\\', '\\\\', $this->data_file).'"', $table);
        if ($qu) {
            echo 'Loading complete!'.EOL;
            unlink($this->data_file);
            unset($this->data);
            unset($qu);
        }
    }
}
