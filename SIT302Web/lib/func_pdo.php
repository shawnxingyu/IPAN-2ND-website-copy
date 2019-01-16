<?php
/**
 *  PDO数据库操作函数库
 */

//连接数据库
if (!function_exists('connect'))
{
    /**
     * @param $dbname
     * @param string $type
     * @param string $host
     * @param string $charset
     * @param int $port
     * @param string $username
     * @param string $pass
     * @return PDO
     */
    function connect($dbname, $type='mysql', $host='127.0.0.1', $charset='utf8', $port=3306, $username='root', $pass='WebSIT306')
    {
        $dsn = "{$type}:host={$host};dbname={$dbname};charset={$charset};port={$port};";
        $userName = $username;
        $password = $pass;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //错误模式
            PDO::ATTR_CASE => PDO::CASE_NATURAL, //自然名称
            PDO::ATTR_EMULATE_PREPARES => true, //启用模拟功能
            PDO::ATTR_PREFETCH => true
        ];
        try{
            $pdo = new PDO($dsn, $userName, $password, $options);
            echo 'OK';
        }catch (PDOException $e){
            print 'connect error: '.$e->getMessage();
            die();
        }

        return $pdo;
    }
}

//新增数据
if(!function_exists('insert'))
{
    /**
     * @param $pdo
     * @param $table
     * @param array $data
     * @return bool
     */
    function insert($pdo, $table, $data=[])
    {
        //创建SQL语句
        $sql = "INSERT IGNORE {$table} SET ";
        foreach (array_keys($data) as $field){
            $sql .=$field.'=:'.$field.',';
        }
        $sql = rtrim(trim($sql),',');
        $sql .=';';
        //die($sql);

        //创建stmt对象
        $stmt=$pdo->prepare($sql);

        //绑定参数到预处理对象
        foreach ($data as $field=>$value){
            $stmt->bindValue(":{$field}",$value);
        }

        //执行新增操作
        if ($stmt->execute()){
            if ($stmt->rowCount()>0){
                return true;
            }
        }else{
            return false;
        }
    }
}

//更新操作
if(!function_exists('update'))
{
    /**
     * @param $pdo
     * @param $table
     * @param array $data
     * @param string $where
     * @return bool
     */
    function update($pdo, $table, $data=[], $where='')
    {
        //创建SQL语句
        $sql = "UPDATE {$table} SET ";
        foreach (array_keys($data) as $field){
            $sql .=$field.'=:'.$field.',';
        }
        $sql = rtrim(trim($sql),',');

        //添加更新条件
        if (!empty($where)){
            $sql .=' WHERE '.$where;
        } else {
            exit('condition should not be empty!');
        }
        $sql = $sql = rtrim(trim($sql),',').';';

        //die($sql);

        //创建stmt对象
        $stmt=$pdo->prepare($sql);

        //绑定参数到预处理对象
        foreach ($data as $field=>$value){
            $stmt->bindValue(":{$field}",$value);
        }

        //执行更新操作
        if ($stmt->execute()){
            if ($stmt->rowCount()>0){
                return true;
            }
        }else{
            return false;
        }
    }
}

//查询单条记录
if(!function_exists('find'))
{
    /**
     * 查询单条语句
     * @param $pdo
     * @param $table
     * @param $fields
     * @param string $where
     * @return bool
     */
    function find($pdo, $table, $fields, $where='')
    {
        //创建SQL语句
        $sql = "SELECT ";
        if (is_array($fields)){
            foreach ($fields as $field){
                $sql .=$field.', ';
            }
        } else {
            $sql .= $fields. ' ';
        }
        $sql =rtrim(trim($sql),',');

        $sql .=" FROM ".$table;

        if (!empty($where)){
            $sql .=' WHERE '.$where;
        }
        $sql .=' LIMIT 1;';
        //die($sql);

        //创建STMT对象
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute()){
            if ($stmt->rowCount()>0){
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                return $stmt->fetch();
            }
        }else{
            return false;
        }

    }
}

//查询多条记录
if(!function_exists('select'))
{
    /**
     * @param $pdo
     * @param $table
     * @param $fields
     * @param string $where
     * @param string $order
     * @return bool
     */
    function select($pdo, $table, $fields, $where='',$order='')
    {
        //创建SQL语句
        $sql = "SELECT ";
        if (is_array($fields)){
            foreach ($fields as $field){
                $sql .=$field.', ';
            }
        } else {
            $sql .= $fields. ' ';
        }
        $sql =rtrim(trim($sql),',');

        $sql .=" FROM ".$table;

        if (!empty($where)){
            $sql .=' WHERE '.$where;
        }

        if (!empty($order)){
            $sql .=' ORDER BY '.$order;
        }
        $sql = rtrim(trim($sql),',').';';

        //die($sql);

        //创建STMT对象
        $stmt = $pdo->prepare($sql);
        //die($stmt->queryString);

        if ($stmt->execute()){
            if ($stmt->rowCount()>0){
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                return $stmt->fetchAll();
            }
        }else{
            return false;
        }

    }
}

//删除操作
if(!function_exists('delete'))
{
    /**
     * @param $pdo
     * @param $table
     * @param string $where
     * @return bool
     */
    function delete($pdo, $table, $where='')
    {
        //创建SQL语句
        $sql = "DELETE FROM {$table} ";

        //添加删除条件
        if (!empty($where)){
            $sql .=' WHERE '.$where;
        } else {
            exit('condition should not be empty!');
        }
        $sql = $sql = rtrim(trim($sql),',').';';

       // die($sql);

        //创建stmt对象
        $stmt=$pdo->prepare($sql);

        //执行更新操作
        if ($stmt->execute()){
            if ($stmt->rowCount()>0){
                return true;
            }
        }else{
            return false;
        }
    }
}

