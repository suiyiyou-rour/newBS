<?php
namespace app\home\controller;
use app\common;
class Index extends common\controller\HomeBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo "home/index/base";
    }



}
