<?php
namespace app\index\controller;

use think\Controller;

/**
* index模块基础类
*/
class Init extends Controller
{
	
	function _initialize()
	{
		if(!file_exists(APP_PATH.'install.lock')){
			$this->redirect(url('/install/index'));
		}
		parent::_initialize();
		error_reporting(0);
		if(!session('?member')){ session('member.id',0);}
		$this->settings = cache('settings');
		if(!session('?admin_user') && $this->settings['site_status'] != 1){
			exit('<meta charset="UTF-8"><p style="text-align:center;font-size:42px;color:#f00;line-height:60px;font-weight:bold;padding-top:100px;margin-bottom:0">网站暂时关闭...</p><p style="width:500px;margin:0 auto;padding:20px;line-height24px;font-weight:bold;">关闭原因：'.$this->settings['site_closedreason'].'</p>');
		}
		$this->categorys = cache('categorys');
		$this->models = cache('models');
		$search_model_ids = explode(',', $this->settings['search_model']);
		foreach ($search_model_ids as $model_id) {
			$search_model_select[$model_id] = $this->models[$model_id];
		}
		$this->seo['title'] = $this->settings['site_name'].$this->settings['title_add'];
		$this->seo['keywords'] = $this->settings['keywords'];
		$this->seo['description'] = $this->settings['description'];
		
		$i = 2;
		foreach($this->categorys as &$v) {
			if ($v['is_menu'] == 1) {
				$v['item'] = $i;
			}
			$i++;
		}
		// 发送基本信息
		$this->assign(['settings' => $this->settings,'categorys' => $this->categorys,'search_model_select'=>$search_model_select,'seo' => $this->seo]);
	}


	// 获取用户user_agent
	public function is_mobile(){
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $is_pc = (strpos($agent, 'windows nt')) ? true : false;
        $is_mac = (strpos($agent, 'mac os')) ? true : false;
        $is_iphone = (strpos($agent, 'iphone')) ? true : false;
        $is_android = (strpos($agent, 'android')) ? true : false;
        $is_ipad = (strpos($agent, 'ipad')) ? true : false;        

        if($is_pc){              
        	return  false;
		} 
		
		if($is_iphone){              
        	return  true;
		} 
		
		if($is_ipad){              
        	return  true;
        }

        if($is_mac){              
        	return  false;
        }        

        if($is_android){              
        	return  true;
        }        

        
}


}