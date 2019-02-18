<?php
namespace app\index\controller;

class Index
{
    public function index(){
		layout(true);
		$this->display();
    }
    // 企业列表
    public function enterprise_list(){
        $enterprise=M('enterprise')->field('id,name')->select();
        jr(0,'成功',$enterprise);
    }
    // 产品系列列表
    public function series_list(){
        $eid=I('eid','');
        $page=I('page','');
        $limit=I('limit','');
        if($eid){
            $where['eid']=$eid;
            $order="orderby desc";
           // $limit=100;
        }else{
            $where="1=1";
            $order="id desc";
        }
        $series=M('product_series');
        $count=$series->where($where)->order($order)->count();
        $totalpage=ceil($count/$limit);
        $page=min($page,$totalpage);
        $page=max($page,1);
        $offset=($page-1)*$limit;
        $re=$series->where($where)->order($order)->limit($offset,$limit)->select();
       // echo M()->getLastSql();
        foreach ($re as &$v){
            $v['ent_name']=M('enterprise')->where(array('id'=>$v['eid']))->getField('name');
        }
        if($re){
            jr(0,'成功',$re,$count);
        }else{
            jr(0,'成功');
        }
      
    }
    public function exhibition_detail(){
        $exid=I('exid','');
        $pid=I('pid','');
        $where['pid']=$pid;
        $where['exid']=$exid;
        $industry=M('product_category')->where($where)->find();
        // echo M()->getLastSql();
        if($industry){
            jr(0,'',$industry);
        }else{
            jr(1,'无数据');
        }
        
    }
    public function exhibition_info(){
        $input=I('get.');
        //var_dump($input);
        if(!$input['exid']){
            $input['exid']=3;
        }
        
        if($input['id']){
            $where['pid']=$input['id'];
            $where['exid']=$input['exid'];
            $industry=M($input['table'])->where($where)->find();
           // echo M()->getLastSql();
            //var_dump($industry);
            $this->assign('product',$industry);
        }
        $this->display();
    }
    //删除系列
    public function del_series(){
        $series_id=I('series_id','');
        $product=M('product');
        $series=M('product_series');
        $series->where(array('id'=>$series_id))->delete();
        jr(0,'删除成功');
    }
    //修改系列
    public function edit_series(){
        $series_id=I('series_id','');
        $name=I('name','');
        $series=M('product_series');
        $re=$series->where(array('id'=>$series_id))->save(array('name'=>$name));
        if($re){
            jr(0,'修改成功');
        }else{
            jr(1,'修改失败');
        }
    }
    // 添加产品系列
    public function add_series(){
        $series=M('product_series');
        if($data=$series->create()){
            $data['eid']=I('eid','');
            $data['update_date']=time();
            $count=$series->where(array('eid'=>$data['eid']))->count();
            $data['orderby']=$count+1;
            $re=$series->add($data);
            if($re){
                jr(0,'添加成功');
            }else{
                jr(1,'添加失败');
            }
        }else{
            jr(1,$company->getError());
        }
    }
    public function product_info(){
        $id=I('id','');
        if($id){
            $obj = A('Dc/Product');
            $product=$obj->get_one(false,'product',$id);
            $this->assign('product',$product);
        }
       
        $this->display();
    }
    public function set_orderby(){
        $orderby=I('order','');
        $id=I('id','');
        $exid=I('exid','');
        if($orderby && $id){
            $re=M('product_category')->where("pid=$id AND exid=$exid")->save(array('orderby'=>$orderby));
            if($re){
                jr(0,'排序成功');
            }else{
                jr(1,'error');
            }
        }else{
            jr(1,'error');
        }
    }
}