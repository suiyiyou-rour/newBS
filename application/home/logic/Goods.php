<?php
/**
 * 产品添加 状态分发
 */
namespace app\home\logic;
use think\Request;
use think\Validate;
class Goods
{

    public function __construct(){

    }

    public function index(){

 //        $gain = ['contact_code', 'inside_code', 'inside_title', 'subtitle', 'service_type', 'line_type', 'play_type', 'begin_address', 'end_address', 'main_place', 'advance_time', 'online_type', 'on_time', 'off_time' , 'service_tel', 'refund_type', 'refund_info', 'rate'];
//        $data = Request::instance()->only($gain,'post');//        $data = input('post.');
        $data = testGroupPage0();//测试参数
        $data["service_type"]      =   json_encode($data["service_type"]); //服务保障      （副）
        $data["main_place"]        =   json_encode($data["main_place"]); //主要景点     （副）必须
        $data["service_tel"]       =   json_encode($data["service_tel"]); //客服电话     （副）
        $data["refund_info"]       =   json_encode($data["refund_info"]);//梯度详细退款     （副）


        $validate = new \app\home\validate\Group();
        $result = $validate->scene('addBasicInfo')->check($data);

        if(true !== $result){
            // 验证失败 输出错误信息
            echo $validate->getError();
            return;
        }
    }
}