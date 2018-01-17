<?php
namespace api\modules\medal\models;

use Yii;


/**
 * This is the model class for table "medal".
 *
 * @property integer $theme_id
 * @property string $token_id
 * @property string $theme_img
 * @property string $theme_thumb_img
 * @property string $medal_name
 * @property string $theme_name
 * @property integer $material_type
 * @property string $amount
 * @property string $address
 * @property integer $status
 * @property integer $addtime
 */


class Medal extends \common\models\Medal
{

    /**
     * 我的资产列表
     */
    public static function getList($address,$page,$pageSize)
    {
        $query = Medal::find();
        $query->where(['address' => $address]);
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

}
