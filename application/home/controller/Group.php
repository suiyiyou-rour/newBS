<?php
/**
 * 跟团游
 */
namespace app\home\controller;
use app\common;
use think\Request;
//use \think\Validate;
class Group extends common\controller\HomeBase
{
    public function __construct()
    {
        parent::__construct();
        if(!IS_WIN){
            header('Access-Control-Allow-Origin:*');
            header("Access-Control-Allow-Methods", "GET,POST");
            header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
        }
    }

    public function index()
    {

    }

    //显示合同
    public function showPact(){
        $res = db('contact')->field('code,name,rate')->where(array('sp_code' => 1))->select();
        if(!$res){
            echo json_encode(array("code" => 304,"msg" => "合同加载错误"));
            return ;
        }
        echo json_encode(array("code" => 200,"data" => $res));
    }

    //添加产品
    public function addgoods()
    {
        $state = input('state');
        if(!$state){
//            $state = 0;
            return;
        }
        switch ($state) {
            case 0:
                $this->addGetCode();
                break;
            case 1:
                //基本信息添加
                $this->addBasicInfo();
                break;
            case 2:
                break;
            case 3:
                break;
            case 4:
                break;
            case 5:
                break;
            case 6:
                break;
            case 7:
                break;
            case 8:
                break;
            default:
                echo 1;
        }
    }

    //获取新添加的产品编号
    private function addGetCode()
    {
        $data = getGoodsCode();
        echo json_encode(array("code" => 200,"data" => $data));
        return ;
    }

    //基本信息添加
    private function addBasicInfo()
    {
        $gain = [
            'contact_code',
            'inside_code',
            'inside_title',
            'subtitle',
            'service_type',
            'line_type',
            'play_type',
            'begin_address',
            'end_address',
            'main_place',
            'advance_time',
            'online_type',
            'on_time',
            'off_time' ,
            'service_tel',
            'refund_type',
            'refund_info',
            'rate'];

        $data = Request::instance()->only($gain,'post');
//        $data = input('post.');

        $result = $this->validate($data,'Group.addBasicInfo');
        if(true !== $result){
            // 验证失败 输出错误信息
            echo json_encode(array("code" => 403,"msg" => $result));
            return;
        }
        echo json_encode(array("code" => 200,"msg" => $result));
    }

    //行程信息添加
    private function addRouteInfo()
    {

    }

    //产品特色添加
    private function addSellingPoint()
    {

    }

    //自费项目添加
    private function addChargedItem()
    {

    }

    //费用包含添加
    private function addIncludeCost()
    {

    }

    //费用不包含添加
    private function addNotInCost()
    {

    }

    //特殊人群限制添加
    private function addSpecialPeople()
    {

    }

    //预定须知添加
    private function addadvanceKnow()
    {

    }


}
