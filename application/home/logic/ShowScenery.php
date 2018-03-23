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
        return "basicInfo";
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




}