<?php
namespace app\home\logic;
use think\Request;

class AddGroup
{
    /**
     * 状态分发
     */
    public function dispatcher($state){
        switch ($state) {
            case '0':
                //基本信息添加
                return $this->basicInfo();
            case '1':
                //行程信息添加
                return $this->routeInfo();
            case '2':
                //产品特色添加
                return $this->sellingPoint();
            case '3':
                //自费项目添加
                return $this->chargedItem();
            case '4':
                //费用包含添加
                return $this->includeCost();
            case '5':
                //费用不包含添加
                return $this->notInCost();
            case '6':
                //特殊人群限制添加
                return $this->specialPeople();
            case '7':
                //预定须知添加
                return $this->advanceKnow();
            default:
                return json_encode(array("code" => 404,"msg" => "参数错误"));
        }
    }

    //基本信息添加
    public function basicInfo()
    {
        $hash = input('post.hash');
        if(!checkFromHash($hash)){
            return json_encode(array("code" => 405,"msg" => "您表单提交速度过快，请3秒后重试。"));
        }
        //数据验证
        $data = $this->basicInfoData();
        $validate = new \app\home\validate\Group();
        $result = $validate->scene('addBasicInfo')->check($data);
        if(true !== $result){
            // 验证失败 输出错误信息
            return json_encode(array("code" => 405,"msg" => $validate->getError()));
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
        db('goods_create')->insert(array('goods_code' => $goodsCode,"tab" => 0));
        if($goodsRes && $groupRes && $supplyRes){
            return json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode,"tab" => 1)));
        }else {
            return json_encode(array("code" => 403,"msg" => "数据保存出错，请再试一次"));
        }

//        echo json_encode(array("code" => 200,"msg" => $data));
    }

    //行程信息添加
    public function routeInfo()
    {
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "添加商品，商品号不能为空"));
        }
        //数据验证
        $data = $this->routeInfoData();
//        $data = testGroupPage1();//测试参数
        $validate = new \app\home\validate\Group();
        $result = $validate->scene('addRouteInfo')->check($data);
        if(true !== $result){
            // 验证失败 输出错误信息
            return json_encode(array("code" => 405,"msg" => $validate->getError()));
        }
        $groupRes = db('goods_group')->where(array("code" => $goodsCode))->update($data);
        db('goods_create')->where(array("goods_code" => $goodsCode))->update(array("tab" => 1));
        if($groupRes){
            return json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode,"tab" => 2)));
        }else {
            return json_encode(array("code" => 403,"msg" => "数据保存出错，请再试一次"));
        }

    }

    //产品特色添加
    public function sellingPoint()
    {
        return "sellingPoint";
    }

    //自费项目添加
    public function chargedItem()
    {
        return "chargedItem";
    }

    //费用包含添加
    public function includeCost()
    {
        return "includeCost";
    }

    //费用不包含添加
    public function notInCost()
    {
        return "notInCost";
    }

    //特殊人群限制添加
    public function specialPeople()
    {
        return "specialPeople";
    }

    //预定须知添加
    public function advanceKnow()
    {
        return "advanceKnow";
    }

    //基本信息数据接收
    private function basicInfoData (){
        $gain = ['contact_code', 'inside_code', 'inside_title', 'subtitle', 'service_type', 'line_type', 'play_type', 'begin_address', 'end_address', 'main_place', 'advance_time', 'online_type', 'on_time', 'off_time' , 'service_tel', 'refund_type', 'refund_info', 'rate'];
        $data = Request::instance()->only($gain,'post');//        $data = input('post.');
//        $data = testGroupPage0();//测试参数
        if(empty($data["service_type"])){
            $data["service_type"]      =   ""; //服务保障      （副）
        }
        if(empty($data["service_type"])){
            $data["main_place"]      =   ""; //主要景点      （副）必须
        }
        if(empty($data["service_type"])){
            $data["service_tel"]      =   ""; //客服电话      （副）
        }
        if(empty($data["service_type"])){
            $data["refund_info"]      =   ""; //梯度详细退款      （副）
        }
        $data["service_type"]      =   json_encode($data["service_type"]); //服务保障      （副）
        $data["main_place"]        =   json_encode($data["main_place"]); //主要景点     （副）必须
        $data["service_tel"]       =   json_encode($data["service_tel"]); //客服电话     （副）
        $data["refund_info"]       =   json_encode($data["refund_info"]);//梯度详细退款     （副）
        return $data;
    }

    //行程信息数据接收
    private function routeInfoData(){
        $gain = ['play_day','go_trans','back_trans','go_trans_cost','back_trans_cost','gather_place','route_info'];
        $data = Request::instance()->only($gain,'post');//        $data = input('post.');+
        if(empty($data["gather_place"])){
            $data["gather_place"]  = "";
        }
        if(empty($data["gather_place"])){
            $data["route_info"]  = "";
        }
        $data["gather_place"]      =   json_encode($data["gather_place"]); //集合地点
        $data["route_info"]        =   json_encode($data["route_info"]); //行程详细
        return $data;
    }


}