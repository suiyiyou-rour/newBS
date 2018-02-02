<?php
namespace app\common\controller;
/**
 * home模块基类
 */
class HomeBase extends Base
{
    public function __construct()
    {
        parent::__construct();
        if(!IS_WIN){
            header('Access-Control-Allow-Origin:*');
            header("Access-Control-Allow-Methods", "GET,POST");
            header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
        }
        $sp = array("id" => '1',"code" => "1234567");
        session("sp",$sp);
    }
}
