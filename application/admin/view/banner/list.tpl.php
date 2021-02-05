{include file="public/toper" /}
<div class="layui-tab layui-tab-brief main-tab-container">
    <ul class="layui-tab-title main-tab-title">
      <a href="<?php echo url('banner/add','category_id='.$category_id) ?>"><li>添加Banner</li></a>
      {if $settings['is_excel']==2 } <!--  如果改为1  则显示excel导出导入  -->
      <a target="_blank" href="<?php echo url('banner/export','category_id='.$category_id) ?>"><li>导出Excel</li></a>
      <li><form class="layui-form"><input type="file" name="file" lay-method="post" lay-type="file" lay-ext="xls|xlsx" lay-title="Excel导入" class="layui-upload-file" id="excel"></form></li>
      <a target="_blank" href="/uploads/sample/banner_sample.xls"><li>Excel导入样例</li></a>
      {/if}
      <div class="main-tab-item">Banner管理</div>
    </ul>
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
      <!-- 搜索 -->
      <form class="layui-form layui-form-pane search-form">
        <div class="layui-form-item">
          <label class="layui-form-label">标题</label>
          <div class="layui-input-inline">
            <input type="text" name="search[title]" value="<?php echo $search['title'] ?>" lay-verify="" placeholder="请输入标题搜索" autocomplete="off" class="layui-input">
          </div>
          <button class="layui-btn" lay-submit="" lay-filter="">搜索</button>
        </div>
        <!-- 每页数据量 -->
        <div class="layui-form-item page-size">
          <label class="layui-form-label total">共计 <?php echo $total; ?> 条</label>
          <label class="layui-form-label">每页数据条</label>
          <div class="layui-input-inline">
            <input type="text" name="page_size" value="<?php echo $per_page ?>" lay-verify="number" placeholder="" autocomplete="off" class="layui-input">
          </div>
          <button class="layui-btn" lay-submit="" lay-filter="">确定</button>
        </div>
      </form>
      <form class="layui-form">
      <input type="hidden" name="category_id" value="<?php echo $category_id ?>">
        <table class="list-table">
          <thead>
            <tr>
              <th style="width:40px"><input type="checkbox" name="checkAll" lay-filter="checkAll" title=" "></th>
              <th style="min-width:25px">ID</th>
              <th>标题</th>
              <th>所属栏目</th>
              <th>所在页面</th>
              <th class="can_click">
              <?php if($order['create_time'] == 'desc'){ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=asc">添加时间 ▼</a>
              <?php }elseif($order['create_time'] == 'asc'){ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=desc">添加时间 ▲</a>
              <?php }else{ ?>
                <a href="?<?php echo $url_params ?>&order[create_time]=desc">添加时间</a>
              <?php } ?>
              </th>
              <th style="text-align: center;">
                图片
              </th>
              <th style="text-align: center;">
                启用/禁用
              </th>
              <th style="width:90px">操作</th>
            </tr> 
          </thead>
          <tbody>
          <?php foreach ($banners as $v) { ?>
            <tr>
              <td><input type="checkbox" name="ids[<?php  echo $v['id'] ?>]" lay-filter="checkOne" value="<?php  echo $v['id'] ?>" title=" "></td>
              <td><?php echo $v['id'] ?></td>
              <td><?php echo $v['title']; ?>
                <!-- <a class="list-title" href="<?php // echo url('index/banner/show','id='.$v['id']) ?>" target="_blank"><?php // echo $v['title']; ?></a> -->
              </td>
              <td><?php echo $v['category_name'] ? $v['category_name'] : '首页' ?></td>
              <td>
                <?php echo $v['position'] == 'home' ? '首页' : '列表页' ?>
              </td>
              <td><?php echo $v['create_time'] ?></td>
              <td style="text-align: center;">
                <?php if($v['image_url']){ ?>
                  <a class="thumb" href="<?php echo $v['image_url'] ?>" target="_blank" thumb="<?php echo thumb($v['image_url'],200,200) ?>"><i class="layui-icon">&#xe64a;</i></a>
                <?php  } ?> 
              </td>
              <td style="text-align: center;">
              <?php if($v['is_use'] != 1){ ?>
                <a href="javascript:void(0)" class="list-switch list-switch-off is_top" data-id="<?php echo $v['id'] ?>" data-status="1" title="点击启用"><i class="layui-icon">&#x1006;</i></a>
              <?php }else{ ?>
                <a href="javascript:void(0)" class="list-switch list-switch-on is_top" data-id="<?php echo $v['id'] ?>" data-status="0" title="点击禁用"><i class="layui-icon">&#xe605;</i></a>
              <?php } ?>
              </td>
              <td style="text-align: center;">
              <a href="<?php echo url('banner/edit','id='.$v['id']) ?>" class="layui-btn layui-btn-small" title="编辑"><i class="layui-icon"></i></a>
              <a  class="layui-btn layui-btn-small layui-btn-danger del_btn" banner-id="<?php echo $v['id'] ?>" title="删除" banner-name='<?php echo $v['title'] ?>'><i class="layui-icon"></i></a>
              </td>
            </tr>
          <?php } ?>
          </tbody>
          <thead>
            <tr>
               <th colspan="1"><button class="layui-btn layui-btn-small" lay-submit lay-filter="delete">删除</button></th>
              <th colspan="8"><div id="page"></div></th>
            </tr> 
          </thead>
        </table>
      </form>
      </div>
    </div>
