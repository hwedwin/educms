<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/25
 * Time: 17:40
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\Slide as SlideModel;
use app\base\model\SlideCategory;
use app\base\controller\TemplatePath;
use app\base\controller\Base;
use app\base\controller\SiteId;
use app\base\controller\Upload;

class Slide extends Base
{
    public function index(Request $request)
    {
        // 给当页面标题赋值
        $title = '幻灯片列表';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = Request::instance()->controller();
        $action_name = Request::instance()->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取网站id
        $get_domain = Request::instance()->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);

        // 找出列表数据
        $post_title = $request->param('title');
        $data = new SlideModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach ($data_list as $data){
            $category_id = $data['category_id'];
            $category_data = SlideCategory::get($category_id);
            $category_title = $category_data['title'];
            $data['category_title'] = $category_title;
        }

        $this->assign('data_list',$data_list);

        return $this->fetch($template_path);
    }

    public function create()
    {
        $title = '幻灯片';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = Request::instance()->controller();
        $action_name = Request::instance()->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取网站id
        $get_domain = Request::instance()->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);
        $this->assign('site_id',$site_id);

        // 获取分类列表
        $category_data = new SlideCategory();
        $category = $category_data->where(['site_id'=>$site_id])->select();
        $this->assign('category',$category);

        return $this->fetch($template_path);
    }

    public function save(Request $request)
    {
        // 获取上传的文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb = $thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        // 获取上传略缩图的文件
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon = $icon_file_info->qcloud_file($local_icon,$icon_filename);
        }

        $post_site_id = $request->post('site_id');
        $post_category_id = $request->post('category_id');
        $post_title = $request->post('title');
        $post_desc = $request->post('desc');
        $post_sort= $request->post('sort');
        $post_sort=(int)$post_sort;
        $post_button_text= $request->post('button');
        $post_url= $request->post('url');
        $post_status= $request->post('status');

        if($post_title==''){
            $this->error('幻灯片标题不能为空');
        }
        $user = new SlideModel;
        $user['site_id'] = $post_site_id;
        $user['category_id'] = $post_category_id;
        $user['title']    = $post_title;
        $user['desc'] = $post_desc;
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        $user['sort']    = $post_sort;
        $user['button'] = $post_button_text;
        $user['url'] = $post_url;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增幻灯片成功', '/admin/slide/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function edit($id)
    {
        $title = '编辑幻灯片';
        $this->assign('title',$title);

        // 当前方法不同终端的模板路径
        $controller_name = Request::instance()->controller();
        $action_name = Request::instance()->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->admin_path($controller_name,$action_name);
        $template_public = $template_path_info->admin_public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        // 获取网站id
        $get_domain = Request::instance()->server('HTTP_HOST');
        $this->assign('domain',$get_domain);
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);
        $this->assign('site_id',$site_id);
        // 获取当前分类id
        $categorg_id_info = SlideModel::get($id);
        $categorg_id = $categorg_id_info['category_id'];

        // 获取信息
        $data_list = SlideModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new SlideCategory();
        $category = $category_data->where(['site_id'=>$site_id])->select();
        $this->assign('category',$category);

        $my_categorg_data = SlideCategory::get($categorg_id);
        $my_categorg_title = $my_categorg_data['title'];
        $this->assign('my_category_id',$categorg_id);
        $this->assign('my_categorg_title',$my_categorg_title);

        return $this->fetch($template_path);
    }

    public function update(Request $request)
    {
        // 获取上传的文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb = $thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        // 获取上传略缩图的文件
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon = $icon_file_info->qcloud_file($local_icon,$icon_filename);
        }

        $post_id = $request->post('id');
        $post_site_id = $request->post('site_id');
        $post_category_id = $request->post('category_id');
        $post_title = $request->post('title');
        $post_desc = $request->post('desc');
        $post_sort= $request->post('sort');
        $post_sort=(int)$post_sort;
        $post_button_text= $request->post('button');
        $post_url= $request->post('url');
        $post_status= $request->post('status');
        if($post_title=='' or $post_id==''){
            $this->error('幻灯片名称不能为空');
        }

        $user = SlideModel::get($post_id);
        $user['site_id'] = $post_site_id;
        $user['category_id'] = $post_category_id;
        $user['title']    = $post_title;
        $user['desc'] = $post_desc;
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        $user['sort']    = $post_sort;
        $user['button'] = $post_button_text;
        $user['url'] = $post_url;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('保存幻灯片信息成功', '/admin/slide/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function delete($id)
    {
        $user = SlideModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除幻灯片成功', '/admin/slide/index');
        } else {
            $this->error('您要删除的幻灯片不存在');
        }
    }

}