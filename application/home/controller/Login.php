<?php
/**
 * 跟团游
 */
namespace app\home\controller;
use app\common\controller\HomeBase;
//use think\Request;
//use \think\Validate;
class Login extends HomeBase
{   
    // 后台登录
    public function login(){
        $account = input('account');
        $password = input('password');
        $remember = input('remember');
       
        if($account == '' || $account == null || $password == '' || $password == null){
            return json(array('code' => 404,'msg' => '账号或密码不能为空'));
        }
        
        $data = db('sp')->where(array('account_num' => $account,'pwd' => md5($password)))->find();
        if(empty($data)){
            return json(array('code' => 404,'msg' => '账号或密码不能为空'));
        }
        
        

        return json(array('code' => 202,'msg' => ));
    }
}

 