{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <li class="layui-this">修改banner</li>
      <div class="main-tab-item">banner管理</div>
    </ul> 
    <div class="layui-tab-content"> 
       <form class="layui-form">
        <div class="layui-tab-item layui-show">
          <input type="hidden" name="id" value="<?php echo $banner['id'] ?>">

          <div class='layui-form-item'>
            <label class="layui-form-label">所在页面</label>
            <div class="layui-input-inline input-custom-width"  id="IsPurchased" >
              <input type="radio" name="position" value="home" title="首页 <span style='color:#ccc;'>[首页不能选择所属栏目]</span>" lay-filter="position" <?php if($banner['position'] == 'home') echo 'checked'?>>
              <input type="radio" name="position" value="list" title="列表页" lay-filter="position" <?php if($banner['position'] == 'list') echo 'checked'?>>
            </div>
          </div>
          <div class='count' style="display: <?php if($banner['position'] == 'home') echo 'none'?>">
            <?php echo Form::select_no_option('category_id',$banner['category_id'],'所属栏目','',$model_category_select_option,'required');?>
          </div>
          
          <?php echo Form::input('text','title',$banner['title'],'标题','','请输入标题','required');?>
          <?php echo Form::file('image_url',$banner['image_url'],'图片','','','images','','图片','image');?>
          <?php // echo Form::radio('is_recommend',$banner['is_recommend'],'是否推荐','用于前台推荐调用',array(1=>'是',0=>'否'));?>
          <?php echo Form::radio('is_use',$banner['is_use'],'是否启用','用于banner是否显示',array(1=>'是',0=>'否'));?>
          <div style="display:none">
            <?php if($settings['editor'] && ($settings['editor']=='umeditor')){
                  echo Form::umeditor('content',$banner['content'],'内容');
            }else{
                  echo Form::layedit('content',$banner['content'],'内容','','请输入内容','layedit','content');
            } ?>
          </div>
          
          <?php echo Form::date('create_time',$banner['create_time'],'添加时间','');?>
          <?php echo Form::input('text','sort','0','排序','','请输入排列序号','');?>
          <?php echo Form::input('text','url',$banner['url'],'链接地址','','请输入链接地址','required');?>
          
          <div class="layui-form-item">
            <div class="layui-input-block">
              <button class="layui-btn" lay-submit="" lay-filter="article_edit">立即提交</button>
            </div>
          </div>
        </div>   
      </form>
    </div>
</div>
<script type="text/javascript">
layui.use(['element', 'form', 'upload', 'layedit', 'laydate'], function(){
  var element = layui.element()
  ,form = layui.form()
  ,layedit = layui.layedit
  ,laydate = layui.laydate
  ,jq = layui.jquery;

  //创建一个编辑器
  var content = layedit.build('content',{
    uploadImage: { url: '<?php echo url("upload/layedit_upimage") ?>' ,type: 'post'  }
    ,height: 400
  });
  //表单验证
  form.verify({
    //编辑器数据同步
    layedit: function(value){
      layedit.sync(content);
    }
  });
  //图片上传
  layui.upload({
    url: '<?php echo url("upload/upimage") ?>'
    ,elem:'#image'
    ,before: function(input){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
    }
    ,success: function(res){
      layer.close(loading);
      jq('input[name=image_url]').val(res.path);
      layer.msg(res.msg, {icon: 1, time: 1000});
    }
  }); 
  //图片预览
  jq('input[name=image_url]').hover(function(){
    jq(this).after('<img class="input-img-show" src="'+jq(this).val()+'" >');
  },function(){
    jq(this).next('img').remove();
  });
  
  //监听提交
  form.on('submit(article_edit)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("banner/edit")}',param,function(data){
      if(data.code == 200){
        layer.close(loading);
        layer.msg(data.msg, {icon: 1, time: 1000}, function(){
          location.reload();//do something
        });
      }else{
        layer.close(loading);
        layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
      }
    });
    return false;
  });

  //获取标题分词
  jq('input[name=title]').blur(function(){
    if(!jq('input[name=keywords]').val()){
      var title = jq(this).val();
      var param = {'source':title}
      jq.get('{:url("setting/get_keywords")}',param,function(data){
        if(data.code == 200){
          jq('input[name=keywords]').val(data.keywords);
        }
      });
    }
  });

  // 显示隐藏栏目选项  当轮播位置在首页时隐藏栏目  选择列表页时显示栏目
  form.on('radio(position)', function (data) {        
           
           if ($('#IsPurchased input[name="position"]:checked ').val() == "home") {
               $(".count").hide();
           }
           else {
               $(".count").show();
           }
           form.render();
       });
  
  
})
</script>

{include file="public/footer" /}