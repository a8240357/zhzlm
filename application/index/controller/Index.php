<?php
namespace app\index\controller;

/**
* 首页类
*/
class Index extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->article_model = model('common/article');
		$this->category_model = model('common/category');
		$this->banner_model = model('common/banner');
	}

	function index(){ 
		$recommend_list = $this->article_model->get_list(['is_theme' => 0, 'is_use' => 1],'is_top desc,id desc',config('article_count'), config('is_page')); //推荐文章（首页文章列表）
		$cate_id = input('cate_id', NULL);
		$categorys = $this->category_model->get_categorys($cate_id);  // 最末层主题列表
		$new_list = $this->category_model->get_article_cates($categorys, true);  // 推荐的主题列表
		// $new_list = $this->article_model->get_list([],['id desc'],10); //  原来的最新文章  现在需要改为推荐主题
		$hot_list = $this->article_model->get_list([],['hits desc'],10); //热门文章  目前没有用
		$links = json_decode($this->settings['links'],true); //友情链接

		$banners = $this->banner_model->get_list(['is_use' => 1, 'position' => 'home'], 'sort asc');

		$categorys = $this->category_model->get_categorys($category_id);  // 最末层主题列表
		$new_recommends = $this->category_model->get_article_cates($categorys, true);  // 推荐的主题列表

		// 判断用户设备  跳转不同页面
		$res = $this->is_mobile();
		if ($res) {
			return view('/mobile/index',['links'=>$links,'recommend_list'=>$recommend_list,'page'=>config('is_page') ? $recommend_list->render() : '','new_list'=>$new_list,'hot_list'=>$hot_list, 'banners'=>$banners,'recommend_cates' => $new_recommends]);
		} else {
			return view('/pc/index',['links'=>$links,'recommend_list'=>$recommend_list,'page'=>config('is_page') ? $recommend_list->render() : '','new_list'=>$new_list,'hot_list'=>$hot_list, 'banners'=>$banners,'recommend_cates' => $new_recommends]);
		}
	}
}
