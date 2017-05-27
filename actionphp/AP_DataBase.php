<?php

/* sample
$db = AP_DataBase::get_instance('localhost','root','mysql');
$db->drop_db('test');
$db->create_db('test');
$db->create_table('test', 'good_table1',
    AP_DataBaseTableInit::get_instance()
        ->addAIPrimaryUnsignedID('id')
        ->addColumn('first_name', AP_DataBaseTableInit::CLOUMN_TYPE_VARCHAR_N,20)
        ->addColumn('last_name', AP_DataBaseTableInit::CLOUMN_TYPE_VARCHAR_N, 20)
        ->addColumn('email', AP_DataBaseTableInit::CLOUMN_TYPE_VARCHAR_N, 50)
        ->addColumn('birthday', AP_DataBaseTableInit::CLOUMN_TYPE_TIMESTAMP)
);
$db->create_table('test', 'good_table2',
    AP_DataBaseTableInit::get_instance()
        ->addAIPrimaryUnsignedID('id')
        ->addColumn('m_id', AP_DataBaseTableInit::CLOUMN_TYPE_INT_N)
);

$db->insert('test','good_table1',array('first_name'=>'紘謙', 'last_name'=>'win'));
$db->insert('test','good_table1',array('first_name'=>'紘謙', 'last_name'=>'win'));
$db->insert('test','good_table1',array('first_name'=>'紘謙', 'last_name'=>'win'));
$db->insert('test','good_table1',array('first_name'=>'紘謙', 'last_name'=>'win'));
$db->insert('test','good_table1',array('first_name'=>'紘謙', 'last_name'=>'win'));

$db->insert('test','good_table2',array('m_id'=>'1'));
$db->insert('test','good_table2',array('m_id'=>'2'));
$db->insert('test','good_table2',array('m_id'=>'3'));
$db->insert('test','good_table2',array('m_id'=>'4'));
$db->insert('test','good_table2',array('m_id'=>'5'));

ap_fun_trace($db->get_last_insert_id());

$db->delete('test','good_table1',
    AP_DataBaseSQLWhere::get_instance()->add_where(AP_DataBaseSQLWhere::WHERE_EQ_K_V, array('id'=>'1'))
);

$db->update('test','good_table1', array(first_name=>'cool1',last_name=>'cool2'),
    AP_DataBaseSQLWhere::get_instance()->add_where(AP_DataBaseSQLWhere::WHERE_EQ_K_V, array('id'=>'2'))
);

ap_fun_trace(
    $db->select('test','good_table1',
    AP_DataBaseSQLWhere::get_instance()->add_where(AP_DataBaseSQLWhere::WHERE_GT_K_V,array('id'=>'0'))
));

ap_fun_trace(
    $db->select_join('test','good_table1','good_table2','id','m_id',
        AP_DataBaseSQLWhere::get_instance()->add_where(AP_DataBaseSQLWhere::WHERE_GT_K_V,array('id'=>'0'),'good_table1'))
);

$db->drop_table('test','good_table2');
*/

class AP_DataBaseTableInit extends AP_Object
{
    const CLOUMN_TYPE_BOOLEAN = "BOOLEAN";			    //0或1
    const CLOUMN_TYPE_TINYINT_N = "TINYINT(n)";		    //8bit 整數(1 byte, N -128~127)
    const CLOUMN_TYPE_SMALLINT_N = "SMALLINT(n)";	    //16bit 整數(2 bytes, N -32,768~32,767)
    const CLOUMN_TYPE_MEDIUMINT_N = "MEDIUMINT(n)";	    //24bit 整數(3 bytes, N -8,388,608~8,388,607)
    const CLOUMN_TYPE_INT_N = "INT(n)";				    //32bit 整數(4 bytes, N -2,147,483,648~2,147,483,647)
    const CLOUMN_TYPE_BIGINT_N = "BIGINT(n)";			//64bit 整數(8 bytes, N -9,223,372,036,854,775,808~9,223,372,036,854,775,807)
    const CLOUMN_TYPE_DATE = "DATE";				    //2016-01-01
    const CLOUMN_TYPE_TIME = "TIME";				    //23:59:59
    const CLOUMN_TYPE_DATETIME = "DATETIME";		    //2016-01-01 23:59:59 存多少顯示多少，Update手動更新
    const CLOUMN_TYPE_TIMESTAMP = "TIMESTAMP";		    //2016-01-01 23:59:59 會依照時區變化，Update自動更新
    const CLOUMN_TYPE_CHAR_N = "CHAR(n)";			    //固定長度字串，N 最長 255 字元個數
    const CLOUMN_TYPE_VARCHAR_N = "VARCHAR(n)";		    //可變長度字串，N < 65,535 字元個數
    const CLOUMN_TYPE_TEXT = "TEXT";				    //可變長度字串，最長 65,535 字元個數
    private $column_types;
    private $column_query_array = array();
    private static $instance;

