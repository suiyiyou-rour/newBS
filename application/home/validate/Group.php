<?php
namespace app\home\validate;
use think\Validate;
class Group extends Validate
{
    protected $rule = [
        'contact_code'      =>   'require|max:11',   //合同编码  （主）必须
        'inside_code'       =>   'max:24',   //供应商产品内部编号    （主）
        'inside_title'      =>   'require|max:50',//内部显示标题   （主）必须
        'subtitle'          =>   'max:150',//商品副标题     （主）
        'service_type'      =>   'max:20',//服务保障      （副）
        'line_type'         =>   'max:1|number',//路线类型     （副）
        'play_type'         =>   'max:1|number',//游玩类型     （副）
        'begin_address'     =>  'require|max:30',//出发地     （副）必须
        'end_address'       =>  'require|max:30',//目的地     （副）必须
        'main_place'        =>  'require',//主要景点     （副）必须
        'advance_time'      =>  'require|max:11|number',//提前预定时间     （主）必须
        'online_type'       =>  'require|max:1|number',//上线类型   (主)必须
        'on_time'           =>  'max:11|number',//上线时间     （主）
        'off_time'          =>  'max:11|number',//下线时间     （主）
        'service_tel'       =>  'max:128',//客服电话     （副）
        'refund_type'       =>  'require|max:1',//退款类型     （副）必须
//        'refund_info'       =>  '',//梯度详细退款     （副）
        'rate'               =>  'require|max:3',//产品费率     （主）必须
    ];

    protected $message = [
        'contact_code.require'  => '合同编码是必须的',
        'contact_code.max'      => '合同编码不能超过11个字符',
        'inside_code.max'       => '供应商产品内部编号不能超过24个字符',
        'inside_title.require'  => '内部显示标题是必须的',
        'inside_title.max'      => '内部显示标题不能超过50个字符',
        'subtitle.max'          => '商品副标题不能超过150个字符',
        'service_type.max'      => '服务保障不能超过20个字符',
        'line_type.max'         => '路线类型不能超过1个字符',
        'line_type.number'      => '路线类型只能是数字',
        'play_type.max'         => '游玩类型不能超过1个字符',
        'play_type.number'      => '游玩类型格式错误',
        'begin_address.require' => '出发地是必须的',
        'begin_address.max'     => '出发地不能超过30个字符',
        'end_address.require'   => '目的地是必须的',
        'end_address.max'       => '目的地不能超过30个字符',
        'main_place.require'    => '主要景点是必须的',
        'advance_time.require'  => '提前预定时间是必须的',
        'advance_time.max'      => '提前预定时间不能超过11个字符',
        'advance_time.number'   => '提前预定时间格式错误',
        'online_type.require'   => '上线类型是必须的',
        'online_type.max'       => '上线类型不能超过11个字符',
        'online_type.number'    => '上线类型格式错误',
        'on_time.max'           => '上线时间不能超过11个字符',
        'on_time.number'        => '上线时间只能是数字',
        'off_time.max'          => '下线时间不能超过11个字符',
        'off_time.number'       => '下线时间只能是数字',
        'service_tel.max'       => '客服电话不能超过128个字符',
        'refund_type.require'   => '退款类型是必须的',
        'refund_type.max'       => '退款类型格式错误',
        'rate.require'          => '产品费率是必须的',
        'rate.max'              => '产品费率格式错误',
    ];

    protected $scene = [
        'add'  =>  ['contact_code','inside_code'],
        'addBasicInfo'   =>  [
            'contact_code',
            'inside_code',
            'inside_title',
            'subtitle',
            'service_type',
            'line_type',
            'play_type',
            'begin_address',
            'end_address',
            'main_place',
            'advance_time',
            'online_type',
            'on_time',
            'off_time' ,
            'service_tel',
            'refund_type',
            'refund_info',
            'rate'
        ],

    ];







}