<?php
/**
 * 跟团游
 */
namespace app\home\controller;
use app\common;

class Group extends common\controller\Base
{
    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Methods", "GET,POST");
        header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    }

    public function index()
    {
        echo "home/group/index" . "<br/>";
        echo getGoodsCode();

    }

    //添加产品
    public function addgoods()
    {
        $state = input('post.state');
        echo 1;
        die;
        if(!$state){
            $state = 0;
        }
        switch ($state) {
            case 0:
                $this->addGetCode();
                break;
            case 1:
                //基本信息添加
                $this->addBasicInfo();
                break;
            case 2:
                break;
            case 3:
                break;
            case 4:
                break;
            case 5:
                break;
            case 6:
                break;
            case 7:
                break;
            case 8:
                break;
            default:
                echo 1;
        }
    }

    //获取新添加的产品编号
    private function addGetCode()
    {
        $data = getGoodsCode();
        echo json_encode(array("code" => 200,"data" => $data));
        return ;
    }

    //基本信息添加
    private function addBasicInfo()
    {
        echo json_encode(array("code" => 200,"data" => "addBasicInfo"));
        return;
    }

    //行程信息添加
    private function addRouteInfo()
    {

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


}
