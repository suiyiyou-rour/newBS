<?php
/**
 * 跟团游
 */
namespace app\home\controller;
use app\common\controller\HomeBase;
use think\Request;
//use \think\Validate;
class Group extends HomeBase
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $goodsCode = "g001969474";
        $where = array(
            "code"          =>  $goodsCode,
            'is_del'      =>  ['<>',"1"]  //未删除
        );
        $res = db('goods')->field("check_type")->where($where)->find();
        if(!$res){
            echo 1;
            die;
        }
        if($res["check_type"] !== 0 && $res["check_type"] !== 1){
            echo 2;
            die;
        }
        var_dump($res["check_type"]);


    }

    //商品添加
    public function add(){
        $state = input('state');
        if($state == null || $state == ""){
            echo json_encode(array("code" => 404,"msg" => "参数错误404"));
            return;
        }
        $res =  \think\Loader::model('Goods','logic')->dispatcher("group","Add",$state);
        echo $res;
    }

    //商品显示
    public function show(){
        $state = input('state');
        if($state == null || $state == ""){
            echo json_encode(array("code" => 404,"msg" => "参数错误404"));
            return;
        }
        $res =  \think\Loader::model('Goods','logic')->dispatcher("group","Show",$state);
        echo $res;
    }

    //商品页面选择显示
    public function option(){
        $state = input('state');
        if($state == null || $state == ""){
            echo json_encode(array("code" => 404,"msg" => "参数错误404"));
            return;
        }
        $res =  \think\Loader::model('Goods','logic')->dispatcher("group","Option",$state);
        echo $res;
    }

    //商品删除
    public function del(){
        $goodsCode = "g001594519";
        if(empty($goodsCode)){
            echo json_encode(array("code" => 404,"msg" => "参数错误404"));
            return;
        }
        $where = [
            "code"    =>  $goodsCode,
            "sp_code"     =>  getSpCode(),         //todo 供应商code
        ];
        $res = db('goods')->field("check_type")->where($where)->find();
        if($res["check_type"] == 0){
            var_dump($res);
        }else if($res["check_type"] && $res["check_type"] != 0){

        }else{
            echo json_encode(array("code" => 405,"msg" => "请求错误，请联系管理员"));
            return;
        }

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
                //行程信息添加
                $this->addRouteInfo();
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
                $this->showRouteInfo();
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

    //显示选项
    public function showOption(){
        $state = input('state');
        if($state == null || $state == ""){
            echo json_encode(array("code" => 404,"msg" => "参数错误404"));
            return;
        }
        switch ($state) {
            case '0':
                $this->optionPact();
                break;
            case '1':
                $this->OptionBasicInfo();
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

    //基本信息添加
    private function addBasicInfo()
    {
        $hash = input('post.hash');
        if(!checkFromHash($hash)){
            echo json_encode(array("code" => 405,"msg" => "您表单提交速度过快，请3秒后重试。"));
            return;
        }
        //数据验证
        $gain = ['contact_code', 'inside_code', 'inside_title', 'subtitle', 'service_type', 'line_type', 'play_type', 'begin_address', 'end_address', 'main_place', 'advance_time', 'online_type', 'on_time', 'off_time' , 'service_tel', 'refund_type', 'refund_info', 'rate'];
        $data = Request::instance()->only($gain,'post');//        $data = input('post.');
//        $data = testGroupPage0();//测试参数
        $data["service_type"]      =   json_encode($data["service_type"]); //服务保障      （副）
        $data["main_place"]        =   json_encode($data["main_place"]); //主要景点     （副）必须
        $data["service_tel"]       =   json_encode($data["service_tel"]); //客服电话     （副）
        $data["refund_info"]       =   json_encode($data["refund_info"]);//梯度详细退款     （副）
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
        db('goods_create')->insert(array('goods_code' => $goodsCode,"tab" => 0));
        if($goodsRes && $groupRes && $supplyRes){
            echo json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode,"tab" => 1)));
        }else {
            echo json_encode(array("code" => 403,"msg" => "数据保存出错，请再试一次"));
        }

//        echo json_encode(array("code" => 200,"msg" => $data));
    }

    //行程信息添加
    private function addRouteInfo()
    {
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            echo json_encode(array("code" => 404,"msg" => "添加商品，商品号不能为空"));
            return ;
        }
        //数据验证
        $gain = ['play_day','go_trans','back_trans','go_trans_cost','back_trans_cost','gather_place','route_info'];
        $data = Request::instance()->only($gain,'post');//        $data = input('post.');
        $data["gather_place"]      =   json_encode($data["gather_place"]); //集合地点
        $data["route_info"]        =   json_encode($data["route_info"]); //行程详细
