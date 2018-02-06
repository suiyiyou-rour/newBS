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

    //基本信息
    public function basicInfo()
    {
        $goodsCode = input('post.goodsCode');
        if($goodsCode){//有商品code 查询
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
            if(empty($res)){
                return "ok";
            }else{
                return json_encode(array("code" => 200,"data" => $res));
            }

            //没有 未填完
        }
    }

    //行程信息
    public function routeInfo()
    {
        $goodsCode = input('post.goodsCode');
        $goodsCode = "g001691898";
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "添加商品，商品号不能为空"));
        }
        $Field = "play_day,go_trans,back_trans,go_trans_cost,back_trans_cost,gather_place,route_info";
        return 1;

    }

    //产品特色
    public function sellingPoint()
    {
        return "sellingPoint";
    }

    //自费项目
    public function chargedItem()
    {
        return "chargedItem";
    }

    //费用包含
    public function includeCost()
    {
        return "includeCost";
    }

    //费用不包含
    public function notInCost()
    {
        return "notInCost";
    }

    //特殊人群限制
    public function specialPeople()
    {
        return "specialPeople";
    }

    //预定须知
    public function advanceKnow()
    {
        return "advanceKnow";
    }
}