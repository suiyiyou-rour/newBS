<?php
namespace app\home\logic;

use think\Request;

class AddTicket
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
                //行程信息添加
                $output = $this->buyUsed();
                break;
            case '2':
                //产品特色添加
                $output = $this->rulesSet();
                break;
            case '11':
                //价格库存
                $output = $this->ratesInventory();
                break;
            case '100':
                //图片异步上传
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
        $goodsCode = input('post.goodsCode');
        //数据验证
        $data = $this->basicInfoData();
        $validate = new \app\home\validate\Ticket();
        $result = $validate->scene('addBasicInfo')->check($data);
        if (true !== $result) {
            return array("code" => 405, "msg" => $validate->getError());
        }

        //主表添加数据
        $goodsData["sp_code"]       =   session("sp.code");     //供应商编号
        $goodsData["contact_code"]  =   $data["contact_code"]; //合同编码  （主）必须
        $goodsData["show_title"]    =   $data["show_title"];    //产品名称(外部标题) （主）必须
        //副表添加数据
        $ticketData["goods_class"]       =   $data["goods_class"];          //商品种类  （副）必须
        $ticketData["city"]              =   $data["city"];                  //城市      （副）必须
        $ticketData["place_name"]        =   $data["place_name"];           //景点名称   （副）必须
        $ticketData["ticket_type"]       =   $data["ticket_type"];          //门票票种  （副）必须
        $ticketData["include_cost"]      =   $data["include_cost"];         //费用包含 （副）必须
        $ticketData["include_cost_info"] =   $data["include_cost_info"];   //费用包含补充说明（副）
        $ticketData["not_include_info"]  =   $data["not_include_info"];    //费用不包含（副）
        $ticketData["safe_server"]       =   $data["safe_server"];          //保险服务 1 0 （副）必须
        $ticketData["safe_server_info"] =   $data["safe_server_info"];       //保险说明 （副）
        $ticketData["service_tel"]       =   $data["service_tel"];           //客服电话 （副）必须
        //补充表
