<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/29
 * Time: 22:22
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\WechatApplet as WechatAppletModel;
use app\base\controller\TemplatePath;
use app\base\controller\Base;
use app\base\controller\SiteId;

class WechatApplet extends Base
{
    public function index(Request $request)
    {
        // 给当页面标题赋值
        $title = '微信小程序';
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
        $data = new WechatAppletModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$data_list);

        return $this->fetch($template_path);
    }

    public function create()
    {
        $title = '添加小程序';
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

        return $this->fetch($template_path);
    }

    public function save(Request $request)
    {
        $post_site_id = $request->post('site_id');
        $post_title = $request->post('title');
        $post_status= $request->post('status');

        if($post_title==''){
            $this->error('标题不能为空');
        }
        $user = new WechatAppletModel;
        $user['site_id'] = $post_site_id;
        $user['title']    = $post_title;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增成功', '/admin/wechat_applet/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function edit($id)
    {
        $title = '编辑小程序';
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

        // 获取信息
        $data_list = WechatAppletModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($template_path);
    }

    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_site_id = $request->post('site_id');
        $post_title = $request->post('title');
        $post_status= $request->post('status');

        if($post_title=='' or $post_id==''){
            $this->error('幻灯片名称不能为空');
        }

        $user = WechatAppletModel::get($post_id);
        $user['site_id'] = $post_site_id;
        $user['title']    = $post_title;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('保存成功', '/admin/wechat_applet/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function delete($id)
    {
        $user = WechatAppletModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除成功', '/admin/wechat_applet/index');
        } else {
            $this->error('删除失败');
        }
    }

}