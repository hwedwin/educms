<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/2
 * Time: 14:52
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\ArticleCategory as ArticleCategoryModel;
use app\base\controller\TemplatePath;
use app\base\controller\Upload;
use app\base\controller\SiteId;
use app\base\controller\Base;

class ArticleCategory extends Base
{
    public function index(Request $request)
    {
        $title='文章分类管理';
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

        $post_name= $request->post('title');
        if($post_name==!''){
            $data_sql['title'] =  ['like','%'.$post_name.'%'];
        }
        $data_sql['site_id'] = $site_id;
        $category_data = new ArticleCategoryModel;
        $data_list = $category_data->where($data_sql) -> order('sort asc')-> select();
        $data_count = count($data_list);

        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);

        return $this->fetch($template_path);
    }

    public function create()
    {
        $title='添加文章分类';
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

        // 获取网站分类列表
        $category_data = new ArticleCategoryModel();
        $category = $category_data->where(['site_id'=>$site_id])->select();
        $this->assign('category',$category);

        return $this->fetch($template_path);
    }

    public function save(Request $request)
    {
        $post_site_id= $request->post('site_id');

        // 获取icon文件
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon=$icon_file_info->qcloud_file($local_icon,$icon_filename);
        }

        // 获取 分类略缩图 thumb文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb=$thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }


        $post_sort= $request->post('sort');
        if($post_sort==''){
            $post_sort=0;
        }
        $post_title= $request->post('title');
        $post_short_title= $request->post('short_title');
        $post_keywords= $request->post('keywords');
        $post_desc= $request->post('desc');

        $post_parent_id= $request->post('parent_id');
        $post_category_template_id= $request->post('category_template_id');
        $post_article_template_id= $request->post('article_template_id');

        $post_redirect_url= $request->post('redirect_url');
        $post_body= $request->post('body');
        $post_unique_code = $request->post('unique_code');

        $post_status= $request->post('status');

        if($post_title==''){
            $this->error('分类名称不能为空');
        }
        $user = new ArticleCategoryModel;
        $user['title'] = $post_title;
        $user['short_title'] = $post_short_title;
        $user['keywords'] = $post_keywords;
        $user['desc'] = $post_desc;

        $user['parent_id'] = $post_parent_id;
        $user['site_id'] = $post_site_id;
        $user['category_template_id'] = $post_category_template_id;
        $user['article_template_id'] = $post_article_template_id;

        $user['redirect_url'] = $post_redirect_url;
        $user['body'] = $post_body;
        $user['unique_code'] = $post_unique_code;

        $user['sort'] = $post_sort;


        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }

        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增分类成功', '/admin/article_category/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function edit($id)
    {
        $title='编辑文章分类';
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

        // 获取网站信息
        $data_list = ArticleCategoryModel::get($id);
        $this->assign('data_list',$data_list);
        return $this->fetch($template_path);
    }

    public function update(Request $request)
    {
        $post_id= $request->post('id');

        // 获取icon文件
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon=$icon_file_info->qcloud_file($local_icon,$icon_filename);
        }

        // 获取 分类略缩图 thumb文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb=$thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        $post_sort= $request->post('sort');
        if($post_sort==''){
            $post_sort=0;
        }
        $post_title= $request->post('title');
        $post_short_title= $request->post('short_title');
        $post_keywords= $request->post('keywords');
        $post_desc= $request->post('desc');

        $post_parent_id= $request->post('parent_id');
        $post_category_template_id= $request->post('category_template_id');
        $post_article_template_id= $request->post('article_template_id');

        $post_redirect_url= $request->post('redirect_url');
        $post_body= $request->post('body');
        $post_unique_code= $request->post('unique_code');

        $post_status= $request->post('status');

        if($post_title==''){
            $this->error('分类名称不能为空');
        }

        $user = ArticleCategoryModel::get($post_id);
        $user['title'] = $post_title;
        $user['short_title'] = $post_short_title;
        $user['keywords'] = $post_keywords;
        $user['desc'] = $post_desc;

        $user['parent_id'] = $post_parent_id;
        $user['category_template_id'] = $post_category_template_id;
        $user['article_template_id'] = $post_article_template_id;

        $user['redirect_url'] = $post_redirect_url;
        $user['body'] = $post_body;
        $user['unique_code'] = $post_unique_code;

        $user['sort'] = $post_sort;
        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        if(!empty($post_banner)){
            $user['banner'] = $post_banner;
        }

        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('保存分类信息成功', '/admin/article_category/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function delete($id)
    {
        $user = ArticleCategoryModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除分类成功', '/admin/article_category/index');
        } else {
            $this->error('您要删除的分类不存在');
        }
    }

}