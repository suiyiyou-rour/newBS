<?php
namespace app\home\logic;
use think\Request;

class AddGroup
{
    /**
     * 状态分发
     */
    public function dispatcher($state){
        //需要商品code
        if($state != '0'){
            $goodsCode = input('post.goodsCode');
            if(empty($goodsCode)){
                return json_encode(array("code" => 404,"msg" => "添加商品，商品号不能为空"));
            }
            //是否有写入状态检测
            $res = $this->checkGoodsType($goodsCode);
            if($res !== true){
                return json_encode(array("code" => 405,"msg" => $res));
            }
        }
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
            case '11':
                //价格库存
                return $this->advanceKnow();
            case '100':
                //预定须知添加
                return $this->imageUpload();
            case '101':
                //预定须知添加
                return $this->imageDel();
            default:
                return json_encode(array("code" => 404,"msg" => "参数错误"));
        }
    }


    /**
     * todo
     * todo tab规则  0  1  2  3  4  5  6  7
     * todo 必须     1  1  1  0  1  0  0  1
     * todo 页码写入 0  1  3     6        7
     */

    //基本信息添加 0
    public function basicInfo()
    {
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
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
            if($goodsRes && $groupRes && $supplyRes){
                db('goods_create')->insert(array('goods_code' => $goodsCode));
                return json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode)));
            }else {
                return json_encode(array("code" => 403,"msg" => "数据保存出错，请再试一次"));
            }
        }else{
            $data = $this->basicInfoData();
            $validate = new \app\home\validate\Group();
            $result = $validate->scene('addBasicInfo')->check($data);
            if(true !== $result){
                // 验证失败 输出错误信息
                return json_encode(array("code" => 405,"msg" => $validate->getError()));
            }
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
            $groupData["service_type"]      =   $data["service_type"]; //服务保障      （副）
            $groupData["line_type"]         =   $data["line_type"]; //路线类型     （副）
            $groupData["play_type"]         =   $data["play_type"]; //游玩类型     （副）
            $groupData["begin_address"]     =   $data["begin_address"]; //出发地     （副）必须
            $groupData["end_address"]       =   $data["end_address"]; //目的地     （副）必须
            $groupData["main_place"]        =   $data["main_place"]; //主要景点     （副）必须
            $groupData["service_tel"]       =   $data["service_tel"]; //客服电话     （副）
            $groupData["refund_type"]       =   $data["refund_type"]; //退款类型     （副）必须
            $groupData["refund_info"]       =   $data["refund_info"];//梯度详细退款     （副）
            $goodsRes  =  db('goods')->where(array("code" => $goodsCode))->update($goodsData);
            $groupRes  =  db('goods_group')->where(array("goods_code" => $goodsCode))->update($groupData);
            if($goodsRes === false){
                return json_encode(array("code" => 403,"msg" => "保存出错，请稍后再试"));
            }
            if($groupRes === false){
                return json_encode(array("code" => 403,"msg" => "保存错误，请稍后再试"));
            }
            return json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode)));
        }


