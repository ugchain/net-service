<?php
namespace api\modules\medal\models;

use Yii;


/**
 * This is the model class for table "medal_give".
 *
 * @property integer $medal_id
 * @property string $from_address
 * @property string $to_address
 * @property integer $status
 * @property integer $addtime
 */
class MedalGive extends \common\models\MedalGive
{
    public function getMedal()
    {
        return $this->hasMany(Medal::className(), ['medal_id' => 'id']);
    }

    /**
     * 勋章转赠记录
     * @param $medal_id 勋章ID
     * @return  array
     */
    public static function getMedalGiveInfoByMedalId($medal_id, $page = "1", $pageSize = "10")
    {
        $query = MedalGive::find();
        $query->where(['medal_id' => $medal_id]);
        $query->orderBy('addtime DESC');
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
        return ['list' => $index_list, 'is_next_page' => $is_next_page,"count"=>$count];
    }

    //创建数据
    public static function insertData($address, $medal_id, $recipient_address, $status)
    {
        $model = new self();
        $model->medal_id = $medal_id;
        $model->from_address = $address;
        $model->to_address = $recipient_address;
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
        $sql = "SELECT `m`.*, `mg`.* FROM `ug_medal_give` as `mg` LEFT JOIN `ug_medal` as `m` ON mg.medal_id = m.id where 
            mg.from_address = '" . $address . "' order by mg.addtime desc limit " . $pageSize . " offset " . $offset;
        $commond = $query->createCommand($sql);
        $list = $commond->queryAll();

        $arr = [];
        $new_list = [];
        foreach ($list as $k => $v) {
            if(!in_array($v['medal_id'], $arr)) {
                $arr[] = $v['medal_id'];
                $new_list[] = $v;
            }
        }
        $count = count($new_list);
        //默认无下一页
        $is_next_page = "0";
        if ($count - ($page * $pageSize) >= 0) {
            $is_next_page = "1";//有下一页
        }
        return ['list' => $list, 'is_next_page' => $is_next_page,"count"=> $count];
    }


}
