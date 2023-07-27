<?php

class mysql implements \Countable
{
    /**
    * @var PDO
    */
    private $db = null;

    /**
    * @var string
    */
    private $name = null;

    public function __construct($dbName, $name = null, $dbms = null, $host = null, $user = null, $pass = null) {
        if ($dbms == null) $dbms = $GLOBALS['config']("mysql_ms");
        if ($host == null) $dbms = $GLOBALS['config']("mysql_host");
        if ($user == null) $dbms = $GLOBALS['config']("mysql_user");
        if ($pass == null) $dbms = $GLOBALS['config']("mysql_password");

        $QQBotDsn = "$dbms:host=$host;dbname=$dbName";
        try {
            $this->db = new PDO($QQBotDsn, $user, $pass); //初始化一个PDO对象
            // echo "连接成功<br/>";
        } catch (PDOException $e) {
            die ("MYSQL connect error!: " . $e->getMessage() . "<br/>");
        }
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->name = $name;
        // $this->createTable();
    }

    static function load($dbName, $name=null, $dbms = null, $host = null, $user = null, $pass = null) {
        if ($dbms == null) $dbms = $GLOBALS['config']("mysql_ms");
        if ($host == null) $dbms = $GLOBALS['config']("mysql_host");
        if ($user == null) $dbms = $GLOBALS['config']("mysql_user");
        if ($pass == null) $dbms = $GLOBALS['config']("mysql_password");
        
		return new mysql($dbName, $name=$name, $dbms = $dbms, $host = $host, $user = $user, $pass = $pass);
	}