//        echo json_encode(array("code" => 200,"msg" => $data));
    }

    //行程信息添加 1
    public function routeInfo()
    {
        $goodsCode = input('post.goodsCode');
        //数据验证
        $data = $this->routeInfoData();
//        $data = testGroupPage1();//测试参数
        $validate = new \app\home\validate\Group();
        $result = $validate->scene('addRouteInfo')->check($data);
        if(true !== $result){
            // 验证失败 输出错误信息
            return json_encode(array("code" => 405,"msg" => $validate->getError()));
        }
        $groupRes = db('goods_group')->where(array("goods_code" => $goodsCode))->update($data);
        if($groupRes === false){
            return json_encode(array("code" => 403,"msg" => "数据保存出错，请再试一次"));
        }
        $tab = $this->getGoodsTab($goodsCode);
        if($tab < 1){
            db('goods_create')->where(array("goods_code" => $goodsCode))->update(array("tab" => 1));
        }
        return json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode)));

    }

    //产品特色添加 2
    public function sellingPoint()
    {
        $goodsCode = input('post.goodsCode');

        $data       = input('post.');
        //图片数组
        if(empty($data["fileList"]) || empty($data["feature_reasons"])){
            return json_encode(array("code" => 404,"msg" => "上传参数错误1"));
        }
        $fileList = objSetArray($data["fileList"]);
        if(empty($fileList[0]["name"])){
            return json_encode(array("code" => 404,"msg" => "上传参数错误2"));
        }
        $imageArray = array();
        foreach ($fileList as $k){
            $imageArray[] = $k["name"];
        }

        //首图
        $goodsData["head_img"] = $fileList[0]["name"];
        //图片数组
        $supplyData["image"] = json_encode($imageArray);
        //推荐理由 feature_reasons
        $groupData["feature_reasons"] = json_encode($data["feature_reasons"]);
        $dd["head_img"] = $fileList[0]["name"];
        $dd["image"] = json_encode($imageArray);
        $dd["feature_reasons"] = json_encode($data["feature_reasons"]);

        $goodsRes  =  db('goods')->where(array("code" => $goodsCode))->update($goodsData);
        $supplyRes =  db('goods_supply')->where(array("goods_code" => $goodsCode))->update($supplyData);
        $groupRes  =  db('goods_group')->where(array("goods_code" => $goodsCode))->update($groupData);
//        db('goods_create')->where(array("goods_code" => $goodsCode))->update(array("tab" => 3));//跳页
        if($goodsRes === false){
            return json_encode(array("code" => 403,"msg" => "首图保存出错，请稍后再试"));
        }
        if($supplyRes === false){
            return json_encode(array("code" => 403,"msg" => "图片保存错误，请稍后再试"));
        }
        if($groupRes === false){
            return json_encode(array("code" => 403,"msg" => "推荐理由保存错误，请稍后再试"));
        }
        $tab = $this->getGoodsTab($goodsCode);
        if($tab < 3){
            db('goods_create')->where(array("goods_code" => $goodsCode))->update(array("tab" => 3));
        }
        return json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode)));


    }

    //自费项目添加 3
    public function chargedItem()
    {
        $goodsCode = input('post.goodsCode');

        $data = input('post.');
        $groupData["charged_item"] = $data["charged_item"];
        if(empty($groupData["charged_item"])){
            $groupData["charged_item"]  = "";
        }

        $groupData["charged_item"]      =   json_encode($groupData["charged_item"]); //自费项目
        $res = db('goods_group')->where(array("goods_code" => $goodsCode))->update($groupData);
        if(!$res){
            return json_encode(array("code" => 403,"msg" => "推荐理由保存错误，请稍后再试"));
        }
        return json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode)));
    }

    //费用包含添加 4
    public function includeCost()
    {
        $goodsCode = input('post.goodsCode');

        //数据验证
        $data = $this->includeCostData();
//        $validate = new \app\home\validate\Group();
//        $result = $validate->scene('addIncludeCost')->check($data);
//        if(true !== $result){
//            // 验证失败 输出错误信息
//            return json_encode(array("code" => 405,"msg" => $validate->getError()));
//        }

        $groupRes = db('goods_group')->where(array("goods_code" => $goodsCode))->update($data);
        if($groupRes === false){
            return json_encode(array("code" => 403, "msg" => "数据保存出错，请再试一次"));
        }
        $tab = $this->getGoodsTab($goodsCode);
        if($tab < 6){
            db('goods_create')->where(array("goods_code" => $goodsCode))->update(array("tab" => 6));
        }
        return json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode)));
    }

    //费用不包含添加 5
    public function notInCost()
    {
        $goodsCode = input('post.goodsCode');

        $post = input("post.");
        if(empty($post["cost_not_include"])){
            return json_encode(array("code" => 404,"msg" => "费用不包含不能为空"));
        }
        $cost_not_include = json_encode($post["cost_not_include"]);

        $array = json_decode($cost_not_include,true);
        $data["single_supplement"] = $array["room"]["one"];     //单房差
        $data["cost_not_include"] = $cost_not_include;  //费用不包含
        $groupRes = db('goods_group')->where(array("goods_code" => $goodsCode))->update($data);
        if($groupRes){
            return json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode)));
        }else {
            return json_encode(array("code" => 403, "msg" => "数据保存出错，请再试一次"));
        }
    }

    //特殊人群限制添加 6
    public function specialPeople()
    {
        $goodsCode = input('post.goodsCode');

        $postData = input("post.");
        $data["crowd_limit"] = json_encode($postData["crowd_limit"]);
        if(empty($data["crowd_limit"])){
            return json_encode(array("code" => 404,"msg" => "不能为空"));
        }
        $groupRes = db('goods_group')->where(array("goods_code" => $goodsCode))->update($data);
        if($groupRes){
            return json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode)));
        }else {
            return json_encode(array("code" => 403, "msg" => "数据保存出错，请再试一次"));
        }

    }

    //预定须知添加 7
    public function advanceKnow()
    {
        $goodsCode = input('post.goodsCode');

        $postData = input("post.");
        $data["book_notice"] = json_encode($postData["book_notice"]);
        if(empty($data["book_notice"])){
            return json_encode(array("code" => 404,"msg" => "不能为空"));
        }
        $groupRes = db('goods_group')->where(array("goods_code" => $goodsCode))->update($data);
        if($groupRes === false){
            return json_encode(array("code" => 403, "msg" => "数据保存出错，请再试一次"));
        }
        $tab = $this->getGoodsTab($goodsCode);
        if($tab < 6){
            db('goods_create')->where(array("goods_code" => $goodsCode))->update(array("tab" => 7));
        }
        return json_encode(array("code" => 200,"data" => array("goodsCode" => $goodsCode)));

    }

    //价格库存添加 11
    public function ratesInventory(){
        $goodsCode = input('post.goodsCode');

//        M('group_price')->where(array('g_code' => $code, 'g_user_code' => $this->userId))->delete();
//        foreach ($rl as $r) {
//            $data['g_go_time'] = $r['priceDate'];//日期
//            $data['g_man_my_price'] = $r['menplatformprice'];//成人平台价格
//            $data['g_man_mark_price'] = $r['menmarketprice'];//成人市场价
//            $data['g_man_js_price'] = $r['mencloseprice'];//成人结算价
//            $data['g_is_child'] = $r['ischildtravel']['val'];//是否有儿童价
//            $data['g_child_my_price'] = $r['ischildtravel']['platformprice'];//儿童平台价格
//            $data['g_child_js_price'] = $r['ischildtravel']['closeprice'];//儿童市场价格
//            $data['g_df_ch'] = $r['issingleroom']['val'];//单房差 -1为无
//            $data['g_df_plat'] = $r['issingleroom']['platformprice'];//单房差 -1为无
//            $data['g_df_ch_close'] = $r['issingleroom']['closeprice'];//单房差 -1为无
//            $data['g_no_kc_num'] = $r['noneedkc'];//不需要确认库存
//            $data['g_need_kc_num'] = $r['needkc'];//需要确认库存
//            $data['g_is_buy'] = $r['islimitNum']['val'];//最大购买量
//            $data['g_max_buy_num'] = $r['islimitNum']['max'];//最大购买量
//            $data['g_min_buy_num'] = $r['islimitNum']['min'];//最低购买量
//            $data['g_code'] = $code;
//            $data['g_user_code'] = $this->userId;
//            $result = M('group_price')->add($data);
//            if (!$result) {
//                $this->ajaxReturn(array('code' => '0', 'msg' => '操作失败'));
//            }
//        }
    }

    //异步上传图片 100
    private function imageUpload(){
//        return json_encode(array("code" => 404,"msg" => "上传大小错误"));
        //todo 商品号
        $goodsCode = input('post.goodsCode');
//        return json_encode(array("code" => 404,"msg" => $goodsCode));
        $imgLimit = config("imageUpLimit");
        $file = request()->file('file');
        if(empty($file)){
            return json_encode(array("code" => 404,"msg" => "参数错误"));
        }
        $info = $file->validate($imgLimit)->move(ROOT_PATH . 'public' . DS . 'image' . DS . 'group');
        if($info){
            return json_encode(array("code" => 200,"data" => array("name" => 'group'. DS  .$info->getSaveName(),"goodsCode"=>$goodsCode)));
        }else{
            // 上传失败获取错误信息
            return json_encode(array("code" => 404,"msg" => $file->getError()));
        }
    }

    //图片删除 101
    private function imageDel(){
        $name = input("post.name");
        $goodsCode = input("post.goodsCode");
        return json_encode(array("code" => 200,"data" => $name));
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
        if(empty($data["route_info"])){
            $data["route_info"]  = "";
        }
        $data["gather_place"]      =   json_encode($data["gather_place"]); //集合地点
        $data["route_info"]        =   json_encode($data["route_info"]); //行程详细
        return $data;
    }

    //产品特色数据接收
    private function sellingPointData(){
        $gain = ['fileList','feature_reasons'];
        $data = Request::instance()->only($gain,'post');//        $data = input('post.');+
        if(empty($data["feature_reasons"])){
            $data["feature_reasons"]  = "";
        }
        $data["feature_reasons"]      =   json_encode($data["feature_reasons"]); //推荐理由


        return $data;
    }

    //费用包含数据接收
    private function includeCostData(){
        $gain = ['main_place','little_traffic', 'stay', 'food_server', 'tick_server','guide_server','safe_server','child_price_type','child_price_info','child_price_supply','give_info'];
        $data = Request::instance()->only($gain,'post');//        $data = input('post.');
        if(empty($data["main_place"])){
            $data["main_place"]  = ""; //门票
        }
        if(empty($data["child_price_info"])){
            $data["child_price_info"]  = ""; //儿童价说明
        }
        $data["main_place"]             =   json_encode($data["main_place"]); //门票
        $data["child_price_info"]        =   json_encode($data["child_price_info"]); //儿童价说明
        return $data;
    }

    //
    private function notInCostData(){
        $gain = ['vis_major', 'stay', 'food_server', 'tick_server','guide_server','safe_server','child_price_type','child_price_info','child_price_supply','give_info'];
        $data = Request::instance()->only($gain,'post');//        $data = input('post.');

    }


    //获取商品页面
    private function getGoodsTab($goodsCode){
        $res = db('goods_create')->field("tab")->where(array("goods_code" => $goodsCode))->find();
        return $res["tab"];
    }

    //商品修改状态检测
    private function checkGoodsType($goodsCode){
        $where = array(
            "code"          =>  $goodsCode,
            'is_del'      =>  ['<>',"1"]  //未删除
        );
        $res = db('goods')->field("check_type")->where($where)->find();
        if(!$res){
            return "没有商品或者商品被删除";
        }
        if($res["check_type"] !== 0 && $res["check_type"] !== 1){
            return "商品不在编辑状态";
        }
        return true;
    }
}