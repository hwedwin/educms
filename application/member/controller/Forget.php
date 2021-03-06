<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/22
 * Time: 17:08
 */
namespace app\member\controller;

use think\Request;
use think\Session;
use app\index\controller\Base;
use app\base\controller\TemplatePath;
use app\base\model\Member;
use app\base\controller\SiteId;

class Forget extends Base
{
    public function index()
    {
        $title = '重置密码';
        $description = '教育培训分销系统重置密码';
        $keywords = '重置密码,教育培训分销';
        $this->assign('title',$title);
        $this->assign('description',$description);
        $this->assign('keywords',$keywords);

        // 当前方法不同终端的模板路径
        $module_name = Request::instance()->module();
        $controller_name = Request::instance()->controller();
        $action_name = Request::instance()->action();
        $template_path_info = new TemplatePath();
        $template_path = $template_path_info->info($module_name,$controller_name,$action_name);
        $template_public = $template_path_info->public_path();
        $template_public_header = $template_public.'/header';
        $template_public_footer = $template_public.'/footer';
        $this->assign('public_header',$template_public_header);
        $this->assign('public_footer',$template_public_footer);

        return $this->fetch($template_path);
    }

    public function password(Request $request)
    {
        // 获取当前域名
        $get_domain = Request::instance()->server('HTTP_HOST');
        $site_id_data = new SiteId();
        $site_id = $site_id_data->info($get_domain);

        $post_mobile = $request->param('mobile');
        $post_password = $request->param('password');
        $post_password = md5($post_password);
        $post_password2 = $request->param('password2');
        $post_password2 = md5($post_password2);
        if($post_password != $post_password2){
            $this->error('两次输入的密码不一致！');
        }
        $post_sms_code = $request->param('sms_code');

        $sms_code = Session::get('sms_code');
        if($post_sms_code != $sms_code){
            $this->error('短信验证码不正确');
        }

        $member_data = new Member;
        $member_data->where(['site_id'=>$site_id,'tel'=>$post_mobile])->update(['password' => $post_password]);

        $this->success('密码修改成功','/member/login/index');
    }
}