//        $supplyData["image"]        =   $data["image"];     //图片（补）
        //图片数组处理
        if (empty($data["fileList"])) {
            return array("code" => 404, "msg" => "图片参数上传参数错误");
        }
        $fileList = objSetArray($data["fileList"]);
        if (empty($fileList[0]["name"])) {
            return array("code" => 404, "msg" => "图片参数上传参数错误-首图丢失");
        }
        $imageArray = array();
        foreach ($fileList as $k) {
            $imageArray[] = $k["name"];
        }
        $goodsData["head_img"] = $fileList[0]["name"];      //首图(主)
        $supplyData["image"] = json_encode($imageArray);    //图片数组（补充）

        //有商品号（更新）
        if ($goodsCode) {
            $goodsRes = db('goods')->where(array("code" => $goodsCode))->update($goodsData);
            $groupRes = db('goods_ticket')->where(array("goods_code" => $goodsCode))->update($ticketData);
            if ($goodsRes === false) {
                return array("code" => 403, "msg" => "保存出错，请稍后再试");
            }
            if ($groupRes === false) {
                return array("code" => 403, "msg" => "保存错误，请稍后再试");
            }
            return array("code" => 200, "data" => array("goodsCode" => $goodsCode));
        }

        //没有商品号（保存）
        $hash = input('post.hash');
        if (!checkFromHash($hash)) {
            return array("code" => 405, "msg" => "您表单提交速度过快，请3秒后重试。");
        }
        $goodsCode = createGoodsCode("t");                  //产品编号
        //主表添加数据
        $goodsData["code"]          =   $goodsCode;        //产品编号
        $goodsData["create_time"]   =   time();            //创建时间
        $goodsData["goods_type"]    =   2;                 //门票
        //副表
        $ticketData["goods_code"]   =   $goodsCode;        //产品编号
        //补充表
        $supplyData["goods_code"]   =   $goodsCode;         //产品编号

        $goodsRes = db('goods')->insert($goodsData);
        $groupRes = db('goods_ticket')->insert($ticketData);
        $supplyRes = db('goods_supply')->insert($supplyData);
        if ($goodsRes && $groupRes && $supplyRes) {
            db('goods_create')->insert(array('goods_code' => $goodsCode));  //插入页码表
            return array("code" => 200, "data" => array("goodsCode" => $goodsCode));
        } else {
            return array("code" => 403, "msg" => "数据保存出错，请再试一次");
        }
    }

    //购买使用说明 1
    public function buyUsed()
    {
        $goodsCode = input('post.goodsCode');
        //数据验证
        $data = $this->buyUsedData();
        $validate = new \app\home\validate\Ticket();
        $result = $validate->scene('addBuyUsed')->check($data);
        if (true !== $result) {
            return array("code" => 405, "msg" => $validate->getError());
        }
        //主表添加数据
        $goodsData["advance_time"]      =   $data["advance_time"]; //提前预定时间   （主）必须
        //副表添加数据
        $ticketData["contact_need"]     =   $data["contact_need"];  //联系人信息      (副)必须
        $ticketData["player_info"]      =   $data["player_info"];   //游玩人限制信息  (副)必须
        $ticketData["min_buy_num"]      =   $data["min_buy_num"];   //最少购买人数   (副)必须
        $ticketData["max_buy_num"]      =   $data["max_buy_num"];   //最多购买人数   (副)必须
        $ticketData["mobile_limit"]     =   $data["mobile_limit"];  //手机号限制    (副)
        $ticketData["identity_limit"]   =   $data["identity_limit"];//身份证限制    (副)
        $ticketData["entrance_time"]    =   $data["entrance_time"]; //入园时间      (副)必须
        $ticketData["entrance_place"]   =   $data["entrance_place"];//入园地址     (副)必须

        $goodsRes = db('goods')->where(array("code" => $goodsCode))->update($goodsData);
        $groupRes = db('goods_ticket')->where(array("goods_code" => $goodsCode))->update($ticketData);
        if ($goodsRes === false) {
            return array("code" => 403, "msg" => "保存出错，请稍后再试");
        }
        if ($groupRes === false) {
            return array("code" => 403, "msg" => "保存错误，请稍后再试");
        }

        return array("code" => 200, "data" => array("goodsCode" => $goodsCode));

    }

    //价格库存有效期 规则设置2
    public function rulesSet(){
        $priceType = input('post.price_type');
        if($priceType != "1" && $priceType != "2"){//1价格日历 2有效期
            return array("code" => 403, "msg" => "日期模式错误");
        }
        if($priceType == "1"){
            return $this->rulesSetIndate();
        }
        return $this->rulesSetCalendar();
    }


    //有效期 tab2 设置
    private function rulesSetIndate(){
        $goodsCode = input('post.goodsCode');
        //数据验证
        $data = $this->rulesSetIndateData();
        $validate = new \app\home\validate\Ticket();
        $result = $validate->scene('AddRulesSetIndate')->check($data);
        if (true !== $result) {
            return array("code" => 405, "msg" => $validate->getError());
        }
        return "";
    }

    //价格日历 tab2 设置
    private function rulesSetCalendar(){
        $goodsCode = input('post.goodsCode');
        //数据验证
        $data = $this->rulesSetCalendarData();
        $validate = new \app\home\validate\Ticket();
        $result = $validate->scene('AddRulesSetCalendar')->check($data);
        if (true !== $result) {
            return array("code" => 405, "msg" => $validate->getError());
        }


        return "";
    }


    //价格库存 11
    public function ratesInventory()
    {
        return "ratesInventory";
    }

    //异步上传图片 100
    private function imageUpload()
    {
        $imgLimit = config("imageUpLimit");
        $file = request()->file('file');
        if (empty($file)) {
            return array("code" => 404, "msg" => "参数错误");
        }
        $info = $file->validate($imgLimit)->move(ROOT_PATH . 'public' . DS . 'image' . DS . 'ticket');
        if ($info) {
            return array("code" => 200, "data" => array("name" => 'ticket' . DS . $info->getSaveName()));
        } else {
            // 上传失败获取错误信息
            return array("code" => 403, "msg" => $file->getError());
        }
    }

    //图片删除 101
    private function imageDel()
    {
        $name = input("post.name");
        $goodsCode = input("post.goodsCode");
        return array("code" => 200, "data" => $name);
    }


    //基本信息数据接收 0
    private function basicInfoData()
    {
        $gain = ['contact_code', 'goods_class', 'city', 'place_name', 'ticket_type', 'show_title', 'include_cost', 'include_cost_info', 'not_include_info', 'safe_server','safe_server_info', 'service_tel', 'fileList'];
        $data = Request::instance()->only($gain, 'post');//        $data = input('post.');
        if (empty($data["include_cost"])) {
            $data["include_cost"] = ""; //费用包含     （副）必须
        }
        if (empty($data["service_tel"])) {
            $data["service_tel"] = ""; //客服电话      （副）必须
        }
        if (empty($data["place_name"])) {
            $data["place_name"] = ""; //景点名称      （副）必须
        }
        if(empty($data["safe_server_info"])){
            $data["safe_server_info"] = "";
        }
        $data["include_cost"] = json_encode($data["include_cost"]);
        $data["service_tel"] = json_encode($data["service_tel"]);
        $data["place_name"] = json_encode($data["place_name"]);
        return $data;
    }

    //购买使用说明 1
    private function buyUsedData()
    {
        $gain = ['advance_time', 'contact_need', 'player_info', 'min_buy_num', 'max_buy_num', 'mobile_limit', 'identity_limit', 'entrance_time', 'entrance_place'];
        $data = Request::instance()->only($gain, 'post');//        $data = input('post.');
        if (empty($data["player_info"])) {
            $data["player_info"] = ""; //游玩人信息
        }
        if (empty($data["mobile_limit"])) {
            $data["mobile_limit"] = ""; //手机号限制
        }
        if (empty($data["identity_limit"])) {
            $data["identity_limit"] = ""; //身份证
        }
        if (empty($data["entrance_time"])) {
            $data["entrance_time"] = ""; //入园时间
        }
        if (empty($data["entrance_place"])) {
            $data["entrance_place"] = ""; //入园地址
        }
        $data["player_info"] = json_encode($data["player_info"]);
        $data["mobile_limit"] = json_encode($data["mobile_limit"]);
        $data["identity_limit"] = json_encode($data["identity_limit"]);
        $data["entrance_time"] = json_encode($data["entrance_time"]);
        $data["entrance_place"] = json_encode($data["entrance_place"]);
        return $data;
    }

    //价格日历 tab2 数据接收
    private function rulesSetCalendarData()
    {
        $gain = ['price_type','effective_days','stock_num','refund','refund_info','online_type','offline_type','on_time','off_time'];
        $data = Request::instance()->only($gain, 'post');//        $data = input('post.');
        return $data;
    }

    //有效期 tab2 数据接收
    private function rulesSetIndateData()
    {
        $gain = ['price_type', 'usable_date', 'disabled_date','stock_num', 'plat_price', 'settle_price','market_price','refund','refund_info','online_type','offline_type','on_time','off_time'];
        $data = Request::instance()->only($gain, 'post');//        $data = input('post.');
        //todo 有效期时间段没接收
        if (empty($data["usable_date"])) {
            $data["usable_date"] = ""; //可以用日期
        }
        if (empty($data["disabled_date"])) {
            $data["disabled_date"] = ""; //不可用日期
        }
        $data["usable_date"] = json_encode($data["usable_date"]);
        $data["disabled_date"] = json_encode($data["disabled_date"]);
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
        $this->lastEditTime($goodsCode);
        $tab = db('goods_create')->where(array("goods_code" => $goodsCode))->value('tab');
        if($tab !== null && $tab < 2){
            //更新tab
            if($state == 1){
                if ($tab < 1) {
                    db('goods_create')->where(array("goods_code" => $goodsCode))->update(array("tab" => 1));
                };
            }else if($state == 2){
                if ($tab < 2) {
                    db('goods_create')->where(array("goods_code" => $goodsCode))->update(array("tab" => 2));
                };
            }
        }
    }

    //更改商品保存状态 从已编辑到保存 0 - 1
    private function saveGoodsType($goodsCode){
        $where = [
            "code"        => $goodsCode,
            'is_del'      =>  ['<>',"1"]  //未删除
        ];
        $res = db('goods')->field("check_type")->where($where)->find();
        if($res && $res["check_type"] == 0){
            $calendarType = db('goods_calendar')->field("id")->where(array("goods_code" => $goodsCode))->find();
            if($calendarType){
                db('goods')->where(array("code" => $goodsCode))->update(array("check_type"=>1));
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