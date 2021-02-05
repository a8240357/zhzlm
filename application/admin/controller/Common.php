<?php
namespace app\cms\controller;
use think\Controller;
use think\Session;
use think\Db;
use think\Loader;

class Common extends Controller
{
    public $subActive;
    public function _initialize()
    {
        // 2019 2 27 new
        $admin_id = session::get('admin_id');
       $session_id = db('lgadmin_admin')->where('id',$admin_id)->field('session_id')->value('session_id');
       if ($session_id != session_id()) {
           // 清空session
           Session::delete('admin_id');
           Session::delete('admin_gh');
           Session::delete('admin_name');
           Session::delete('admin_tell');
           Session::delete('admin_role_id');
           Session::delete('admin_type_id');
           Session::delete('admin_pic');
           Session::delete('ip');
           Session::delete('ip_date');
           // 退出
           $this->error('您的账号在其他地方登录，您已被迫下线！','/cmslogin');
       }
        //引用扩展类
        Loader::import("auth/Auth", EXTEND_PATH);
        $auth=new \Auth();
        //当前操作
        $this->current_action = request()->module().'/'.request()->controller().'/'.lcfirst(request()->action());
        $this->current_action = strtolower($this->current_action);
        //检查是否有权限
        //站点   1：供货商系统  2：仓储系统  3：物流系统 4：零售系统   5：电商后台系统
        $site= 5;
        //var_dump($site,$admin_id,$this->current_action);die; session('admin_user')['id']
        $auth_result = $auth->check($this->current_action,$admin_id,$site);
        if($auth_result == false){
//            echo "<script>";
//            echo "alert('权限不足！');";
//            echo "window.history.back()";
//            echo "</script>";
            $this->error('您没有权限访问');
            die();
        }
    }


    // 获取后台窗口顶部标题
    public function getTitle()
    {
        //查询底部公共信息
        $site = Db::name('Config')->find();
        return $site;
    }


    /***
     * 公共权限方法判断当前栏目是否高亮
     * @param $url
     * @param $dir_res
     * @param $beUrl 栏目所关联的页面内的url以逗号隔开的
     * @return string
     */
    public function isActive($url, $dir_res, $beUrl)
    {
        $url = strtolower($url);
        $beUrl = strtolower($beUrl);
        $beUrlArrey = explode(",", $beUrl);
        if($url == $dir_res || in_array($dir_res, $beUrlArrey)){
            return 'active';
        }else if (empty($dir_res)) {
            return 'sub-menu open';
        } else {
            return  '';
        }
    }

    /***
     * 获取当前控制器方法名称
     * @return string
     */
    public function dirRes ()
    {
        $yi = request()->module();
        $er = request()->controller();
        $san = request()->action();
        $dir_res = '/'.$yi . '/' . lcfirst($er) . '/' . $san;
        return $dir_res;
    }

    /***
     * 小栏目遍历
     * @param $subMenu
     * @param $role_url
     * @return string
     */
    public function createSubMenu($subMenu, $role_url, $dir_res)
    {
        $this->subActive = '';
        if(!empty($subMenu)) {
            $lis = '';
            foreach ($role_url as $value) {
                $value = '/'.$value;
                foreach($subMenu as $k => $v) {
                    if($v['column_url'] == $value) {
                        $this->subActive = $this->subActive ? $this->subActive : $this->isActive($value, $dir_res, $v['column_urls']);
                        $lis .= "<li class='". $this->isActive($value, $dir_res, $v['column_urls']) ."'>
                            <a href=". ($v['column_url'] ? $v['column_url'] : 'javascript:;') .">
                                <i class=''></i>
                                ". $v['column_name'] ."
                            </a>

                            <b class='arrow'></b>

                        </li>";
                    }
                }
            }
            return "<ul class='sub'>". $lis ."</ul>";
        } else {
            return '';
        }
    }


    public function type_nav()
    {
        //admin
        $role_id = session('admin_id');
        if($role_id=='')
        {
            $this->error('请先登录','/passway');
            exit();
        }
        // 获取admin_id后进行权限查询 并且切分成数组
        $map=['b.uid'=>$role_id,'b.site'=>5];
        $rules_id = Db::name('auth_group')->alias('a')->join('auth_group_access b','a.id = b.group_id')->where($map)->value('rules');
        //explode字符串->数组
        $rules_id = explode(',', $rules_id);
        //所拥有的权限
        $rules = Db::name('auth_rule')->where('id','in',$rules_id)->column('name');
        // 获取当前控制器方法名称
        $dir_res = $this->dirRes();
        // 查询后台栏目
        $p_column = Db::name('lgadmin_column')->where('pid = 0')->order('column_sort', 'asc')->select();
        foreach ($p_column as $key => $val) {
            // 查询每个大栏目下的小栏目
            $column = Db::name('lgadmin_column')->where('pid ='. $val['id'])->order('column_sort', 'asc')->select();
            $p_column[$key] = $val;
            $p_column[$key]['column'] = $column;
        }
//        echo '<pre />';
//        var_dump($rules);die;
        // html字符串
        $nav_res = "<style></style>";
        // 存放的是已经遍历过的菜单的id
        $isCreateArr = [];
        foreach ($p_column as $k => $v) {

            foreach($rules as $value) {
                $val = $value;
                $value = '/'.$value;
                if($v['column_url'] == $value) {
                    $nav_res .= "<li class='". $this->isActive($value, $dir_res, $v['column_urls']) ."'>
                        <a href=". ($v['column_url'] ? $v['column_url'] : 'javascript:;') .">
                            <i class='". $v['column_icon'] ."'></i>
                            <span> ". $v['column_name'] ." </span>
                        </a>
                        <b class='arrow'></b>
                    </li>";
                    break;
                }
                else if ($val == $v['column_url'] && $v['pid'] == 0) {
                    if( !in_array($v['id'], $isCreateArr) ) {
                        $isCreateArr[] = $v['id'];
                        $subMenu = $this->createSubMenu($v['column'], $rules, $dir_res);
                        $className = $this->isActive($value, $dir_res, $v['column_urls']);
                        $nav_res .= "<li class='". ($this->subActive ? 'active sub-menu open': 'sub-menu') ."'>
                            <a class='' href=". ('javascript:;') ." >
                                <i class='". $v['column_icon'] ."'></i>
                                <span> ". $v['column_name'] ." </span>
                                <span class='arrow '></span>
                            </a>
                            ". $subMenu ."
                         </li>";
                        break;
                    }
                }
            }
        }


        return $nav_res;
    }
}
