<?php
namespace app\home\validate;
use think\Validate;
class Vehicle extends Validate
{
    //定义规则
    protected $rule = [
        'type'      =>  'require|max:1|number',     //车辆类型 0接驳车 1接送机 2租车 3包车（含司机）
        'instruction'  =>  'require',              //使用说明
        'image'         =>  'require',              //酒店图片
    ];


    //反馈信息
    protected $message = [
        'type.require'              => '车辆类型格式错误',
        'type.max'                  => '车辆类型格式错误',
        'type.number'               => '车辆类型格式错误',
        'instruction.require'      => '使用说明是必须填写的',
        'image.require'             => '酒店图片是必须填写的',
    ];

    //定义场景
    protected $scene = [
        'add'   =>  ['type','instruction',"image"],
    ];
}