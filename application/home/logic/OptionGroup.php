<?php
namespace app\home\logic;

class OptionGroup
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
        $contact = db('contact')->field('code,name,rate')->where(array('sp_code' => 1))->select();
        if(!$contact){
            return json_encode(array("code" => 405,"msg" => "合同加载错误,请刷新页面"));
        }
        $data["contact"] = $contact;
        $data["hash"] = getFromHash();
        return json_encode(array("code" => 200,"data" => $data));
    }

    //行程信息
    public function routeInfo()
    {
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "查询商品号不能为空"));
        }
        $address = db('goods_group')->field('begin_address,main_place')->where(array('goods_code' => $goodsCode))->find();
        if(!$address){
            return json_encode(array("code" => 405,"msg" => "查询错误,请刷新页面"));
        }
        $address["main_place"] = json_decode($address["main_place"]);
        return json_encode(array("code" => 200,"data" => $address));

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
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "查询商品号不能为空"));
        }
        $address = db('goods_group')->field('main_place')->where(array('goods_code' => $goodsCode))->find();
        if(!$address){
            return json_encode(array("code" => 405,"msg" => "查询产品不存在，请联系管理员"));
        }
        $address = json_decode($address["main_place"],true);
        foreach ($address as $k){
            $array = array("bol" => 0 ,"name" => $k["place"]);
            $output[] = $array;
        }
//        $output = $address["tickBox"];
        return json_encode(array("code" => 200,"data" => $output));
    }

    //费用不包含
    public function notInCost()
    {
        $goodsCode = input('post.goodsCode');
        if(empty($goodsCode)){
            return json_encode(array("code" => 404,"msg" => "查询商品号不能为空"));
        }
        $address = db('goods_group')->field('little_traffic')->where(array('goods_code' => $goodsCode))->find();
        if(!$address){
            return json_encode(array("code" => 405,"msg" => "查询产品不存在，请联系管理员"));
        }
        return json_encode(array("code" => 200,"data" => $address));

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