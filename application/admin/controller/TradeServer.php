<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2016/11/11
 * Time: 9:10
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\TradeServer as TradeServerModel;

class TradeServer extends Base
{
    public function index(Request $request)
    {
        $title='交易平台服务器管理';
        $this->assign('title',$title);

        $post_server_name= $request->post('server_name');
        $trade_data = new TradeServerModel();

        if(empty($post_server_name)){
            $data_list = $trade_data->where(['status'=>1]) -> select();
        }else{
            $data_list = $trade_data->where(['status'=>1,'server_name'=>['like','%'.$post_server_name.'%']]) -> select();
        }

        $data_count = count($data_list);

        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);

        return $this->fetch();
    }

    public function add()
    {
        $title='添加交易平台服务器';
        $this->assign('title',$title);

        return $this->fetch();
    }

    public function insert(Request $request)
    {
        $post_platform= $request->post('platform');
        $post_server_name= $request->post('server_name');
        $post_short_name= $request->post('short_name');
        $post_ip= $request->post('ip');
        $post_status= $request->post('status');
        if($post_platform=='' or $post_server_name==''){
            $this->error('交易平台名称和交易服务器名称不能为空');
        }
        $user = new TradeServerModel;
        $user['platform'] = $post_platform;
        $user['server_name'] = $post_server_name;
        $user['short_name'] = $post_short_name;
        $user['ip'] = $post_ip;
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('新增交易服务器成功', '/admin/tradeserver/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function edit($id)
    {
        $title='编辑 交易平台服务器';
        $this->assign('title',$title);

        $data_list = TradeServerModel::get($id);
        $this->assign('data_list',$data_list);
        return $this->fetch();
    }

    public function save(Request $request)
    {
        $post_id= $request->post('id');
        $post_platform= $request->post('platform');
        $post_server_name= $request->post('server_name');
        $post_short_name= $request->post('short_name');
        $post_ip= $request->post('ip');
        $post_status= $request->post('status');
        if($post_id==''){
            $this->error('用户不能为空');
        }

        $user = TradeServerModel::get($post_id);
        $user['platform'] = $post_platform;
        $user['server_name'] = $post_server_name;
        $user['short_name'] = $post_short_name;
        $user['ip'] = $post_ip;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('交易服务器信息保存成功', '/admin/tradeserver/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function delete($id)
    {
        $user = TradeServerModel::get($id);
        if ($user) {
            $user->delete();
            $this->success('删除交易服务器成功', '/admin/tradeserver/index');
        } else {
            $this->error('您要删除的交易服务器不存在');
        }
    }

}