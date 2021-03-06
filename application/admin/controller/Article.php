<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/2
 * Time: 14:48
 */
namespace app\admin\controller;

use think\Request;
use think\Session;
use app\base\model\Article as ArticleModel;
use app\base\model\ArticleCategory;
use app\base\model\Admin;
use app\base\controller\Upload;
use app\base\controller\TemplatePath;
use app\base\controller\SiteId;
use app\base\controller\Base;


class Article extends Base
{
    public function index(Request $request)
    {
        $title='文章管理';
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

        $post_title= $request->post('title');
        if($post_title==!''){
            $data_sql['title'] =  ['like','%'.$post_title.'%'];
        }
        // 分页数量
        $pages=15;
        $article_list = new ArticleModel();
        $data_list =$article_list->where(['site_id'=>$site_id,'status'=>1])->order('id desc') -> paginate($pages);
        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $category_id=$data->category_id;
            $category_list = ArticleCategory::get($category_id);
            $data->category=$category_list['title'];
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);
        return $this -> fetch($template_path);
    }

    public function create()
    {
        $title='添加文章';
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

        $article_category_info = new ArticleCategory();
        $category_list = $article_category_info->where(['site_id'=>$site_id]) -> select();
        foreach($category_list as $data)
        {
            $data->id;
            $data->title;
        }
        $this->assign('category_list',$category_list);
        return $this->fetch($template_path);
    }

    public function save(Request $request)
    {
        // 获取 网站略缩图 thumb文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb=$thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        $post_site_id = $request->post('site_id');

        $admin_username=Session::get('username');
        $admin_list = Admin::get(['username'=>$admin_username]);
        $mid=$admin_list['id'];

        $post_category= $request->post('category');
        $post_template_id = $request->post('template_id');
        if($post_template_id==""){
            $category_list = ArticleCategory::get($post_category);
            $post_template_id = $category_list['article_template_id'];
        }
        $post_redirect_url= $request->post('redirect_url');
        $post_related_articles= $request->post('related_articles');

        $post_title= $request->post('title');
        $post_short_title= $request->post('short_title');


        $post_unique_code= $request->post('unique_code');
        if($post_unique_code==""){
            $post_unique_code= 'a'.time().rand(1000,9999);
        }
        $post_keywords= $request->post('keywords');
        $post_desc = $request->post('desc');

        $post_click= $request->post('click');
        $post_sort = $request->post('sort');
        $post_author= $request->post('author');
        $article_body = $request->post('myVent');
        $post_body = preg_replace('/mmbiz.qpic.cn\//',$_SERVER['HTTP_HOST'].'/qpic/',$article_body);

        $post_status= $request->post('status');
        if($post_title=='' or $post_category==''){
            $this->error('文章标题和分类不能为空');
        }
        $user = new ArticleModel;
        $user['title'] = $post_title;
        $user['category_id'] = $post_category;
        $user['mid'] = $mid;
        $user['site_id'] = $post_site_id;
        $user['short_title'] = $post_short_title;
        $user['unique_code'] = $post_unique_code;
        $user['keywords'] = $post_keywords;
        $user['desc'] = $post_desc;
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        $user['click'] = $post_click;
        $user['sort'] = $post_sort;
        $user['author'] = $post_author;
        $user['template_id'] = $post_template_id;
        $user['redirect_url'] = $post_redirect_url;
        $user['related_articles'] = $post_related_articles;

        $user['body'] = $post_body;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增文章成功', '/admin/article/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function edit($id)
    {
        $title='编辑文章';
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

        $article_info = new ArticleModel();
        $data_list = $article_info -> get($id);
        $this->assign('data_list',$data_list);

        $my_category_id = $data_list['category_id'];
        $this->assign('my_category_id',$my_category_id);

        $article_category_info = new ArticleCategory();
        $category_list = $article_category_info->where(['status'=>1,'site_id'=>$site_id]) -> select();

        foreach($category_list as $data)
        {
            $data->id;
            $data->title;
        }
        $this->assign('category_list',$category_list);

        return $this->fetch($template_path);
    }

    public function update(Request $request)
    {
        // 获取 网站略缩图 thumb文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb=$thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        $admin_username=Session::get('username');
        $admin_list = Admin::get(['username'=>$admin_username]);
        $mid=$admin_list['id'];

        $post_id= $request->post('id');

        $post_category= $request->post('category');
        $post_template_id= $request->post('template_id');
        if($post_template_id==""){
            $category_list = ArticleCategory::get($post_category);
            $post_template_id=$category_list['article_template_id'];
        }
        $post_redirect_url= $request->post('redirect_url');
        $post_related_articles= $request->post('related_articles');

        $post_title= $request->post('title');
        $post_short_title= $request->post('short_title');


        $post_unique_code= $request->post('unique_code');
        if($post_unique_code==""){
            $post_unique_code= 'a'.time().rand(1000,9999);
        }
        $post_keywords= $request->post('keywords');
        $post_desc = $request->post('desc');

        $post_click= $request->post('click');
        $post_sort = $request->post('sort');
        $post_author= $request->post('author');
        $article_body = $request->post('myVent');
        $post_body = preg_replace('/mmbiz.qpic.cn\//',$_SERVER['HTTP_HOST'].'/qpic/',$article_body);

        $post_status= $request->post('status');
        if($post_title=='' or $post_category==''){
            $this->error('文章标题和分类不能为空');
        }
        $user = ArticleModel::get($post_id);
        $user['title'] = $post_title;
        $user['category_id'] = $post_category;
        $user['mid'] = $mid;
        $user['short_title'] = $post_short_title;
        $user['unique_code'] = $post_unique_code;
        $user['keywords'] = $post_keywords;
        $user['desc'] = $post_desc;

        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        $user['click'] = $post_click;
        $user['sort'] = $post_sort;
        $user['author'] = $post_author;
        $user['template_id'] = $post_template_id;
        $user['redirect_url'] = $post_redirect_url;
        $user['related_articles'] = $post_related_articles;

        $user['body'] = $post_body;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存文章内容成功', '/admin/article/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function delete($id)
    {
        $user = ArticleModel::get($id);
        $user['status'] = 0;
        if ($user->save()) {
            $this->success('文章已删除', '/admin/article/recycle');
        } else {
            $this->error('操作失败');
        }
    }

    public function recycle(Request $request){
        $title='文章回收站';
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

        $post_title= $request->post('title');
        if($post_title==!''){
            $data_sql['title'] =  ['like','%'.$post_title.'%'];
        }
        // 分页数量
        $pages=15;
        $article_info = new ArticleModel();
        $data_list = $article_info->where(['status'=>0])->order('id desc')  -> paginate($pages);
        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $category_id=$data->category_id;
            $category_list = ArticleCategory::get(['id'=>$category_id]);
            $data->category=$category_list['title'];
        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);
        return $this -> fetch($template_path);
    }

    // 恢复网站
    public function recovery($id){
        $user = ArticleModel::get($id);
        $user['status'] = 1;
        if ($user->save()) {
            $this->success('文章已恢复', '/admin/article/recycle');
        } else {
            $this->error('操作失败');
        }
    }

    //永久删除
    public function del($id)
    {
        $user = ArticleModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('文章已经永久删除', '/admin/article/recycle');
        } else {
            $this->error('您要删除的文章不存在');
        }
    }

}