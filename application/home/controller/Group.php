<?php
/**
 * 跟团游
 */
namespace app\home\controller;
use app\common;
use think\Request;
use think\Session;
//use \think\Validate;
class Group extends common\controller\HomeBase
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
//        echo getHash();
        var_dump(checkHash("927d13527349b5de1fbbacd57fa5b0e9"));
//        var_dump(cookie('hash')) ;
    }

    //显示合同
    public function showPact(){
        $res = db('contact')->field('code,name,rate')->where(array('sp_code' => 1))->select();
        if(!$res){
            echo json_encode(array("code" => 405,"msg" => "合同加载错误"));
            return ;
        }
        echo json_encode(array("code" => 200,"data" => $res));
    }

    //添加产品
    public function addgoods()
    {

        $state = input('state');
        if($state == null || $state == ""){
            echo json_encode(array("code" => 404,"msg" => "参数错误404"));
            return;
        }
        switch ($state) {
            case '0':
                //基本信息添加
                $this->addBasicInfo();
                break;
            case '1':
                break;
            case '2':
                break;
            case '3':
                break;
            case '4':
                break;
            case '5':
                break;
            case '6':
                break;
            case '7':
                break;
            default:
                echo json_encode(array("code" => 404,"msg" => "参数错误"));
        }
    }

    //显示商品数据（添加显示）
    public function showGoods(){
        $state = input('state');
        if($state == null || $state == ""){
            echo json_encode(array("code" => 404,"msg" => "参数错误404"));
            return;
        }
        switch ($state) {
            case '0':
                $this->showBasicInfo();
                break;
            case '1':
                break;
            case '2':
                break;
            case '3':
                break;
            case '4':
                break;
            case '5':
                break;
            case '6':
                break;
            case '7':
                break;
            default:
                echo json_encode(array("code" => 404,"msg" => "参数错误"));
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
        //数据验证
        $gain = ['contact_code', 'inside_code', 'inside_title', 'subtitle', 'service_type', 'line_type', 'play_type', 'begin_address', 'end_address', 'main_place', 'advance_time', 'online_type', 'on_time', 'off_time' , 'service_tel', 'refund_type', 'refund_info', 'rate'];
        $data = Request::instance()->only($gain,'post');//        $data = input('post.');
        $data["service_type"]      =   json_encode($data["service_type"]); //服务保障      （副）
        $data["main_place"]        =   json_encode($data["main_place"]); //主要景点     （副）必须
        $data["service_tel"]       =   json_encode($data["service_tel"]); //客服电话     （副）
        $data["refund_info"]       =   json_encode($data["refund_info"]);//梯度详细退款     （副）
//        $data = testGroupPage1();//测试参数
        $result = $this->validate($data,'Group.addBasicInfo');
        if(true !== $result){
            // 验证失败 输出错误信息
            echo json_encode(array("code" => 405,"msg" => $result));
            return;
        }

        $goodsCode = createGoodsCode("g");//产品编号
        //主表添加数据
        $goodsData["code"]              =    $goodsCode;//产品编号
        $goodsData["sp_code"]           =    session("sp.code");//产品编号
        $goodsData["contact_code"]      =   $data["contact_code"]; //合同编码  （主）必须
        $goodsData["inside_code"]       =   $data["inside_code"]; //内部编号   （主）
        $goodsData["inside_title"]      =   $data["inside_title"]; //内部显示标题   （主）必须
        $goodsData["subtitle"]          =   $data["subtitle"]; //商品副标题     （主）
        $goodsData["advance_time"]      =   $data["advance_time"]; //提前预定时间     （主）必须
        $goodsData["online_type"]       =   $data["online_type"]; //上线类型   (主)必须
        $goodsData["on_time"]           =   $data["on_time"]; //上线时间     （主）
        $goodsData["off_time"]          =   $data["off_time"]; //下线时间     （主）
        $goodsData["rate"]              =   $data["rate"]; //产品费率     （主）必须

        //副表添加数据
        $groupData["goods_code"]        =   $goodsCode; //产品编号
        $groupData["service_type"]      =   $data["service_type"]; //服务保障      （副）
        $groupData["line_type"]         =   $data["line_type"]; //路线类型     （副）
        $groupData["play_type"]         =   $data["play_type"]; //游玩类型     （副）
        $groupData["begin_address"]     =   $data["begin_address"]; //出发地     （副）必须
        $groupData["end_address"]       =   $data["end_address"]; //目的地     （副）必须
        $groupData["main_place"]        =   $data["main_place"]; //主要景点     （副）必须
        $groupData["service_tel"]       =   $data["service_tel"]; //客服电话     （副）
        $groupData["refund_type"]       =   $data["refund_type"]; //退款类型     （副）必须
        $groupData["refund_info"]       =   $data["refund_info"];//梯度详细退款     （副）

        //补充表
        $supplyData["goods_code"]        =   $goodsCode; //产品编号

        $goodsRes = db('goods')->insert($goodsData);
        $groupRes = db('goods_group')->insert($groupData);
        $supplyRes = db('goods_supply')->insert($supplyData);
        if($goodsRes && $groupRes && $supplyRes){
            echo json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode)));
        }else {
            echo json_encode(array("code" => 403,"msg" => "数据保存出错，请再试一次"));
        }

//        echo json_encode(array("code" => 200,"msg" => $data));
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

    //显示首页
    private function showBasicInfo(){
        //
        $contact = db('contact')->field('code,name,rate')->where(array('sp_code' => 1))->select();
        if(!$contact){
            echo json_encode(array("code" => 405,"msg" => "合同加载错误"));
            return ;
        }
        $date["contact"] = $contact;
        $date["hash"] = getHash();
        echo json_encode(array("code" => 200,"data" => $date));
    }
}