    public static function get_instance():AP_DataBaseTableInit {
        return self::$instance = self::$instance ? self::$instance : new AP_DataBaseTableInit;
    }

    public function __construct() {
        $this->column_types = (new ReflectionClass(__CLASS__))->getConstants();
    }

    public function addAIPrimaryUnsignedID(string $col_name){
        if(!AP_Validator::test_variableName($col_name)) return 1;
        AP_Array::push($this->column_query_array, " `{$col_name}` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY");
        return $this;
    }

    public function addColumn(string $col_name, string $col_type, int $col_length = null, bool $unsigned = true) {
        if(!AP_Array::inArray($this->column_types, $col_type)) return 1;
        if(!AP_Validator::test_variableName($col_name)) return 2;
        if(!AP_Validator::test_uint($col_name) && $col_length < 1) return 3;
        if($col_type == self::CLOUMN_TYPE_CHAR_N && $col_length == null) return 4;
        if($col_type == self::CLOUMN_TYPE_VARCHAR_N && $col_length == null) return 5;
        if($col_type == self::CLOUMN_TYPE_TINYINT_N && $col_length == null) $col_length = 4;
        if($col_type == self::CLOUMN_TYPE_SMALLINT_N && $col_length == null) $col_length = 6;
        if($col_type == self::CLOUMN_TYPE_MEDIUMINT_N && $col_length == null) $col_length = 9;
        if($col_type == self::CLOUMN_TYPE_INT_N && $col_length == null) $col_length = 11;
        if($col_type == self::CLOUMN_TYPE_BIGINT_N && $col_length == null) $col_length = 20;
        if($col_type == self::CLOUMN_TYPE_CHAR_N) $unsigned = false;
        if($col_type == self::CLOUMN_TYPE_VARCHAR_N) $unsigned = false;
        if(AP_String::lastIndexOf($col_type, '(n)') != -1)
        {
            $col_type = AP_String::replace($col_type, '/\(n\)/', "({$col_length})");
            AP_Array::push($this->column_query_array, " `{$col_name}` {$col_type} ".(($unsigned)?'UNSIGNED':''));
        }
        else
        {
            AP_Array::push($this->column_query_array, " `{$col_name}` {$col_type} ");
        }
        return $this;
    }

    public function get_part_query_string():string {
        $return_str = AP_Array::join($this->column_query_array,',');
        $this->column_query_array = array();
        return $return_str;
    }
}

class AP_DataBaseSQLWhere extends AP_Object
{
    const WHERE_EQ_K_V = ' {k} = {v} ';
    const WHERE_NE_K_V = ' {k} != {v} ';
    const WHERE_GT_K_V = ' {k} > {v} ';
    const WHERE_GE_K_V = ' {k} >= {v} ';
    const WHERE_LT_K_V = ' {k} < {v} ';
    const WHERE_LE_K_V = ' {k} <= {v} ';
    const WHERE_IN_K_VL = ' {k} IN ({vlist}) ';
    const WHERE_NOT_IN_K_VL = ' {k} NOT IN ({vlist}) ';
    const WHERE_LIKE_K_V = ' {k} LIKE {v} ';
    const WHERE_NOT_LIKE_K_V = ' {k} NOT LIKE {v} ';
    const LOGIC_AND = ' AND ';
    const LOGIC_OR = ' OR ';
    const ORDER_BY_ASC_KL = ' ORDER BY {klist} ASC ';
    const ORDER_BY_DESC_KL = ' ORDER BY {klist} DESC ';
    const LIMIT_S_C = ' LIMIT {start}, {count} ';
    const GROUP_START = ' ( ';
    const GROUP_END = ' ) ';

    private $stmt_where_types = array();
    private $stmt_logic_types = array();
    private $stmt_order_types = array();
    private $stmt_query_strs_array = array();
    private $stmt_query_vals_array = array();
    private static $instance;

    public static function get_instance():AP_DataBaseSQLWhere {
        return self::$instance = self::$instance ? self::$instance : new AP_DataBaseSQLWhere;
    }

    public function __construct() {
        $stmt_types = (new ReflectionClass(__CLASS__))->getConstants();
        foreach ($stmt_types as $k => $v)
            if(AP_String::indexOf($k, 'WHERE') != -1)
                $this->stmt_where_types[$k] = $v;
        foreach ($stmt_types as $k => $v)
            if(AP_String::indexOf($k, 'LOGIC') != -1)
                $this->stmt_logic_types[$k] = $v;
        foreach ($stmt_types as $k => $v)
            if(AP_String::indexOf($k, 'ORDER_BY') != -1)
                $this->stmt_order_types[$k] = $v;
        return $this;
    }