//        $data = testGroupPage1();//测试参数
        $result = $this->validate($data,'Group.addRouteInfo');
        if(true !== $result){
            // 验证失败 输出错误信息
            echo json_encode(array("code" => 405,"msg" => $result));
            return;
        }
        $groupRes = db('goods_group')->insert($data);
        db('goods_create')->insert(array('goods_code' => $goodsCode,"tab" => 1));
        if($groupRes){
            echo json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode,"tab" => 2)));
        }else {
            echo json_encode(array("code" => 403,"msg" => "数据保存出错，请再试一次"));
        }

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
        $goodsCode = input('post.goodsCode');
        if($goodsCode){//有商品code 查询
            $goodsField = "a.contact_code,a.inside_code,a.inside_title,a.subtitle,a.advance_time,a.online_type,a.on_time,a.off_time,a.rate";
            $groupField = "b.service_type,b.line_type,b.play_type,b.begin_address,b.end_address,b.main_place,b.service_tel,b.refund_type,b.refund_info";
            $allField = $goodsField.','.$groupField;
            $alias = array("syy_goods" => "a","syy_goods_group" => "b");
            $join = [['syy_goods_group','a.code = b.goods_code']];
            $where = array("a.code" => $goodsCode);
            $data = db('goods')->alias($alias)->join($join)->field($allField)->where($where)->find();
            if(!$data){
                echo json_encode(array("code" => 403,"msg" => "数据库查询出错吗，请联系管理员"));
                return;
            }
            $data["service_type"]      =   json_decode($data["service_type"]); //服务保障      （副）
            $data["main_place"]        =   json_decode($data["main_place"]); //主要景点     （副）必须
            $data["service_tel"]       =   json_decode($data["service_tel"]); //客服电话     （副）
            $data["refund_info"]       =   json_decode($data["refund_info"]);//梯度详细退款     （副）

            $data["state"] = '0';
            echo json_encode(array("code" => 200,"data" => $data));
            return;

        }else{//没有商品code
            //有未填完信息
            //没有未填完
        }

    }

    //显示行程信息
    private function showRouteInfo(){

    }





    //显示基本信息选项（合同）
    private function optionPact(){
        $contact = db('contact')->field('code,name,rate')->where(array('sp_code' => 1))->select();
        if(!$contact){
            echo json_encode(array("code" => 405,"msg" => "合同加载错误,请刷新页面"));
            return ;
        }
        $data["contact"] = $contact;
        $data["hash"] = getFromHash();
        echo json_encode(array("code" => 200,"data" => $data));
    }

    //显示行程信息选项
    private function optionBasicInfo(){
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            echo json_encode(array("code" => 404,"msg" => "查询商品号不能为空"));
            return ;
        }
        $address = db('goods_group')->field('begin_address,main_place')->where(array('goods_code' => $goodsCode))->find();
        if(!$address){
            echo json_encode(array("code" => 405,"msg" => "查询错误,请刷新页面"));
            return ;
        }
        $address["main_place"] = json_decode($address["main_place"]);
        echo json_encode(array("code" => 200,"data" => $address));
    }
}
