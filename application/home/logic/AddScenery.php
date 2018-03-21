<?php
namespace app\home\logic;
use think\Request;

class AddScenery
{
    /**
     * 状态分发
     */
    public function dispatcher($state)
    {
        //需要商品code
        $goodsCode = input('post.goodsCode');
        if ($state != '0' && $state != '100' && $state != '101') {
            if (empty($goodsCode)) {
                return json_encode(array("code" => 412, "msg" => "添加商品，商品号不能为空"));
            }
            //是否有写入状态检测
            $res = $this->checkGoodsType($goodsCode);
            if ($res !== true) {
                return json_encode(array("code" => 405, "msg" => $res));
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
            case '100':
                //图片上传
                $output = $this->imageUpload();
                break;
            case '101':
                //图片删除
                $output = $this->imageDel();
                break;
            default:
                $output = array("code" => 404, "msg" => "参数错误");
        }
        $this->endOperation($goodsCode,$state);//后置方法
        return json_encode($output);
    }

    //基本信息添加 0
    public function basicInfo()
    {
        //数据验证
        $gain = ['contact_code', 'add_type', 'settlement_type','inside_code'];
        $data = Request::instance()->only($gain, 'post');
        $validate = new \app\home\validate\Scenery();
        $result = $validate->scene('addBasicInfo')->check($data);
        if (true !== $result) {
            return array("code" => 405, "msg" => $validate->getError());
        }

        //主表添加数据
        $goodsData["sp_code"]       =   session("sp.code");     //供应商编号
        $goodsData["contact_code"]  =   $data["contact_code"]; //合同编码  （主）必须
        $goodsData["inside_code"]  =   $data["inside_code"];    //供应商内部编号  （主）必须

        //副表添加数据
        $sceneryData["add_type"]            =   $data["add_type"];          //添加产品类型 0手动
        $sceneryData["settlement_type"]    =   $data["settlement_type"];   //结算模式 0底价模式

        //有商品号（更新）
        $goodsCode = input('post.goodsCode');
        if ($goodsCode) {
            $goodsRes = db('goods')->where(array("code" => $goodsCode))->update($goodsData);
            $sceneryRes = db('goods_scenery')->where(array("goods_code" => $goodsCode))->update($sceneryData);
            if ($goodsRes === false) {
                return array("code" => 403, "msg" => "保存出错，请稍后再试");
            }
            if ($sceneryRes === false) {
                return array("code" => 403, "msg" => "保存错误，请稍后再试");
            }
            return array("code" => 200, "data" => array("goodsCode" => $goodsCode));
        }

        //没有商品号（保存）
        $hash = input('post.hash');
        if (!checkFromHash($hash)) {
            return array("code" => 405, "msg" => "您表单提交速度过快，请3秒后重试。");
        }
        $goodsCode = createGoodsCode("s");                  //产品编号
        //主表添加数据
        $goodsData["code"]          =   $goodsCode;        //产品编号
        $goodsData["create_time"]   =   time();            //创建时间
        $goodsData["goods_type"]    =   3;                 //酒景
        //副表
        $sceneryData["goods_code"]   =   $goodsCode;        //产品编号
        //补充表
        $supplyData["goods_code"]   =   $goodsCode;         //产品编号

        $goodsRes   = db('goods')->insert($goodsData);      //主表
        $sceneryRes = db('goods_scenery')->insert($sceneryData);//副表
        $supplyRes = db('goods_supply')->insert($supplyData);//补充表

        if ($goodsRes && $sceneryRes && $supplyRes) {
            db('goods_create')->insert(array('goods_code' => $goodsCode));  //插入页码表
            return array("code" => 200, "data" => array("goodsCode" => $goodsCode));
        } else {
            return array("code" => 403, "msg" => "数据保存出错，请再试一次");
        }
    }


    //打包内容 1
    public function packDetails(){
        $goodsCode = input('post.goodsCode');
        $data = $this->packDetailsData();       //数据接收

        $validate = new \app\home\validate\Scenery();
        $result = $validate->scene('addPackDetails')->check($data);
        if (true !== $result) {
            return array("code" => 405, "msg" => $validate->getError());
        }
        $sceneryRes = db('goods_scenery')->where(array("goods_code" => $goodsCode))->update($data);
        if ($sceneryRes === false) {
            return array("code" => 403, "msg" => "保存出错，请稍后再试");
        }
        return array("code" => 200, "data" => array("goodsCode" => $goodsCode));
    }

    // 套餐信息 2
    public function packageInfo(){
        return "packageInfo";
        //数据验证
//        $data = $this->packageInfoData();
//        $validate = new \app\home\validate\Scenery();
//        $result = $validate->scene('addPackageInfo')->check($data);
//        if (true !== $result) {
//            return array("code" => 405, "msg" => $validate->getError());
//        }
    }

    //价格库存 3
    public function ratesInventory(){
        return "ratesInventory";
    }

    //商品设置 4
    public function productSet(){
        //数据验证
//        $data = $this->packageInfoData();
//        $validate = new \app\home\validate\Scenery();
//        $result = $validate->scene('addProductInfo')->check($data);
//        if (true !== $result) {
//            return array("code" => 405, "msg" => $validate->getError());
//        }
        return "productSet";
    }

    //商品信息 5
    public function productInfo(){
        return "productInfo";
    }


    //异步上传图片 100
    private function imageUpload()
    {
//        return array("code" => 404,"msg" => "上传大小错误");
        //todo 商品号
        $goodsCode = input('post.goodsCode');
//        return array("code" => 404,"msg" => $goodsCode);
        $imgLimit = config("imageUpLimit");
        $file = request()->file('file');
        if (empty($file)) {
            return array("code" => 404, "msg" => "参数错误");
        }
        $info = $file->validate($imgLimit)->move(ROOT_PATH . 'public' . DS . 'image' . DS . 'group');
        if ($info) {
            return array("code" => 200, "data" => array("name" => 'group' . DS . $info->getSaveName(), "goodsCode" => $goodsCode));
        } else {
            // 上传失败获取错误信息
            return array("code" => 404, "msg" => $file->getError());
        }
    }

    //图片删除 101
    private function imageDel()
    {
        $name = input("post.name");
        $goodsCode = input("post.goodsCode");
        return array("code" => 200, "data" => $name);
    }

    //打包内容数据 1
    private function packDetailsData(){
        $gain = ['hotel_code', 'view_code', 'meal_code','vehicle_code'];
        $data = Request::instance()->only($gain, 'post');
        if(empty($data['hotel_code'])){
            $data['hotel_code'] = "";
        }
        if(empty($data['view_code'])){
            $data['view_code'] = "";
        }
        if(empty($data['meal_code'])){
            $data['meal_code'] = "";
        }
        if(empty($data['vehicle_code'])){
            $data['vehicle_code'] = "";
        }
        $data["hotel_code"] = json_encode($data["hotel_code"]);
        $data["view_code"] = json_encode($data["view_code"]);
        $data["meal_code"] = json_encode($data["meal_code"]);
        $data["vehicle_code"] = json_encode($data["vehicle_code"]);
        return $data;

    }










    //商品修改状态检测
    private function checkGoodsType($goodsCode)
    {
        $where = array(
            "code" => $goodsCode,
            'is_del' => ['<>', "1"]  //未删除
        );
        $res = db('goods')->field("check_type")->where($where)->find();
        if (!$res) {
            return "没有商品或者商品被删除";
        }
        if ($res["check_type"] !== 0 && $res["check_type"] !== 1) {
            return "商品不在编辑状态";
        }
        return true;
    }

    //后置方法 步骤操作结束后完成的事
    private function endOperation($goodsCode,$state){
        $this->lastEditTime($goodsCode);//更新时间
        $tab = db('goods_create')->where(array("goods_code" => $goodsCode))->value('tab');
        if($tab !== null && $tab < 5){
            //更新tab
            if($state == 1){
                if ($tab < 1) {
                    db('goods_create')->where(array("goods_code" => $goodsCode))->update(array("tab" => 1));
                };
            }
        }
    }

    //更新最后一次编辑时间
    private function lastEditTime($goodsCode){
        $where = [
            "code"              => $goodsCode,
            "is_del"            =>  ['<>',"1"],  //未删除
        ];
        $res = db('goods')->field("id")->where($where)->find();
        if($res){
            $data["last_edit_time"] = time();
            db('goods')->where(array("code" => $goodsCode))->update($data);
        }
    }


}