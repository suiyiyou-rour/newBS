<?php
namespace app\home\validate;
use think\Validate;
class Scenery extends Validate
{
    //定义规则
    protected $rule = [
        //基本信息添加 0
        'contact_code'              =>   'require|max:11',          //合同编码      （主）必须
        'add_type'                  =>   'require|max:1|number',    //添加产品类型  （副）必须
        'settlement_type'           =>   'require|max:1|number',   //结算模式      （副）必须
        //套餐信息 2
        'hotel_day'                 =>   'require|max:2|number',    //酒店天数      （副）必须
        'apply_type'                =>   'require|max:24',          //酒店适用人数  （副）必须
        'show_market_price'         =>   'require|number|between:0,999999',   //展示用的市场价格  （副）必须
        'trip_info'                 =>   'require',                  //行程信息      （副）必须
        //商品设置 4
        'advance_time'              =>  'require|max:11|number',    //提前预定时间     （主）必须
        'stock_confirm_time'        =>  'require|max:64',           //普通库存确认时间  （副）必须
        'min_buy_num'               =>  'require|max:4|number',     //最少购买人数  （副）必须
        'max_buy_is_open'           =>  'require|max:1|number',     //最大购买限制  （副）必须
        'max_buy_num'               =>  'max:4|number',              //最多购买人数  （副）
        'refund'                    =>  'require|max:1|number',     //退款设置  （副）必须
        'refund_info'               =>  'require|max:128',          //退款说明  （副） 必须
        'contact_info'              =>  'require|max:128',          //联系人信息  （副）必须
        'play_people_info'          =>  'require|max:128',          //游玩人信息  （副）必须
        'friendship_hints'          =>  'require',                  //友情提示  （副）必须
        'book_notice'               =>  'require',                  //使用说明  （副）必须





    ];

    //反馈信息
    protected $message = [
        //基本信息添加 0
        'contact_code.require'          => '合同编码是必须的',
        'contact_code.max'              => '合同编码不能超过11个字符',
        'add_type.require'              => '添加产品类型是必须选择的',
        'add_type.max'                  => '添加产品类型格式错误',
        'add_type.number'               => '添加产品类型格式错误',
        'settlement_type.require'       => '结算模式是必须选择的',
        'settlement_type.max'           => '结算模式类型格式错误',
        'settlement_type.number'        => '结算模式类型格式错误',
        //套餐信息 2
        'hotel_day.require'             => '酒店天数是必须的',
        'hotel_day.max'                 => '酒店天数不能大于99天',
        'hotel_day.number'              => '酒店天数必须是数字',
        'apply_type.require'            => '酒店适用人数是必须的',
        'apply_type.max'                => '酒店适用人数格式错误',
        'show_market_price.require'    => '市场价是必须的',
        'show_market_price.number'     => '市场价必须是数字',
        'show_market_price.between'    => '市场价不能大于999999',
        'trip_info.require'             => '行程信息是必须的',
        //商品设置 4
        'advance_time.require'          => '提前预定时间是必须的',
        'advance_time.max'              => '提前预定时间不能超过11个字符',
        'advance_time.number'           => '提前预定时间格式错误',
        'stock_confirm_time.require'    => '普通库存确认时间是必须的',
        'stock_confirm_time.max'        => '提前预定时间格式错误',
        'min_buy_num.require'           => '最少购买人数是必须的',
        'min_buy_num.max'               => '最少购买人数不能超过9999',
        'min_buy_num.number'            => '最少购买人数只能是数字',
        'max_buy_is_open.require'       => '最大购买限制是必须的',
        'max_buy_is_open.max'           => '最大购买限制格式错误',
        'max_buy_is_open.number'        => '最大购买限制格式错误',
        'max_buy_num.max'               => '最多购买人数不能超过9999',
        'max_buy_num.number'            => '最多购买人数只能是数字',
        'refund.require'                => '退款设置是必须的',
        'refund.max'                    => '退款设置格式错误',
        'refund.number'                 => '退款设置格式错误',
        'refund_info.require'          => '退款说明是必须的',
        'refund_info.max'               => '退款说明不能超过128个字符',
        'contact_info.require'          => '联系人信息是必须的',
        'contact_info.max'              => '联系人信息格式错误',
        'play_people_info.require'      => '游玩人信息是必须的',
        'play_people_info.max'          => '游玩人信息格式错误',
        'friendship_hints.require'      => '友情提示是必须的',
        'book_notice.require'           => '使用说明是必须的',



    ];

    //定义场景
    protected $scene = [
        //基本信息添加 0
        'addBasicInfo'   =>  ['contact_code', 'add_type', 'settlement_type'],
        //套餐信息 2
        'addPackageInfo' =>  ['hotel_day', 'apply_type', 'show_market_price','trip_info'],
        //商品设置 4
        'addProductInfo' =>  ['advance_time','stock_confirm_time','min_buy_num','max_buy_is_open','max_buy_num', 'refund','refund_info','contact_info','play_people_info','friendship_hints','book_notice'],



    ];







}