</div>
<script type="text/javascript">
layui.use(['element', 'laypage', 'layer', 'form', 'upload'], function(){
  var element = layui.element()
  ,jq = layui.jquery
  ,form = layui.form()
  ,laypage = layui.laypage;

  //置顶
  jq('.list-table td .is_top').click(function(){
    var id = jq(this).attr('data-id');
    var status = jq(this).attr('data-status');
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      var row = jq(this);
      jq.post('{:url("banner/to_use")}',{'id':id, 'status': status},function(data){
        if(data.code == 200){
          if(row.hasClass('list-switch-off')){
            row.removeClass('list-switch-off').find('.layui-icon').html('&#xe605;');
            row.attr('title','点击禁用');
            row.attr('data-status','0');
          }else{
            row.addClass('list-switch-off').find('.layui-icon').html('&#x1006;');
            row.attr('title','点击启用');
            row.attr('data-status','1');
          }
          layer.close(loading);
          layer.msg(data.msg, {icon: 1, time: 1000});
        }else{
          layer.close(loading);
          layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
        }
      });
  });


  //excel导入
  // layui.upload({
  //   url: '<?php echo url("upload/upfile") ?>'
  //   ,elem:'#excel'
  //   ,before: function(input){
  //     loading = layer.load(2, {
  //       shade: [0.2,'#000'] //0.2透明度的白色背景
  //     });
  //   }
  //   ,success: function(res){
  //     var category_id = <?php echo $category_id; ?>;
  //     var path = res.path
  //     jq.post('{:url("banner/import")}',{'category_id':category_id,'path':path},function(data){
  //       if(data.code == 200){
  //         layer.close(loading);
  //         layer.msg(data.msg, {icon: 1, time: 1000}, function(){
  //           location.reload();//do something
  //         });
  //       }else{
  //         layer.close(loading);
  //         layer.msg(data.msg, {icon: 2, anim: 6, time: 1000});
  //       }
  //     });
  //   }
  // }); 

  //图片预览
  jq('.list-table td .thumb').hover(function(){
    jq(this).append('<img class="thumb-show" src="'+jq(this).attr('thumb')+'" >');
  },function(){
    jq(this).find('img').remove();
  });

  //ajax删除
  jq('.del_btn').click(function(){
    var name = jq(this).attr('banner-name');
    var id = jq(this).attr('banner-id');
    layer.confirm('确定删除【'+name+'】?', function(index){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      jq.post('{:url("banner/del")}',{'id':id},function(data){
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
    });
    
  });
  
  //全选
  form.on('checkbox(checkAll)', function(data){
    if(data.elem.checked){
      jq("input[type='checkbox']").prop('checked',true);
    }else{
      jq("input[type='checkbox']").prop('checked',false);
    }
    form.render('checkbox');
  });  

  form.on('checkbox(checkOne)', function(data){
    var is_check = true;
    if(data.elem.checked){
      jq("input[lay-filter='checkOne']").each(function(){
        if(!jq(this).prop('checked')){ is_check = false; }
      });
      if(is_check){
        jq("input[lay-filter='checkAll']").prop('checked',true);
      }
    }else{
      jq("input[lay-filter='checkAll']").prop('checked',false);
    } 
    form.render('checkbox');
  });

  //监听删除提交
  form.on('submit(delete)', function(data){
    //判断是否有选项
    var is_check = false;
    jq("input[lay-filter='checkOne']").each(function(){
      if(jq(this).prop('checked')){ is_check = true; }
    });
    if(!is_check){
      layer.msg('请选择数据', {icon: 2,anim: 6,time: 1000});
      return false;
    }
    //确认删除
    layer.confirm('确定批量删除?', function(index){
      loading = layer.load(2, {
        shade: [0.2,'#000'] //0.2透明度的白色背景
      });
      var param = data.field;
      jq.post('{:url("banner/batches_delete")}',param,function(data){
        if(data.code == 200){
          layer.close(loading);
          layer.msg(data.msg, {icon: 1, time: 1000}, function(){
            location.reload();//do something
          });
        }else{
          layer.close(loading);
          layer.msg(data.msg, {icon: 2,anim: 6, time: 1000});
        }
      });
    });
    return false;
  });

  //监听移动提交
  form.on('submit(move)', function(data){
    //判断是否有选项
    var is_check = false;
    jq("input[lay-filter='checkOne']").each(function(){
      if(jq(this).prop('checked')){ is_check = true; }
    });
    if(!is_check){
      layer.msg('请选择数据', {icon: 2,anim: 6,time: 1000});
      return false;
    }
    //移动到位置选择 var params = JSON.stringify(data.field);
    loading = layer.load(2, {
      shade: [0.2,'#000'] //0.2透明度的白色背景
    });
    var param = data.field;
    jq.post('{:url("category/model_category_select")}',param,function(data){
      layer.open({
        type: 1, 
        title: '数据移动',
        area: ['auto', '490px'],
        content: data //这里content是一个普通的String
      });
      form.render('select');//刷新重新加载select
      layer.close(loading);
      form.on('select(move)', function(data){
        param.to_category_id = data.value;
        var selected_text = jq(data.elem).find("option:selected").text();
        //确认移动
        layer.confirm('确定批量移动至【'+selected_text+'】?', function(index){
          loading = layer.load(2, {
            shade: [0.2,'#000'] //0.2透明度的白色背景
          });
          jq.post('{:url("banner/batches_move")}',param,function(data){
            if(data.code == 200){
              layer.close(loading);
              layer.msg(data.msg, {icon: 1, time: 1000}, function(){
                location.reload();//do something
              });
            }else{
              layer.close(loading);
              layer.msg(data.msg, {icon: 2,anim: 6, time: 1000});
            }
          });
        });
      });
    });

    return false;
  });



  laypage({
    cont: 'page'
    ,skip: true
    ,pages: <?php echo ceil($total/$per_page) ?> //总页数
    ,groups: 5 //连续显示分页数
    ,curr: <?php echo $current_page ?>
    ,first: 1 //将首页显示为数字1,。若不显示，设置false即可
    ,last: <?php echo ceil($total/$per_page) ?> //将尾页显示为总页数。若不显示，设置false即可
    ,jump: function(e, first){ //触发分页后的回调
      if(!first){ //一定要加此判断，否则初始时会无限刷新
        loading = layer.load(2, {
          shade: [0.2,'#000'] //0.2透明度的白色背景
        });
        location.href = '?<?php echo $url_params ?>&page='+e.curr;
      }
    }
  });
})

</script>

{include file="public/footer" /}