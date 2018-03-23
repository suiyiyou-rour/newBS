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
//        $this->endOperation($goodsCode,$state);//后置方法
        return json_encode($output);
    }

    //基本信息添加 0
    public function basicInfo()
    {
        //数据验证
        $data = $this->basicInfoData();
        $validate = new \app\home\validate\Scenery();
        $result = $validate->scene('addBasicInfo')->check($data);
        if (true !== $result) {
            return array("code" => 405, "msg" => $validate->getError());
        }

        //主表添加数据
        $goodsData["sp_code"]       =   session("sp.code");     //供应商编号
        $goodsData["contact_code"]  =   $data["contact_code"]; //合同编码  （主）必须
        //副表添加数据
        $sceneryData["add_type"]            =   $data["goods_class"];          //添加产品类型 0手动
        $sceneryData["settlement_type"]    =   $data["city"];                  //结算模式 0底价模式

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

        $goodsRes   = db('goods')->insert($goodsData);
        $sceneryRes = db('goods_scenery')->insert($sceneryData);
        if ($goodsRes && $sceneryRes) {
            db('goods_create')->insert(array('goods_code' => $goodsCode));  //插入页码表
            return array("code" => 200, "data" => array("goodsCode" => $goodsCode));
        } else {
            return array("code" => 403, "msg" => "数据保存出错，请再试一次");
        }
    }


    //打包内容 1
    public function packDetails(){
        return "packDetails";
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

<<<<<<< HEAD
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

    //商品设置数据 4
    private function productSetData(){
        $gain = ['advance_time','stock_confirm_time','min_buy_num','max_buy_is_open','max_buy_num', 'refund','refund_info','contact_info','play_people_info','friendship_hints','book_notice'];
        $data = Request::instance()->only($gain, 'post');
        if(empty($data['stock_confirm_time'])){
            $data['stock_confirm_time'] = "";
        }
        if(empty($data['contact_info'])){
            $data['contact_info'] = "";
        }
        if(empty($data['play_people_info'])){
            $data['play_people_info'] = "";
        }
        $data["stock_confirm_time"] = json_encode($data["stock_confirm_time"]);
        $data["contact_info"] = json_encode($data["contact_info"]);
        $data["play_people_info"] = json_encode($data["play_people_info"]);
        return $data;
    }

    //商品信息数据 5
    private function productInfoData(){
        $gain =  ['show_title', 'on_time', 'off_time','recommend_account','class_label','fileList'];
        $data = Request::instance()->only($gain, 'post');
        if(empty($data['recommend_account'])){
            $data['recommend_account'] = "";
        }
        if(empty($data['class_label'])){
            $data['class_label'] = "";
        }
        $data["recommend_account"] = json_encode($data["recommend_account"]);
        $data["class_label"] = json_encode($data["class_label"]);
        return $data;

    }



    //处理图片数组(前端对象转字符串)
    private function imageSetStr($imageObj){
        $fileList = objSetArray($imageObj);
        $imageArray = array();
        foreach ($fileList as $k) {
            $imageArray[] = $k["name"];
        }
        return json_encode($imageArray);
    }
=======
>>>>>>> f61db4025671ff42f3060c8984fea3ceceed9666

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

    //基本数据接收
    private function basicInfoData(){
        return "";
    }


}