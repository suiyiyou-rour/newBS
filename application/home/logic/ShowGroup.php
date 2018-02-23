<?php
namespace app\home\logic;
use think\Request;

class ShowGroup
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

    /**
     * todo
     * todo tab规则  0  1  2  3  4  5  6  7
     * todo 必须     1  1  1  0  1  0  0  1
     * todo 页码写入 0  1  3     6        7
     * todo 数据显示判断（页码） tab 1 2 4 7
     * todo 空值判断 3 5 6
     *
     */

    //基本信息
    public function basicInfo()
    {
        $goodsCode = input('post.goodsCode');
        if($goodsCode){
            //有商品code 查询
            $goodsField = "a.contact_code,a.inside_code,a.inside_title,a.subtitle,a.advance_time,a.online_type,a.on_time,a.off_time,a.rate";
            $groupField = "b.service_type,b.line_type,b.play_type,b.begin_address,b.end_address,b.main_place,b.service_tel,b.refund_type,b.refund_info";
            $allField = $goodsField.','.$groupField;
            $alias = array("syy_goods" => "a","syy_goods_group" => "b");
            $join = [['syy_goods_group','a.code = b.goods_code']];
            $where = [
                "a.code"        => $goodsCode,
                'a.is_del'      =>  ['<>',"1"]  //未删除
            ];
            $data = db('goods')->alias($alias)->join($join)->field($allField)->where($where)->find();
            if(!$data){
                return json_encode(array("code" => 403,"msg" => "商品不存在或者商品被删除，请联系管理员"));
            }
            $data["service_type"]      =   json_decode($data["service_type"]); //服务保障      （副）
            $data["main_place"]        =   json_decode($data["main_place"]); //主要景点     （副）必须
            $data["service_tel"]       =   json_decode($data["service_tel"]); //客服电话     （副）
            $data["refund_info"]       =   json_decode($data["refund_info"]);//梯度详细退款     （副）

            $data["state"] = '0';
            $data["tab"] = $this->getGoodsTab($goodsCode);
            $data["goodsCode"] = $goodsCode;
            return json_encode(array("code" => 200,"data" => $data));

        }else{//没有商品code
            //有 未填完信息
            $alias = array("syy_goods" => "a","syy_goods_create" => "b");
            $join = [['syy_goods_create','a.code = b.goods_code']];
            $where = [
                "a.check_type"  =>  '0',        //制作中
                "a.sp_code"     => "1234567",   //todo 供应商code
                'a.is_del'      =>  ['<>',"1"]  //未删除
            ];
            $goodsField = "a.code,a.inside_title";
            $createField = "b.tab";
            $allField = $goodsField.','.$createField;
            $res = db('goods')->alias($alias)->field($allField)->where($where)->join($join)->order('a.id desc')->select();
            if($res){
                foreach ($res as &$k){
                    $k["tab"] = $k["tab"] + 1;
                }
                return json_encode(array("code" => 1,"data" => $res));
            }else{
                //没有 未填完信息
                return json_encode(array("code" => 2));
            }
        }
    }

    //行程信息
    public function routeInfo()
    {
        //TODO 判断未删除 制作中 填写过 未填写过
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "商品号不能为空"));
        }
        $tab = $this->getGoodsTab($goodsCode);
        if($tab < 1){
            return json_encode(array("code" => 201,"data" => array("tab"=>$tab)));
        }
        $field = "play_day,go_trans,back_trans,go_trans_cost,back_trans_cost,gather_place,route_info";
        $where = [
            //todo 供应商code
//            "check_type"      =>  '0',        //制作中
            "goods_code"        => $goodsCode,
//            "is_del"            =>  ['<>',"1"]  //未删除
        ];
        $groupInfo = db('goods_group')->field($field)->where($where)->find();
        if(!$groupInfo){
            return json_encode(array("code" => 403,"msg" => "商品不存在或者商品被删除，请联系管理员"));
        }else{
            $groupInfo["gather_place"]      =   json_decode($groupInfo["gather_place"]); //集合地点
            $groupInfo["route_info"]        =   json_decode($groupInfo["route_info"]); //行程详细
            $groupInfo["go_trans"]          =   (int)$groupInfo["go_trans"];
            $groupInfo["back_trans"]        =   (int)$groupInfo["back_trans"];
            $groupInfo["state"] = '1';
            $groupInfo["tab"] = $tab;
            $groupInfo["goodsCode"] = $goodsCode;
            return json_encode(array("code" => 200,"data" => $groupInfo));
        }

    }

    //产品特色
    public function sellingPoint()
    {
        //TODO 判断未删除 制作中 填写过 未填写过
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "商品号不能为空"));
        }
        $tab = $this->getGoodsTab($goodsCode);
        if($tab < 2){
            return json_encode(array("code" => 201,"data" => array("tab"=>$tab)));
        }

        $Field = "a.feature_reasons,b.image";
        $alias = array("syy_goods_group" => "a","syy_goods_supply" => "b");
        $join = [['syy_goods_supply','a.goods_code = b.goods_code']];
        $where = [
            "a.goods_code"        => $goodsCode
        ];
        $data = db('goods_group')->alias($alias)->join($join)->field($Field)->where($where)->find();

        if(!$data){
            return json_encode(array("code" => 405,"msg" => "查询失败，请联系管理员"));
        }

        $output["feature_reasons"] = json_decode($data["feature_reasons"]);
        $imgArray = json_decode($data["image"],true);
        $output["fileList"] = array();
        foreach ($imgArray as $k){
            $newArray = [
                "name"  => $k ,
                "url"  => config("img_url") . $k ,
                "status"  => "success" ,
            ];
            $output["fileList"][] = $newArray;
        }
        $output["state"] = '2';
        $output["tab"] = $tab;
        $output["goodsCode"] = $goodsCode;
        return json_encode(array("code" => 200,"data" => $output));

    }

    //自费项目
    public function chargedItem()
    {
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "商品号不能为空"));
        }
        $tab = $this->getGoodsTab($goodsCode);

        $data = db('goods_group')->field("charged_item")->where(array("goods_code"=> $goodsCode))->find();


        if(empty($data["charged_item"])){
            return json_encode(array("code" => 201,"data" => array("tab"=>$tab)));
        }
        $array = json_decode($data["charged_item"],true);
        if(!is_array($array)){
            return json_encode(array("code" => 403,"msg" => "数据异常，请联系管理员"));
        }
        foreach ($array as &$k){
            $k["place"] = (float)$k["place"];
        }
        $output["charged_item"] = $array;
        $output["state"]         = '3';
        $output["tab"]            = $tab;
        $output["goodsCode"]    = $goodsCode;
        return json_encode(array("code" => 200,"data" => $output));


    }

    //费用包含
    public function includeCost()
    {
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "商品号不能为空"));
        }
        $tab = $this->getGoodsTab($goodsCode);
        if($tab < 4){
            return json_encode(array("code" => 201,"data" => array("tab"=>$tab)));
        }
        $field = 'little_traffic,stay,food_server,tick_server,guide_server,safe_server,child_price_type,child_price_info,child_price_supply,give_info';
        $data = db('goods_group')->field($field)->where(array("goods_code"=> $goodsCode))->find();
        if(empty($data)){
            return json_encode(array("code" => 404,"msg" => "查询错误"));
        }

        $data["tick_server"]             =   json_decode($data["tick_server"]); //门票
        $data["child_price_info"]        =   json_decode($data["child_price_info"]); //儿童价说明
        $data["tab"] = $tab;
        $data["state"] = '4';
        $data["goodsCode"]    = $goodsCode;
        return json_encode(array("code" => 200,"data" => $data));


    }

    //费用不包含
    public function notInCost()
    {
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "商品号不能为空"));
        }
        $tab = $this->getGoodsTab($goodsCode);

        $data = db('goods_group')->field("cost_not_include")->where(array("goods_code"=> $goodsCode))->find();

        if(empty($data["cost_not_include"])){
            return json_encode(array("code" => 201,"data" => array("tab"=>$tab)));
        }
        $output["state"]       = '5';
        $output["tab"]         = $tab;
        $output["goodsCode"]    = $goodsCode;
        $output["cost_not_include"] = json_decode($data["cost_not_include"]);

        return json_encode(array("code" => 200,"data" => $output));


    }

    //特殊人群限制
    public function specialPeople()
    {
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "商品号不能为空"));
        }
        $tab = $this->getGoodsTab($goodsCode);
        $data = db('goods_group')->field("crowd_limit")->where(array("goods_code"=> $goodsCode))->find();
        if(empty($data["crowd_limit"])){
            return json_encode(array("code" => 201,"data" => array("tab"=>$tab)));
        }
        $output["state"]       = '6';
        $output["tab"]         = $tab;
        $output["goodsCode"]    = $goodsCode;
        $output["crowd_limit"] = json_decode($data["crowd_limit"]);

        return json_encode(array("code" => 200,"data" => $output));
    }

    //预定须知
    public function advanceKnow()
    {
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "商品号不能为空"));
        }
        $tab = $this->getGoodsTab($goodsCode);
        $data = db('goods_group')->field("book_notice")->where(array("goods_code"=> $goodsCode))->find();
        if(empty($data["book_notice"])){
            return json_encode(array("code" => 201,"data" => array("tab"=>$tab)));
        }
        $output["state"]       = '7';
        $output["tab"]         = $tab;
        $output["goodsCode"]    = $goodsCode;
        $output["book_notice"] = json_decode($data["book_notice"]);

        return json_encode(array("code" => 200,"data" => $output));
    }

    //获取商品页面
    private function getGoodsTab($goodsCode){
        $res = db('goods_create')->field("tab")->where(array("goods_code" => $goodsCode))->find();
        return $res["tab"];
    }
}