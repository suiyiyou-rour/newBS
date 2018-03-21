<?php
namespace app\home\logic;

class ShowScenery
{
    /**
     * 状态分发
     */
    public function dispatcher($state)
    {
        //需要商品code
        $goodsCode = input('post.goodsCode');
        if ($state != '0') {
            if (empty($goodsCode)) {
                return json_encode(array("code" => 412, "msg" => "显示商品，商品号不能为空"));
            }
        }
        switch ($state) {
            case '0':
                //基本信息添加
                $output = $this->basicInfo();
                break;
            case '1':
                //打包内容
                $output = $this->packDetails();
                break;
            case '2':
                //套餐信息
                $output = $this->packageInfo();
                break;
            case '3':
                //价格库存
                $output = $this->ratesInventory();
                break;
            case '4':
                //商品设置
                $output = $this->productSet();
                break;
            case '5':
                //商品信息
                $output = $this->productInfo();
                break;
            default:
                $output = array("code" => 404, "msg" => "参数错误");
        }
//        $this->endOperation($goodsCode,$state);//后置方法
        return json_encode($output);
    }

    //基本信息添加 0
    public function basicInfo()
    {
        $goodsCode = input('post.goodsCode');
        if($goodsCode){
            //有商品code 查询
            $goodsField = "a.contact_code,a.inside_code";
            $ticketField = "b.add_type,b.settlement_type";
            $allField = $goodsField.','.$ticketField;
            $join = [
                ['syy_goods_scenery b','a.code = b.goods_code']
            ];
            $where = [
                "a.code"         => $goodsCode,
                "a.goods_type"  => 3,
                "a.is_del"       =>  ["<>","1"]  //未删除
            ];
            $data = db('goods')->alias("a")->join($join)->field($allField)->where($where)->find();
            if(!$data){
                return array("code" => 403,"msg" => "商品不存在或者商品被删除，请联系管理员");
            }

            $data["state"] = '0';
            $data["tab"] = $this->getGoodsTab($goodsCode);
            $data["goodsCode"] = $goodsCode;
            return array("code" => 200,"data" => $data);
        }

        //没有商品code
        $join = [['syy_goods_create b','a.code = b.goods_code']];
        $where = [
            "a.check_type"  =>  "0",         //制作中
            "a.goods_type"  =>  "3",         //景酒
            "a.sp_code"     =>  getSpCode(),  //供应商code
            "a.is_del"      =>  ["<>","1"]   //未删除
        ];
        $goodsField = "a.code,a.show_title,a.inside_code";
        $createField = "b.tab";
        $allField = $goodsField.','.$createField;
        $res = db('goods')->alias("a")->field($allField)->where($where)->join($join)->order('a.id desc')->select();
        //有 未填完信息
        if($res){
            foreach ($res as &$k){
                $k["tab"] = $k["tab"] + 1;
                if(empty($k["show_title"])){
                    $k["show_title"] = "未填写标题";
                }
            }
            return array("code" => 201,"data" => $res);
        }
        //没有 未填完信息
        return array("code" => 202);
    }


    //打包内容 1
    public function packDetails(){
        $goodsCode = input('post.goodsCode');
        $tab = $this->getGoodsTab($goodsCode);
        if($tab < 1){
            return array("code" => 203,"data" => array("tab"=>$tab));
        }
        $output = db('goods_scenery')->field('hotel_code,view_code,meal_code,vehicle_code')->where(array("goods_code"=> $goodsCode))->find();
        if(empty($output)){
            return array("code" => 404,"msg" => "查询错误");
        }
        $output["hotel_code"] = json_decode($output["hotel_code"],true);
        $output["view_code"] = json_decode($output["view_code"],true);
        $output["meal_code"] = json_decode($output["meal_code"],true);
        $output["vehicle_code"] = json_decode($output["vehicle_code"],true);
        $output["tab"] = $tab;
        $output["state"] = '1';
        $output["goodsCode"]    = $goodsCode;
        return array("code" => 200,"data" => $output);
    }


    // 套餐信息 2
    public function packageInfo(){
        return "packageInfo";
    }

    //价格库存 3
    public function ratesInventory(){
        return "ratesInventory";
    }

    //商品设置 4
    public function productSet(){
        return "productSet";
    }

    //商品信息 5
    public function productInfo(){
        return "productInfo";
    }


    //获取商品页面 辅
    private function getGoodsTab($goodsCode){
        $res = db('goods_create')->field("tab")->where(array("goods_code" => $goodsCode))->find();
        return $res["tab"];
    }

}