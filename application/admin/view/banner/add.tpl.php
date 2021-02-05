{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <a href="/admin/banner/add"><li class="layui-this">添加banner</li></a>
      <div class="main-tab-item">banner管理</div>
    </ul> 
    <div class="layui-tab-content">
       <form class="layui-form">
        <div class="layui-tab-item layui-show">

          <div class='layui-form-item'>
            <label class="layui-form-label">所在页面</label>
            <div class="layui-input-inline input-custom-width"  id="IsPurchased" >
              <input type="radio" name="position" value="home" title="首页 <span style='color:#ccc;'>[首页不能选择所属栏目]</span>" lay-filter="position" checked="">
              <input type="radio" name="position" value="list" title="列表页" lay-filter="position">
            </div>
          </div>

          
          <?php // echo Form::radio('position','home','所在页面','用于选择展示页面',array('home'=>'首页','list'=>'列表页'));?>
          <?php echo Form::input('text','title','','标题','','请输入标题','');?>

          <div class='count' style='display:none'>
            <?php echo Form::select_no_option('category_id',$banner['category_id'],'所属栏目','',$model_category_select_option,'required');?>
          </div>
          
          
          <?php echo Form::file('image_url','','图片','','','images','','图片','image');?>

          <?php // echo Form::radio('is_recommend',0,'是否推荐','用于前台推荐调用',array(1=>'是',0=>'否'));?>
          <?php echo Form::radio('is_use',1,'是否启用','用于控制banner是否显示',array(1=>'是',0=>'否'));?>
        
          <div style='display:none;'>
            <?php if($settings['editor'] && ($settings['editor']=='umeditor')){
                  echo Form::umeditor('content','','内容');
            }else{
                  echo Form::layedit('content','','内容','','请输入内容','layedit','content');
            } ?>
          </div>
          
          <?php echo Form::date('create_time',date('Y-m-d H:i:s'),'添加时间','默认是当前时间');?>
          <?php echo Form::input('text','sort','0','排序','','请输入排列序号','');?>
          <?php echo Form::input('text','url','http://','链接地址','','请输入链接地址','required');?>

          <div class="layui-form-item">
            <div class="layui-input-block">
              <button class="layui-btn" lay-submit="" lay-filter="banner_add">立即提交</button>
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
  
  //监听提交
  form.on('submit(banner_add)', function(data){
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("banner/add")}',param,function(data){
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