    public function add_where(string $where_type, array $params, string $tb_name=null) {
        if(!AP_Array::inArray($this->stmt_where_types, $where_type)) return 1;
        foreach ($params as $k => $v)
        {
            if(!AP_Validator::test_variableName($k)) return 2;
            if(ap_fun_isArray($v)){
                $where_type = AP_String::replace($where_type, array('/\{k\}/','/\{vlist\}/'), array($tb_name?"`{$tb_name}`.`{$k}`":"`$k`",AP_Array::join(array_fill(0, count($v), '?'),',')));
                $this->stmt_query_vals_array = AP_Array::concat(self::$stmt_query_vals_array, $v);
            }else{
                $where_type = AP_String::replace($where_type, array('/\{k\}/','/\{v\}/'), array($tb_name?"`{$tb_name}`.`{$k}`":"`$k`",'?'));
                AP_Array::push($this->stmt_query_vals_array, $v);
            }
            AP_Array::push($this->stmt_query_strs_array, $where_type);
            break;
        }
        return $this;
    }

    public function add_logic(string $logic_type) {
        if(!AP_Array::inArray($this->stmt_logic_types, $logic_type)) return 1;
        AP_Array::push($this->stmt_query_strs_array, $logic_type);
        return $this;
    }

    public function add_order_by(string $order_type, array $params) {
        if(!AP_Array::inArray(self::$stmt_order_types, $order_type)) return 1;
        foreach($params as $k => $v) $params[$k] = "`$v`";
        $order_type = AP_String::replace($order_type, '/\{klist\}/', AP_Array::join($params,','));
        AP_Array::push($this->stmt_query_strs_array, $order_type);
        return $this;
    }

    public function add_limit(int $start=0, int $count=100) {
        $limit_type = AP_String::replace(self::LIMIT_S_C, array('/\{start\}/','/\{count\}/'), array($start,$count));
        AP_Array::push($this->stmt_query_strs_array, $limit_type);
        return $this;
    }

    public function add_group_start() {
        AP_Array::push($this->stmt_query_strs_array, self::GROUP_START);
        return $this;
    }

    public function add_group_end() {
        AP_Array::push($this->stmt_query_strs_array, self::GROUP_END);
        return $this;
    }

    public function get_part_query_info():array {
        $stmt = AP_Array::join($this->stmt_query_strs_array,'');
        $execute = AP_Array::concat($this->stmt_query_vals_array);
        $this->stmt_query_strs_array = array();
        $this->stmt_query_vals_array = array();
        return array('stmt'=>$stmt,'execute'=>$execute);
    }
}

class AP_DataBase extends AP_Object
{
    private $setting_host;
    private $setting_account;
    private $setting_password;
    private $setting_charset;
    private $last_insert_id;
    private static $instance;

    public static function get_instance(string $host, string $account, string $password, string $charset='utf8mb4'):AP_DataBase {
        if(!AP_Validator::test_variableName($host)) return null;
        if(!AP_Validator::test_variableName($account)) return null;
        if(!(AP_Validator::test_password($password))) return null;
        if(!AP_Validator::test_variableName($charset)) return null;
        return self::$instance = self::$instance ? self::$instance : new AP_DataBase($host, $account, $password, $charset);
    }

    public function __construct(string $host, string $account, string $password, string $charset='utf8mb4') {
        $this->setting_host = $host;
        $this->setting_account = $account;
        $this->setting_password = $password;
        $this->setting_charset = $charset;
    }

    private function get_pdo(string $db_name = null):PDO {
        if(!is_null($db_name))
            return new PDO('mysql:host='.$this->setting_host.';dbname='.$db_name.';charset='.$this->setting_charset, $this->setting_account, $this->setting_password);
        else
            return new PDO('mysql:host='.$this->setting_host.';charset='.$this->setting_charset, $this->setting_account, $this->setting_password);
    }

    public function create_db(string $db_name):int {
        if(!AP_Validator::test_variableName($db_name)) return 1;
        $this->get_pdo()->exec('CREATE DATABASE '.$db_name.' DEFAULT CHARACTER SET '.$this->setting_charset.' COLLATE '.$this->setting_charset.'_general_ci;');
        return -1;
    }

    public function drop_db(string $db_name):int {
        if(!AP_Validator::test_variableName($db_name)) return 1;
        $this->get_pdo()->exec("DROP DATABASE `{$db_name}`;");
        return -1;
    }

    public function create_table(string $db_name, string $tb_name, AP_DataBaseTableInit $config):int {
        if(!AP_Validator::test_variableName($db_name)) return 1;
        if(!AP_Validator::test_variableName($tb_name)) return 2;
        $this->get_pdo($db_name)->exec('CREATE TABLE `'.$tb_name.'` ('.$config->get_part_query_string().');');
        return -1;
    }

