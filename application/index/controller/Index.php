<?php
namespace app\index\controller;
use app\common;
class Index extends common\controller\Base
{
    public function index()
    {
//        $res =
//        var_dump($res);
//        echo onload();
//        Loader::import('first.second.Foo');
//        $foo = new \Foo();
//        echo 1;
//        $res = Db::query('select * from lmm_use');
//        var_dump($res);


    }

    public function home(){
        echo "欢迎来到KK";
    }

    public function demo(){

    }

    /**
     * 第三方类
     */
    public function classCheck(){
        $foo = new \second\Foo();
        echo $foo->index();
    }

    /**
     * api调用测试
     */
    public function apiClassCheck(){
       $res = new \app\api\controller\Api();
        echo $res->index();
    }

    /**
     * tp3.2 字母方法
     */
    public function letterCheck(){
        //C方法
        echo config("syy");
        //I方法
        //检查变量 input('?post.name');
        //var_dump(input('?get.id'));

        //获取GET、POST或者PUT  input('param.name');
        //input('post.name');/////

        //S方法
        // $value = '文件缓存测试';
        // cache('name', $value, 3600);
        // echo cache('name');
        //$cache = new think\Cache();

        //M方法
//        $res = db('lmm_user')->where('id',1)->find();
//        var_dump($res);
    }

}
