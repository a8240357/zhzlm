<?php
namespace app\index\controller;

/**
* 单页控制器类
*/
class Page extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/page');
		$this->category_model = model('common/category');
	}


	function index(){
		$category_id = input('param.category_id');
		$page = $this->model->get_details($category_id);
		$breadcrumb = $this->category_model->breadcrumb($category_id);
		$second_categorys = $this->category_model->get_second_categorys($category_id);
		$this->seo['title'] = $this->categorys[$category_id]['name'].'-'.$this->seo['title'];
		if($this->categorys[$category_id]['meta_keywords']){ $this->seo['keywords'] = $this->categorys[$category_id]['meta_keywords'].','.$this->seo['keywords'];}
		if($page['description']){ 
			$this->seo['description'] = $page['description'];
		}elseif($this->categorys[$category_id]['meta_description']){ 
			$this->seo['description'] = $this->categorys[$category_id]['meta_description'];
		}
		$template = $this->category_model->get_template($category_id,1);

		$categorys = $this->category_model->get_categorys('');  // 此栏目下最末层主题列表
		$new_recommends = $this->category_model->get_article_cates($categorys, true);  // 推荐的主题列表

		// 判断用户设备  跳转不同页面
		$res = $this->is_mobile();
		if ($res) {
			return view($template,['data'=>$page,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo,'recommend_cates' => $new_recommends]);
		} else {
			return view('/page/pc_index',['data'=>$page,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo,'recommend_cates' => $new_recommends]);
		}	
	}
}
