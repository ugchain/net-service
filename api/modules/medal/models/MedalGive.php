<?php
namespace api\modules\medal\models;

use Yii;

class MedalGive extends \common\models\MedalGive
{
    public function getMedal()
    {
        return $this->hasMany(Medal::className(), ['medal_id' => 'id']);
    }

    //创建数据
    public static function insertData($address, $medal_id, $recipient_address, $status)
    {
        $model = new self();
        $model->medal_id = $medal_id;
        $model->owner_address = $address;
        $model->recipient_address = $recipient_address;
        $model->status = $status;
        $model->addtime = time();
        return $model->save();
    }

    /**
     * 获取转增记录
     */
    public static function getList($address, $page, $pageSize)
    {

        $query = Yii::$app->db;
        $offset = ($page - 1) * $pageSize;
        $sql = "SELECT `m`.`*`, `mg`.`*` FROM `ug_medal_give` as `mg` LEFT JOIN `ug_medal` as `m` ON mg.medal_id = m.id where 
            mg.owner_address = '" . $address . "' or recipient_address = '" . $address . "' order by mg.addtime desc limit " . $pageSize . " offset " . $offset;
        var_dump($sql);die;
        $commond = $query->createCommand($sql);
        $list = $commond->queryAll();

        var_dump($list);die;
        $where = ["or", ['owner_address' => $address], ['recipient_address' => $address]];
        $query = MedalGive::find();
        $query->where($where);
        $query->orderBy('id DESC');
        //分页
        $count = $query->count();
        $offset = ($page - 1) * $pageSize;
        $query->offset($offset)->limit($pageSize);
        $index_list = $query->asArray()->all();
        //默认无下一页
        $is_next_page = "0";
        if ($count - ($page * $pageSize) >= 0) {
            $is_next_page = "1";//有下一页
        }
        return ['list' => $index_list, 'is_next_page' => $is_next_page,"count"=> $count];
    }


}
