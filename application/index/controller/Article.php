<?php
namespace app\index\controller;

/**
* 文章控制器类
*/
class Article extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/article');
		$this->category_model = model('common/category');
		$this->banner_model = model('common/banner');
	}

	function index(){
		$category_id = input('param.category_id');
		$category_ids = $this->category_model->get_category_ids($category_id);//获取所有子分类id
		$articles = $this->model->get_list(['category_id'=>['IN',$category_ids], 'is_theme' => 0, 'is_use' => 1],'is_top desc,id desc', config('article_count'),1);
		$recommend_list = $this->model->get_list(['category_id'=>['IN',$category_ids],'is_recommend'=>'1'],'id desc',10); //推荐文章
		$new_list = $this->model->get_list(['category_id'=>['IN',$category_ids]],['id desc'],10); //最新文章 
		$hot_list = $this->model->get_list(['category_id'=>['IN',$category_ids]],['hits desc'],10); //热门文章
		$breadcrumb = $this->category_model->breadcrumb($category_id);
		$second_categorys = $this->category_model->get_second_categorys($category_id);
		$this->seo['title'] = $this->categorys[$category_id]['name'].'-'.$this->seo['title'];
		if($this->categorys[$category_id]['meta_keywords']){ $this->seo['keywords'] = $this->categorys[$category_id]['meta_keywords'].','.$this->seo['keywords'];}
		if($this->categorys[$category_id]['meta_description']){ $this->seo['description'] = $this->categorys[$category_id]['meta_description'];}
		$template = $this->category_model->get_template($category_id,1);

		$categorys = $this->category_model->get_categorys($category_id);  // 最末层主题列表
		$new_recommends = $this->category_model->get_article_cates($categorys, true);  // 推荐的主题列表

		$banners = $this->banner_model->get_list(['is_use' => 1, 'position' => 'list', 'category_id' => $category_id], 'sort asc');  // 轮播图

		// 判断用户设备  跳转不同页面
		$res = $this->is_mobile();
		if ($res) {
			return view('/mobile/index', ['lists'=>$articles,'page'=>$articles->render(),'recommend_list'=>$recommend_list,'hot_list'=>$hot_list,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo, 'recommend_cates' => $new_recommends, 'banners' => $banners, 'cate_id'=>$category_id]);
		} else {
			return view($template,['lists'=>$articles,'page'=>$articles->render(),'recommend_list'=>$recommend_list,'hot_list'=>$hot_list,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo, 'recommend_cates' => $new_recommends, 'banners' => $banners, 'cate_id'=>$category_id]);
		}
	}

	function lists(){
		$category_id = input('param.category_id', NULL);
		$articles = $this->model->get_list(['category_id'=>$category_id, 'is_theme' => 0, 'is_use' => 1],'is_top desc,id desc', config('article_count'),1);
		$recommend_list = $this->model->get_list(['category_id'=>$category_id,'is_recommend'=>'1'],'id desc',10); //推荐文章
		$hot_list = $this->model->get_list(['category_id'=>$category_id],['hits desc'],10); //热门文章
		$breadcrumb = $this->category_model->breadcrumb($category_id);
		$second_categorys = $this->category_model->get_second_categorys($category_id);
		$this->seo['title'] = $this->categorys[$category_id]['name'].'-'.$this->seo['title'];
		if($this->categorys[$category_id]['meta_keywords']){ $this->seo['keywords'] = $this->categorys[$category_id]['meta_keywords'].','.$this->seo['keywords'];}
		if($this->categorys[$category_id]['meta_description']){ $this->seo['description'] = $this->categorys[$category_id]['meta_description'];}
		$template = $this->category_model->get_template($category_id,2);

		$categorys = $this->category_model->get_categorys($category_id);  // 此栏目下最末层主题列表
		$new_recommends = $this->category_model->get_article_cates($categorys, true);  // 推荐的主题列表

		$banners = $this->banner_model->get_list(['is_use' => 1, 'position' => 'list', 'category_id' => $category_id], 'sort asc');  // 轮播图

		// 判断用户设备  跳转不同页面
		$res = $this->is_mobile();
		if ($res) {
			return view('/mobile/index', ['lists'=>$articles,'page'=>$articles->render(),'recommend_list'=>$recommend_list,'hot_list'=>$hot_list,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo, 'recommend_cates' => $new_recommends, 'banners' => $banners, 'cate_id'=>$category_id]);
		} else {
			return view($template,['lists'=>$articles,'page'=>$articles->render(),'recommend_list'=>$recommend_list,'hot_list'=>$hot_list,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo, 'recommend_cates' => $new_recommends, 'banners' => $banners, 'cate_id'=>$category_id]);
		}
	}

	function show(){
		$id = input('param.id');
		$article = $this->model->get_details($id);
		$this->model->where('id', $id)->setInc('hits'); //点击量自增一
		$article['hits'] = $article['hits']+1; //点击量加一
		$hot_list = $this->model->get_list(['category_id'=>$article['category_id']],['hits desc'],10); //热门文章
		$breadcrumb = $this->category_model->breadcrumb($article['category_id']).'<a><cite>'.$article['title'].'</cite></a>';
		$second_categorys = $this->category_model->get_second_categorys($article['category_id']);
		$this->seo['title'] = $article['title'].'-'.$this->seo['title'];
		if($article['keywords']){ $this->seo['keywords'] = $article['keywords'].','.$this->seo['keywords'];}
		if($article['description']){ $this->seo['description'] = $article['description'];}

		$category_id = input('cate_id', NULL);
		$template = $this->category_model->get_template($category_id,3);

		$categorys = $this->category_model->get_categorys($category_id);  // 最末层主题列表
		$new_recommends = $this->category_model->get_article_cates($categorys, true);  // 推荐的主题列表

		$content_length = config('content_length');

		// 判断用户设备  跳转不同页面
		$res = $this->is_mobile();
		if ($res) {
			return view('/mobile/detail',['data'=>$article,'hot_list'=>$hot_list,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo,'recommend_cates' => $new_recommends, 'content_length' => $content_length]);
		} else {
			return view($template,['data'=>$article,'hot_list'=>$hot_list,'breadcrumb'=>$breadcrumb,'second_categorys'=>$second_categorys,'seo'=>$this->seo,'recommend_cates' => $new_recommends, 'content_length' => $content_length]);
		}
	}


	// 主题列表
	public function theme_list () {
		$cate_id = input('cate_id');
		$categorys = $this->category_model->get_categorys($cate_id);  // 最末层主题列表
		$article_categorys = $this->category_model->get_article_cates($categorys);  // 文章主题列表
		$new_recommends = $this->category_model->get_article_cates($categorys, true);  // 推荐的主题列表
		// 顶部的一级栏目列表
		$lanmu = $this->category_model->get_second_categorys();
		foreach($lanmu as $k => $v) {
			if ($v['model_code'] != 'article' || $v['is_use'] == 0) {
				unset($lanmu[$k]);
			}
		}

		// 判断用户设备  跳转不同页面
		$res = $this->is_mobile();
		if ($res) {
			return view('/mobile/list', ['data'=>$article_categorys, 'recommend_cates' => $new_recommends, 'lanmu' => $lanmu, 'cate_id'=>$cate_id]);
		} else {
			return view('/article/theme', ['data'=>$article_categorys, 'recommend_cates' => $new_recommends, 'lanmu' => $lanmu, 'cate_id'=>$cate_id]);
		}
	}

	// 通过主题列表进去的文章列表
	public function theme_art_list () {
		$category_id = input('cate_id');
		$articles = $this->model->get_list(['category_id'=>$category_id, 'is_use' => 1],'is_top desc,id desc',config('article_count'),1); // 文章
		$categorys = $this->category_model->get_categorys($category_id);  // 最末层主题列表
		$new_recommends = $this->category_model->get_article_cates($categorys, true);  // 推荐的主题列表
		$breadcrumb = $this->category_model->breadcrumb($category_id); // 位置  ’‘’-‘’‘

		$banners = $this->banner_model->get_list(['is_use' => 1, 'position' => 'list', 'category_id' => $category_id], 'sort asc');  // 轮播图
		// 判断用户设备  跳转不同页面
		$res = $this->is_mobile();
		if ($res) {
			return view('/mobile/theme_art_list', ['lists'=>$articles,'page'=>$articles->render(),'seo'=>$this->seo, 'recommend_cates' => $new_recommends,'breadcrumb'=>$breadcrumb, 'banners'=>$banners]);
		} else {
			return view('/article/theme_article_list', ['lists'=>$articles,'page'=>$articles->render(),'seo'=>$this->seo, 'recommend_cates' => $new_recommends,'breadcrumb'=>$breadcrumb, 'banners'=>$banners]);
		}
	}

	// 测试
	public function test () {

		echo THINK_VERSION;die;
		$category_id = input('cate_id', NULL);
		$categorys = $this->category_model->get_category_tree($category_id);  // 最末层主题列表

		$data = $this->getSon($categorys);
		halt($data);
	}

	

	


}
