<?php

namespace common\helpers;

use Yii;
use common\helpers\OutputHelper;
/**
 * Class RewardData
 * @package common\helpers
 * 生成红包存储 存储类型为string
 */
class RewardData
{
    //实例化
    public $redis;
    const REPACKET = "ugc_repacket_activity_";

    /**
     * RewardData 初始化
     */
    public function __construct()
    {
        $this->redis = Yii::$app->redis;
    }

    /**
     * 存放数据
     * @id 红包ID
     * @data 存放红包数据
     * @return bool
     */
    public function set($id,$data)
    {
        if(!$this->redis){
            return false;
        }
        //todo 过期时间暂无处理
        $data = json_encode($data);
        //存放数据
        $this->redis->set($id,$data);
        return true;
    }

    /**
     * 获取数据
     * @id 红包ID
     * @return string
     */
    public function get($id)
    {
        if(!$this->redis){
            return false;
        }
        $data = $this->redis->get($id);

        if(!$data){
            return false;
        }
        $data = json_decode($data,true);

        //获取第一个元素值
        if(!$data){
            return false;
        }
        $amount = $data[0];

        //转成大数
        $amount = OutputHelper::toWei($amount);
        return $amount;
    }

    /**
     * 删除数据
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        if(!$this->redis){
            return false;
        }
        $data = $this->redis->get($id);

        if(!$data){
            return false;
        }
        $data = json_decode($data,true);

        //获取第一个元素值
        if(!$data || !is_array($data)){
            return false;
        }
        //删除元素key
        unset($data[0]);
        //数据为空时直接删除
        if(empty($data)){
            $this->redis->del($id);
        }
        //重建索引
        sort($data);
        //重新打乱顺序
        shuffle($data);
        //重新保存
        $this->set($id,$data);

        return true;
    }
}