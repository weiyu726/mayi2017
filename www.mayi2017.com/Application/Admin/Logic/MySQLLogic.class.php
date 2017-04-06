<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/6 0006
 * Time: 下午 8:05
 */

namespace Admin\Logic;





class MySQLLogic implements DbMysql{

    public function connect()
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function disconnect()
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function free($result)
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    /**
     * 执行一个简单的SQL
     * 待确认 是否是写语句
     * @param string $sql
     * @param array $args
     * @return false|int|mixed
     */
    public function query($sql, array $args = array())
    {


        //有可能是查询语句 如果是就输出
        if(strpos('SELECT',$sql) !== false){
            echo __METHOD__;
            dump(func_get_args());
            echo '<hr />';
        }

        //获取所有实参

        $args =func_get_args();
        //获取SQL语句
        $sql = array_shift($args);
        $params = preg_split('/\?[FTN]/',$sql);
        $sql = '';
        //删除最后一个空字符串
        array_pop($params);
        //拼接SQL 语句
        foreach($params as $key=>$value){
            $sql.=$value.$args[$key];
        }
        //返回一个二维数组
        return M()->execute($sql);
    }

    public function insert($sql, array $args = array())
    {
        //获取所有实参
        $args =func_get_args();
        $sql = $args[0];
        $table_name = $args[1];
        $params =$args[2];
        $sql = str_replace('?T',$table_name,$sql);
        $tmp = [];
        foreach($params as $key=>$value){
            $tmp[]= $key.'='.'"'.$value.'"';
        }
        $sql =str_replace('?%', implode(',',$tmp),$sql);
        if(M()->execute($sql) !== false){
            return M()->getLastInsID();
        }else{
            return false;
        }

    }

    public function update($sql, array $args = array())
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function getAll($sql, array $args = array())
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function getAssoc($sql, array $args = array())
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    /**
     * @param string $sql
     * @param array $args
     * @return array|null
     * 获取一行记录
     */
    public function getRow($sql, array $args = array())
    {

        //获取所有实参
        $args =func_get_args();
        //获取SQL语句
        $sql = array_shift($args);
        $params = preg_split('/\?[FTN]/',$sql);
        $sql = '';
        //删除最后一个空字符串
        array_pop($params);
        //拼接SQL 语句
        foreach($params as $key=>$value){
            $sql.=$value.$args[$key];
        }
        //返回一个二维数组
        $rows = M()->query($sql);
        //只要第一行
        return array_shift($rows);

    }

    public function getCol($sql, array $args = array())
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    /**
     * 获取第一行第一个字段值
     * @param string $sql
     * @param array $args
     * @return mixed
     */
    public function getOne($sql, array $args = array())
    {
        //获取所有实参
        $args =func_get_args();
        //获取SQL语句
        $sql = array_shift($args);
        $params = preg_split('/\?[FTN]/',$sql);
        $sql = '';
        //删除最后一个空字符串
        array_pop($params);
        //拼接SQL 语句
        foreach($params as $key=>$value){
            $sql.=$value.$args[$key];
        }
        //返回一个二维数组
        $rows = M()->query($sql);
        //只要第一行
        $row = array_shift($rows);
        //只要第一行第一个值
         return array_shift($row);

    }

}