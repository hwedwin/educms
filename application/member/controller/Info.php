<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/23
 * Time: 5:47
 */
namespace app\member\controller;

use app\index\controller\Base;
use app\base\controller\BrowserCheck;
use app\base\controller\Weixin;
use app\base\model\MemberWeixin;
use app\base\model\Member;

class Info extends Base
{
    public function index()
    {
        $username = $this->username ;
        $this->assign('username',$username);

        $site_id = $this->site_id;
        $template_path = $this->template_path;
        $mid = $this->mid;
        $this->assign('mid',$mid);

        // 判断是否为微信浏览器
        $user_browser = new BrowserCheck();
        $user_browser_info = $user_browser->info();
        if($user_browser_info=='wechat_browser'){
            $weixin_user_info = new Weixin();
            $openid = $weixin_user_info->info($site_id,$mid);
            $this->assign('openid',$openid);
            // 获取会员信息
            $member_weixin_info = MemberWeixin::get(['openid'=>$openid]);
            $member_weixin_id = $member_weixin_info['id'];
            $member_info = Member::get(['weixin_id'=>$member_weixin_id]);
            if(!empty($member_info)){
                $member_info['name'] = $member_info['real_name'];
                $this->assign('member_data',$member_info);
            }else{
                $member_weixin_info['name'] = $member_weixin_info['nickname'];
                $member_weixin_info['tel'] = '';
                $this->assign('member_data',$member_weixin_info);
            }
            return $this->fetch($template_path);
        }

        return $this->fetch($template_path);
    }
}