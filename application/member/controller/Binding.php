<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/9/5
 * Time: 17:38
 */
namespace app\member\controller;

use think\Request;
use think\Session;
use app\base\model\Member;
use app\index\controller\Base;

class Binding extends Base
{
    public function Tel($mid = '',$openid = '')
    {
        $site_id = $this->site_id;

        $username = $this->username ;
        $this->assign('username',$username);

        $template_path = $this->template_path;

        $this->assign('mid',$mid);

        $this->assign('openid',$openid);

        return $this->fetch($template_path);
    }

    public function save(Request $request)
    {
        $site_id = $this->site_id;

        $post_open_id = $request->param('open_id');
        $post_mobile = $request->param('mobile');
        $post_password = $request->param('password');
        $post_password = md5($post_password);
        $post_sms_code = $request->param('sms_code');
        $post_mid = $request->param('mid');
        if(empty($post_mid) or !is_numeric($post_mid)){
            $post_mid = '0';
        }

        $sms_code = Session::get('sms_code');
        if($post_sms_code != $sms_code){
            $this->error('短信验证码不正确','/member/binding/tel');
        }

        $ip = $request->ip();

        // 将会员资料写入到数据库中
        $member_data = new Member();
        $member_data['site_id'] = $site_id;
        $member_data['username'] = 'tel_'.$site_id.$post_mobile;
        $member_data['password'] = $post_password;
        $member_data['tel'] = $post_mobile;
        $member_data['ip'] = $ip;
        $member_data['status'] = 1;
        $member_data->save();
    }
}