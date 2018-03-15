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
        // 权限控制
        $controller = strtolower( request()->controller() );

        $t = get_class_vars($controller);
        dump($t);die;
        $sp = session('sp');
        if(!empty($sp)){
            if( $controller == 'login' ) return false;
            
            $auth =  \think\Loader::model('Auth','logic');
            $res = $auth->checkAuth($sp['code'],$controller);

            if(!$res) return false;
        }

    }
}
