<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/11
 * Time: 9:11
 */
namespace app\admin\controller;

use think\Request;
use app\index\model\TradeAccountShow as TradeAccountDemoModel;
use app\index\model\TradeAccount;
use app\index\model\CommonInfo;

class TradeAccountShow extends Base
{
    public function index(Request $request)
    {
        $title='交易平台演示账户管理';
        $this->assign('title',$title);

        // 获取网站id
        $get_domain=$request->domain();
        $site_info= new CommonInfo();
        $site_id = $site_info->site_id($get_domain);

        $post_account= $request->post('account');
        if($post_account==!''){
            $account_sql['account'] =  $post_account;
            $account_list = TradeAccount::get(['account'=>$post_account]);
            $aid=$account_list['id'];
            $account_demo_sql['aid'] =  $aid;
        }

        $trade_data = new TradeAccountDemoModel();
        $data_list = $trade_data->where(['site_id'=>$site_id]) -> select();
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        foreach($data_list as $data)
        {
            $aid=$data->aid;
            $demo_list = TradeAccount::get($aid);
            $account=$demo_list['account'];
            $data->account=$account;
            $data->status;
        }

        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);

        return $this->fetch();
    }

    public function add()
    {
        $title='添加演示账户';
        $this->assign('title',$title);

        return $this->fetch();
    }

    public function insert(Request $request)
    {
        $post_account= $request->post('account');

        $account_list = TradeAccount::get(['account'=>$post_account]);
        $aid=$account_list['id'];

        $post_status= $request->post('status');

        if($post_account==''){
            $this->error('演示账户不能为空');
        }
        $user = new TradeAccountDemoModel;
        $user['aid'] = $aid;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增演示账户成功', '/admin/tradeaccountshow/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function edit($id)
    {
        $title='编辑 演示账户';
        $this->assign('title',$title);

        $data_list = TradeAccountDemoModel::get($id);
        $aid=$data_list['aid'];

        $account_list = TradeAccount::get($aid);
        $data_list['account'] = $account_list['account'];

        $this->assign('data_list',$data_list);
        return $this->fetch();
    }

    public function save(Request $request)
    {
        $post_id= $request->post('id');
        $post_account= $request->post('account');
        $account_list = TradeAccount::get(['account'=>$post_account]);
        $aid = $account_list['id'];

        $post_status= $request->post('status');
        if($post_id==''){
            $this->error('用户id不能为空');
        }

        $user = TradeAccountDemoModel::get($post_id);
        $user['aid'] = $aid;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('演示账户保存成功', '/admin/tradeaccountshow/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function delete($id)
    {
        $user = TradeAccountDemoModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除演示账户成功', '/admin/tradeaccountshow/index');
        } else {
            $this->error('您要删除的演示账户不存在');
        }
    }

}