    /**
     * @param none
     *
     * @throws InvalidArgumentException
     * @return string|null
     */
    public function getAll($name, $offset=0, $limit=0)
    {
        $addSql = '';
        if ($offset != 0 || $limit != 0){
            $addSql = ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        }
        
        $stmt = $this->db->prepare(
            'SELECT * FROM `' . $name . '`' . $addSql . ';'
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
    public function getOnce($col, $key, $name, $offset=0, $limit=0)
    {
        if (!is_string($key) ||!is_string($col)) {
            throw new InvalidArgumentException('Expected string as key or a column name!');
        }
        
        $addSql = '';
        if ($offset != 0 || $limit != 0){
            $addSql = ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM `' . $name . '` WHERE `'. $col .'` = :key' . $addSql . ';'
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
    public function get($col, $key, $name, $offset=0, $limit=0)
    {
        if (!is_string($key) ||!is_string($col)) {
            throw new InvalidArgumentException('Expected string as key or a column name!');
        }

        $addSql = '';
        if ($offset != 0 || $limit != 0){
            $addSql = ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM `' . $name . '` WHERE `'. $col .'` = :key' . $addSql . ';'
        );
        $stmt->bindParam(':key', $key, PDO::PARAM_STR);
        $stmt->execute();

        $res = [];
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            array_push($res, $row);
        }

        return $res;
    }
    
    public function getList($col, $key, $name, $offset=0, $limit=0)
    {
        if (!is_array($key) ||!is_array($col) || count($col) != count($key)) {
            throw new InvalidArgumentException('Expected string as key or a column name!');
        }

        $addSql = '';
        if ($offset != 0 || $limit != 0){
            $addSql = ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        }
        $sql = 'SELECT * FROM `' . $name . '` WHERE ';
        
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
            $stmt->bindParam(":key$count", $key[$count], PDO::PARAM_STR);
            $count--;
        }
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
    public function update($key, $value, $whereKey, $whereValue, $name)
    {
        if (!is_string($key) || !is_string($whereKey)) {
            throw new InvalidArgumentException('Expected string as key');
        }

        $queryString = 'UPDATE `' . $name . '` SET `' . $key . '`=:value WHERE `' . $whereKey . '`=:whereValue;';
        $stmt = $this->db->prepare($queryString);
        $stmt->bindParam(':value', $value, \PDO::PARAM_STR);
        $stmt->bindParam(':whereValue', $whereValue, \PDO::PARAM_STR);
        $stmt->execute();
    }
    
    public function updateListPair($value, $whereKey, $whereValue, $name)
    {
        $queryString = "UPDATE `$name` SET ";
        $count = count($value)-1;
        foreach ($value as $key => $val){
            $queryString .= "`$key`=:value$count";
            if($count) $queryString .= ',';
            $count--;
        }
        
        $queryString .= ' WHERE ';
        
        $count = count($whereKey)-1;
        while($count >= 0){
            $queryString .= "`".$whereKey[$count]."` = :key$count AND ";
            $count--;
        }
        // die($queryString);
        $stmt = $this->db->prepare(
            $queryString . "1=1;"
        );
        $count = count($whereValue)-1;
        while($count >= 0){
            $stmt->bindParam(":key$count", $whereValue[$count], \PDO::PARAM_STR);
            $count--;
        }
        
        $count = count($value)-1;
        foreach ($value as $key => $val){
            echo $key."=>".$val."<br>";
            $stmt->bindParam(":value$count", $val, \PDO::PARAM_STR);
            $count--;
        }

        $stmt->execute();
    }
    
    public function updateList($key, $value, $whereKey, $whereValue, $name)
    {
        $queryString = "UPDATE `$name` SET `$key`=:value WHERE ";
        
        $count = count($whereKey)-1;
        while($count >= 0){
            $queryString .= "`".$whereKey[$count]."` = :key$count AND ";
            $count--;
        }
        
        $stmt = $this->db->prepare(
            $queryString . "1=1;"
        );
        $count = count($whereValue)-1;
        while($count >= 0){
            $stmt->bindParam(":key$count", $whereValue[$count], \PDO::PARAM_STR);
            $count--;
        }
        
        $stmt->bindParam(":value", $value, \PDO::PARAM_STR);
        $stmt->execute();
    }
    
    /**
     * @param array $value value
     * @param array $col column name
     *
     * @throws InvalidArgumentException
     */
    public function insert($value, $col, $name)
    {
        if (!is_array($value) || !is_array($col)) {
            throw new InvalidArgumentException('Expected array as value or column name');
        }
        
        
        $colString = '`' . implode('`, `', $col) . '`';
        
        $valueString = ':' . implode(', :', $col);
        
        $queryString = 'INSERT INTO `' . $name . '` (' . $colString . ') VALUES (' . $valueString . ');';
        
        $stmt = $this->db->prepare($queryString);
        $len = count($col)-1;
        while ($len >= 0){
            $stmt->bindParam(':'.$col[$len], $value[$len], \PDO::PARAM_STR);
            // echo $col[$len]."to".$value[$len]."<br>";
            $len--;
        }
        $stmt->execute();
    }

    /**
     * @param string $key key
     *
     * @return InvalidArgumentException
     */
    public function delete($col, $key, $name)
    {
        if (!is_string($key) ||!is_string($col)) {
            throw new InvalidArgumentException('Expected string as key or a column name!');
        }
        
        $stmt = $this->db->prepare(
            'DELETE FROM `' . $name . '` WHERE `' . $col . '` = :key;'
        );
        $stmt->bindParam(':key', $key, \PDO::PARAM_STR);
        $stmt->execute();
    }
    
    public function deleteList($col, $key, $name)
    {
        if (!is_array($key) ||!is_array($col) || count($col) != count($key)) {
            throw new InvalidArgumentException('Expected string as key or a column name!');
        }
        $sql = 'DELETE FROM `' . $name . '` WHERE ';
        
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
    public function deleteAll($name)
    {
        $stmt = $this->db->prepare('DELETE FROM `' . $name . '`');
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
    
    public function select($sql)
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $res = [];
        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
            array_push($res, $row);
        }

        return $res;
    }
    
    public function selectOnce($sql)
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            return $this->stdclassToArray($row);
        }

        return null;
    }
    
    public function common($sql)
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
    }

    /**
     * Create storage table in database if not exists
     *
     * @return null
     */
    private function createTable($name)
    {
        $stmt = 'CREATE TABLE IF NOT EXISTS "' . $name . '"';
        $stmt.= '(key TEXT PRIMARY KEY, value TEXT);';
        $this->db->exec($stmt);
    }
    
    private function stdclassToArray($stdclass)
    {
        return json_decode(json_encode($stdclass), true);
    }
}