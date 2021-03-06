<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:18
 */
namespace app\admin\controller;

use think\Request;
use app\base\model\AreaCity as AreaCityModel;
use app\base\model\AreaProvince;
use app\base\controller\TemplatePath;
use app\base\controller\Base;

class AreaCity extends Base
{
    public function index(Request $request)
    {
        // 给当页面标题赋值
        $title = '列表';
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


        // 找出列表数据
        $pages=15;
        $post_title = $request->param('title');
        $data = new AreaCityModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->order('id desc') -> paginate($pages);
        }else{
            $data_list = $data->where(['status'=>1])->order('id desc') -> paginate($pages);
        }
        $data_count = count($data_list);

        foreach ($data_list as $data){
            $province_id = $data['province_id'];
            $state_info = AreaProvince::get($province_id);
            $data['province_title'] = $state_info['title'];
        }

        $this->assign('data_count',$data_count);
        $this->assign('data_list',$data_list);

        return $this->fetch($template_path);
    }

    public function create()
    {
        // 新增
        $title = '类目';
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

        // 获取分类列表
        $category_data = new AreaProvince();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('category',$category);

        return $this->fetch($template_path);
    }

    public function save(Request $request)
    {
        $post_province_id = $request->param('province_id');
        $post_sort = $request->param('sort');
        $post_title = $request->param('title');
        $post_zipcode = $request->param('zipcode');
        $post_status = $request->param('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }
        $data = new AreaCityModel;
        $data['title'] = $post_title;
        $data['zipcode'] = $post_zipcode;
        $data['sort'] = $post_sort;
        $data['province_id'] = $post_province_id;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/area_city/index');
        } else {
            $this->error('操作失败');
        }
    }

    public function edit($id)
    {
        $title = '编辑类目';
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

        // 获取信息
        $data_list = AreaCityModel::get($id);
        $this->assign('data',$data_list);

        // 获取分类列表
        $category_data = new AreaProvince();
        $category = $category_data->where(['status'=>1])->select();
        $this->assign('province_category',$category);

        $my_category_data = AreaProvince::get($data_list['province_id']);
        $my_category_title = $my_category_data['title'];
        $this->assign('my_province_id',$data_list['province_id']);
        $this->assign('my_province_title',$my_category_title);

        return $this->fetch($template_path);
    }

    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_province_id = $request->post('province_id');
        $post_sort = $request->post('sort');
        $post_zipcode = $request->post('zipcode');
        $post_title = $request->post('title');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('名称不能为空');
        }

        $user = AreaCityModel::get($post_id);
        $user['sort'] = $post_sort;
        $user['province_id'] = $post_province_id;
        $user['zipcode'] = $post_zipcode;
        if(!empty($post_title)){
            $user['title'] = $post_title;
        }
        $user['status'] = $post_status;

        if ($user->save()) {
            $this->success('更新成功', '/admin/area_city/index');
        } else {
            $this->error('操作失败');
        }

    }

    public function delete($id)
    {
        $data = AreaCityModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/area_city/index');
        } else {
            $this->error('删除失败');
        }
    }
}