<?php
namespace app\index\controller;
use app\common\controller\Base;
use PHPqrcode\Qrcode;
class Index extends Base
{
    /**
     * 二维码测试
     */
    public function qcode($type='',$code=''){
        //$pid = cookie('pid');

        $save_path = isset($_GET['save_path'])?$_GET['save_path']:'./static/qrcode/'; 
        $web_path = isset($_GET['save_path'])?$_GET['web_path']:'./static/qrcode/';
        $qr_data = 'http://www.suiyiyou.net';
        $qr_level = isset($_GET['qr_level'])?$_GET['qr_level']:'H';
        $qr_size = isset($_GET['qr_size'])?$_GET['qr_size']:'4'; // 二维码图片大小
        $save_prefix = isset($_GET['save_prefix'])?$_GET['save_prefix']:'ZETA';
        $filename = $this->createQRcode($save_path,$qr_data,$qr_level,$qr_size,$save_prefix);
        $pic = $web_path.$filename;
        // 缩略图
        //thumb($pic,$pic,180,180);
        return $pic;
    }

    
    /**
     * 功能：生成二维码
     * @param string $qr_data     手机扫描后要跳转的网址
     * @param string $qr_level    默认纠错比例 分为L、M、Q、H四个等级，H代表最高纠错能力
     * @param string $qr_size     二维码图大小，1－10可选，数字越大图片尺寸越大
     * @param string $save_path   图片存储路径
     * @param string $save_prefix 图片名称前缀
     */
    public function createQRcode($save_path,$qr_data='PHP QR Code :)',$qr_level='L',$qr_size=4,$save_prefix='qrcode'){
        if(!isset($save_path)) return '';
        //设置生成png图片的路径
        $PNG_TEMP_DIR = & $save_path;

        //检测并创建生成文件夹
        if (!file_exists($PNG_TEMP_DIR)){
            mkdir($PNG_TEMP_DIR);
        }
        $filename = $PNG_TEMP_DIR.'test.png';
        $errorCorrectionLevel = 'L';
        if (isset($qr_level) && in_array($qr_level, array('L','M','Q','H'))){
            $errorCorrectionLevel = & $qr_level;
        }
        $matrixPointSize = 4;
        
        if (isset($qr_size)){
            $matrixPointSize = min(max((int)$qr_size, 1), 10);
        }
    
        if (isset($qr_data)) {
            if (trim($qr_data) == ''){
                die('data cannot be empty!');
            }
            //生成文件名 文件路径+图片名字前缀+md5(名称)+.png
            $filename = $PNG_TEMP_DIR.$save_prefix.md5($qr_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
            //开始生成
            QRcode::png($qr_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        } else {
            //默认生成
            QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        }
        if(file_exists($PNG_TEMP_DIR.basename($filename)))
            return basename($filename);
        else
            return FALSE;
    }

    public function index()
    {
//        $res =
//        var_dump($res);
//        echo onload();
//        Loader::import('first.second.Foo');
//        $foo = new \Foo();
//        echo 1;
//        $res = Db::query('select * from lmm_use');
//        var_dump($res);
//        $res = db('sp')->where(array('id'=>1))->find();
//        var_dump($res);
        $bc = 2;
        $res = objSetArray($bc);
        var_dump($res);

    }

    public function home(){
        echo "欢迎来到KK";
    }

    public function demo(){

    }

    /**
     * 第三方类
     */
    public function classCheck(){
        $foo = new \second\Foo();
        echo $foo->index();
    }

    /**
     * api调用测试
     */
    public function apiClassCheck(){
        $res = new \app\api\controller\Api();
        echo $res->index();
    }

    /**
     * tp3.2 字母方法
     */
    public function letterCheck(){
        //C方法
        echo config("syy");
        //I方法
        //检查变量 input('?post.name');
        //var_dump(input('?get.id'));

        //获取GET、POST或者PUT  input('param.name');
        //input('post.name');/////

        //S方法
        // $value = '文件缓存测试';
        // cache('name', $value, 3600);
        // echo cache('name');
        //$cache = new think\Cache();

        //M方法
//        $res = db('lmm_user')->where('id',1)->find();
//        var_dump($res);
    }

    public function sysvar(){ //系统常量
        $module = request()->module();
        $controller = request()->controller();
        $action = request()->action();
        echo $module."<br/>";
        echo $controller."<br/>";
        echo $action."<br/>";
    }



}
