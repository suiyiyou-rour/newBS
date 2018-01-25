<?php
namespace app\home\controller;
use app\common;
class Group extends common\controller\Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo "home/group/index"."<br/>";
        echo getGoodsCode();

    }

    //获取新添加的产品编号
    public function addGetCode(){

    }

    //基本信息添加
    public function addBasicInfo(){

    }

    //行程信息添加
    public function addRouteInfo(){

    }

    //产品特色添加
    public function addSellingPoint(){

    }

    //自费项目添加
    public function addChargedItem(){

    }

    //费用包含添加
    public function addIncludeCost(){

    }

    //费用不包含添加
    public function addNotInCost(){

    }

    //特殊人群限制添加
    public function addSpecialPeople(){

    }

    //预定须知添加
    public function addadvanceKnow(){

    }



}