    public function drop_table(string $db_name, string $tb_name):int {
        if(!AP_Validator::test_variableName($db_name)) return 1;
        if(!AP_Validator::test_variableName($tb_name)) return 2;
        $this->get_pdo($db_name)->exec("DROP TABLE `{$tb_name}`;");
        return -1;
    }

    public function insert(string $db_name, string $tb_name, array $key_val_arr):int {
        if(!AP_Validator::test_variableName($db_name)) return 1;
        if(!AP_Validator::test_variableName($tb_name)) return 2;
        $pdo = $this->get_pdo($db_name);
        $stmt = $pdo->query("DESCRIBE `{$tb_name}`;");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $input_keys = array_keys($key_val_arr);
        $input_values = array_values($key_val_arr);
        foreach ($input_keys as $v) if(!AP_Array::inArray($columns, $v)) return 3; // check all input key in columns
        $stmt = $pdo->prepare('INSERT INTO '.$tb_name.'('.AP_Array::join($input_keys,',').') VALUES('.AP_Array::join(array_fill(0, count($input_keys), '?'),',').')');
        if(!$stmt->execute($input_values)) return 4; // query fail
        $this->last_insert_id = $pdo->lastInsertId();
        return -1;
    }

    public function delete(string $db_name, string $tb_name, AP_DataBaseSQLWhere $where):int {
        if(!AP_Validator::test_variableName($db_name)) return 1;
        if(!AP_Validator::test_variableName($tb_name)) return 2;
        $info = $where->get_part_query_info();
        if(!($this->get_pdo($db_name)->prepare("DELETE FROM {$tb_name} WHERE {$info['stmt']}"))->execute($info['execute'])) return 3;
        return -1;
    }

    public function update(string $db_name, string $tb_name, array $key_val_arr, AP_DataBaseSQLWhere $where):int {
        if(!AP_Validator::test_variableName($db_name)) return 1;
        if(!AP_Validator::test_variableName($tb_name)) return 2;
        $info = $where->get_part_query_info();
        $pdo = $this->get_pdo($db_name);
        $stmt = $pdo->query("DESCRIBE `{$tb_name}`;");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $input_keys = array_keys($key_val_arr);
        $input_values = array_values($key_val_arr);
        foreach ($input_keys as $v) if(!AP_Array::inArray($columns, $v)) return 3; // check all input key in columns
        $params_array = array();
        foreach ($input_keys as $v) AP_Array::push($params_array,' `'.$v.'`=? ');
        $stmt = $pdo->prepare('UPDATE `'.$tb_name.'` SET '.AP_Array::join($params_array,',').' WHERE '.$info['stmt']);
        $input_values = AP_Array::concat($input_values, $info['execute']);
        if(!$stmt->execute($input_values)) return 4; // query fail
        return -1;
    }

    public function select(string $db_name, string $tb_name, AP_DataBaseSQLWhere $where) {
        if(!AP_Validator::test_variableName($db_name)) return 1;
        if(!AP_Validator::test_variableName($tb_name)) return 2;
        $info = $where->get_part_query_info();
        $stmt = ($this->get_pdo($db_name)->prepare("SELECT * FROM `{$tb_name}` WHERE {$info['stmt']}"));
        if(!$stmt->execute($info['execute'])) return 3;
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function select_join(string $db_name, string $tb_name, string $tb_join_name, string $tb_id, string $tb_join_id, AP_DataBaseSQLWhere $where) {
        if(!AP_Validator::test_variableName($db_name)) return 2;
        if(!AP_Validator::test_variableName($tb_name)) return 3;
        if(!AP_Validator::test_variableName($tb_join_name)) return 4;
        if(!AP_Validator::test_variableName($tb_id)) return 5;
        if(!AP_Validator::test_variableName($tb_join_id)) return 6;
        $info = $where->get_part_query_info();
        $pdo = $this->get_pdo($db_name);
        $columns_A = $pdo->query("DESCRIBE `{$tb_name}`;")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($columns_A as $k => $v) $columns_A[$k] = "`{$tb_name}`.`{$v}` AS {$tb_name}_{$v}";
        $columns_B = $pdo->query("DESCRIBE `{$tb_join_name}`;")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($columns_B as $k => $v) $columns_B[$k] = "`{$tb_join_name}`.`{$v}` AS {$tb_join_name}_{$v}";
        $select = AP_Array::join(AP_Array::concat($columns_A,$columns_B),',');
        $stmt = $pdo->prepare("SELECT {$select} FROM `{$tb_name}` LEFT JOIN `{$tb_join_name}` ON `{$tb_name}`.`{$tb_id}`=`{$tb_join_name}`.`{$tb_join_id}` WHERE {$info['stmt']}");
        if(!$stmt->execute($info['execute'])) return 8;
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_last_insert_id():int {
        return $this->last_insert_id;
    }

}