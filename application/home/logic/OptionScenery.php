<?php
namespace app\home\logic;

class OptionScenery
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
                //产品概况
                $output = $this->productStatus();
                break;
            case '2':
                //产品特色添加
                $output = $this->packDetails();
                break;
            case '3':
                //自费项目添加
                $output = $this->packageInfo();
                break;
            case '4':
                //费用包含添加
                $output = $this->ratesInventory();
                break;
            case '5':
                //费用不包含添加
                $output = $this->productSet();
                break;
            case '6':
                //特殊人群限制添加
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
        return "basicInfo";
    }

    //产品概况 1
    public function productStatus()
    {
        return "productStatus";
    }

    //打包内容 2
    public function packDetails(){
        return "packDetails";
    }

    // 套餐信息 3
    public function packageInfo(){
        return "packageInfo";
    }

    //价格库存 4
    public function ratesInventory(){
        return "ratesInventory";
    }

    //商品设置 5
    public function productSet(){
        return "productSet";
    }

    //商品信息
    public function productInfo(){
        return "productInfo";
    }




}