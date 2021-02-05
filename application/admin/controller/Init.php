<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Loader;

/**
* admin模块基础类
*/
class Init extends Controller
{
	
	function _initialize()
	{
		parent::_initialize();
		error_reporting(0);
		$this->model = model('admin/admin');
		if(!session('?admin_user') && strtolower(request()->controller()) != 'login'){
			$this->redirect('login/login');
		}

		//引用扩展类
        Loader::import("org/Auth", EXTEND_PATH);
        $auth=new \Auth();
        //当前操作
        $this->current_action = request()->module().'/'.request()->controller().'/'.lcfirst(request()->action());
		$this->current_action = strtolower($this->current_action);
        //检查是否有权限
        //站点   1：供货商系统  2：仓储系统  3：物流系统 4：零售系统   5：电商后台系统
        $admin_id = session('admin_user')['id'];
		//var_dump($site,$admin_id,$this->current_action);die; 

		// 不需要判断权限的页面
		$arr = [
			'admin/index/index',
			'admin/index/home',
			'admin/cache/update',   // 更新缓存
			'admin/login/logout',   // 退出登录
			'admin/login/login',    // 登录
		];
		// 不需要判断权限的用户ID
		$guanli = [1];

		// 判断页面  首页不判断权限
		if (!in_array($admin_id, $guanli)) {
			if (!in_array($this->current_action, $arr)) {
				$auth_result = $auth->check($this->current_action,$admin_id);
				if($auth_result == false){
		//            echo "<script>";
		//            echo "alert('权限不足！');";
		//            echo "window.history.back()";
		//            echo "</script>";
					$this->error('您没有权限访问');
					die();
				}
			}
		}
		
		
		$this->settings = cache('settings');
		// 发送基本信息
		$this->assign(['settings' => $this->settings,'admin_user' => session('admin_user')]);
	}


}