<?php
namespace app\home\validate;
use think\Validate;
class Scenery extends Validate
{
    //定义规则
    protected $rule = [
        //基本信息添加
        'contact_code'      =>   'require|max:11',   //合同编码  （主）必须
        'add_type'          =>   'require|max:1|number',   //添加产品类型  （副）必须
        'settlement_type'   =>   'require|max:1|number',   //结算模式  （副）必须


    ];

    //反馈信息
    protected $message = [
        //基本信息添加
        'contact_code.require'      => '合同编码是必须的',
        'contact_code.max'          => '合同编码不能超过11个字符',
        'add_type.require'          => '添加产品类型是必须选择的',
        'add_type.max'              => '添加产品类型格式错误',
        'add_type.number'           => '添加产品类型格式错误',
        'settlement_type.require'   => '结算模式是必须选择的',
        'settlement_type.max'       => '结算模式类型格式错误',
        'settlement_type.number'    => '结算模式类型格式错误',


    ];

    //定义场景
    protected $scene = [
        //基本信息添加
        'addBasicInfo'   =>  ['contact_code', 'add_type', 'settlement_type'],


    ];







}