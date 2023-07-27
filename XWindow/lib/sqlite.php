<?php

class sqlite implements \Countable {
    /**
     * @var PDO
     */
    private $db = null;

    /**
     * @var string
     */
    private $name = null;

    public function __construct($name, $filename = null)
    {
        if ($filename == null) $filename = $GLOBALS['config']("sqlite_dbname");

        $this->db = new PDO('sqlite:' . $filename);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->name = $name;
        $this->createTable();
    }
    
    static function load($name, $filename = null) {
        if ($filename == null) $filename = $GLOBALS['config']("sqlite_dbname");

		if (is_int(strripos($name, '..'))) {
			die("error sqlite file name:$name");
		}
		$file = str_replace('.', '/', $name);

		return new sqlite($name, $filename);
	}

    /**
     * @param none
     *
     * @throws InvalidArgumentException
     * @return string|null
     */
    public function getAll($offset=0, $limit=0)
    {
        $addSql = '';
        if ($offset) {
            $addSql .= " OFFSET " . $offset;
        }
        if ($limit){
            $addSql = ' LIMIT ' . $limit;
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM `' . $this->name . '`' . $addSql . ';'
        );
        $stmt->execute();

        $res = [];
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            array_push($res, $row);
        }

        return $res;
    }

    /**
     * @param string $key key
     *
     * @throws InvalidArgumentException
     * @return string|null
     */
    public function getOnce($col, $key, $offset=0, $limit=0)
    {
        if (!is_string($key) ||!is_string($col)) {
            throw new InvalidArgumentException('Expected string as key or a column name!');
        }
        
        $addSql = '';
        if ($offset != 0 || $limit != 0){
            $addSql = ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM `' . $this->name . '` WHERE `'. $col .'` = :key' . $addSql . ';'
        );
        $stmt->bindParam(':key', $key, PDO::PARAM_STR);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            return $this->stdclassToArray($row);
        }

        return null;
    }
    
    /**
     * @param string $key key
     *
     * @throws InvalidArgumentException
     * @return string|null
     */
    public function get($col, $key, $offset=0, $limit=0)
    {
        if (!is_string($key) ||!is_string($col)) {
            throw new InvalidArgumentException('Expected string as key or a column name!');
        }

        $addSql = '';
        if ($offset != 0 || $limit != 0){
            $addSql = ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM `' . $this->name . '` WHERE `'. $col .'` = :key' . $addSql . ';'
        );
        $stmt->bindParam(':key', $key, PDO::PARAM_STR);
        $stmt->execute();

        $res = [];
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            array_push($res, $row);
        }

        return $res;
    }

    /**
     * @param string $key key
     * @param string $value value
     *
     * @throws InvalidArgumentException
     */
    public function update($key, $value, $whereKey, $whereValue)
    {
        if (!is_string($key) || !is_string($whereKey)) {
            throw new InvalidArgumentException('Expected string as key');
        }

        $queryString = 'UPDATE `' . $this->name . '` SET `' . $key . '`=:value WHERE `' . $whereKey . '`=:whereValue;';
        $stmt = $this->db->prepare($queryString);
        $stmt->bindParam(':value', $value, \PDO::PARAM_STR);
        $stmt->bindParam(':whereValue', $whereValue, \PDO::PARAM_STR);
        $stmt->execute();
    }
    
    /**
     * @param array $value value
     * @param array $col column name
     *
     * @throws InvalidArgumentException
     */
    public function insert($value, $col)
    {
        if (!is_array($value) || !is_array($col)) {
            throw new InvalidArgumentException('Expected array as value or column name');
        }

        $valueString = '';
        $count = 0;
        while($count < count($value)){
            $valueString .= ":key$count";
            if ($count != count($value)-1) $valueString .= ", ";
            $count ++;
        }
        
        $colString = '';
        foreach ($col as $row){
            if ($row != $col[0]) $colString .= ',';
            $colString .= '`' . $row . '`';
        }

        $queryString = 'INSERT INTO `' . $this->name . '` (' . $colString . ') VALUES (' . $valueString . ');';
        
        $stmt = $this->db->prepare($queryString);
        $count = 0;
        while($count < count($value)){
            $stmt->bindParam(":key$count", $value[$count], \PDO::PARAM_STR);
            $count++;
        }
        $stmt->execute();
    }

    /**
     * @param string $key key
     *
     * @return InvalidArgumentException
     */
    public function delete($col, $key)
    {
        if (!is_string($key) ||!is_string($col)) {
            throw new InvalidArgumentException('Expected string as key or a column name!');
        }
        
        $stmt = $this->db->prepare(
            'DELETE FROM `' . $this->name . '` WHERE `' . $col . '` = :key;'
        );
        $stmt->bindParam(':key', $key, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function deleteList($col, $key)
    {
        if (!is_array($key) ||!is_array($col) || count($col) != count($key)) {
            throw new InvalidArgumentException('Expected string as key or a column name!');
        }
        $sql = 'DELETE FROM `' . $this->name . '` WHERE ';
        
        $count = count($col)-1;
        while($count >= 0){
            $sql .= "`".$col[$count]."` = :key$count AND ";
            $count--;
        }
        $stmt = $this->db->prepare(
            $sql . "1=1;"
        );
        $count = count($col)-1;
        while($count >= 0){
            $stmt->bindParam(":key$count", $key[$count], \PDO::PARAM_STR);
            $count--;
        }
        $stmt->execute();
    }

    /**
     * Delete all values from store
     *
     * @return null
     */
    public function deleteAll()
    {
        $stmt = $this->db->prepare('DELETE FROM `' . $this->name . '`');
        $stmt->execute();
        $this->data = array();
    }

    /**
     * @return int
     */
    public function count()
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM `' . $this->name . '`')->fetchColumn();
    }

    /**
     * Create storage table in database if not exists
     *
     * @return null
     */
    private function createTable()
    {
        $stmt = 'CREATE TABLE IF NOT EXISTS "' . $this->name . '"';
        $stmt.= '(key TEXT PRIMARY KEY, value TEXT);';
        $this->db->exec($stmt);
    }
    
    private function stdclassToArray($stdclass)
    {
        return json_decode(json_encode($stdclass), true);
    }
}
