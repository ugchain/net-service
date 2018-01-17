<?php
namespace api\modules\medal\models;

use Yii;

/**
 * This is the model class for table "medal_give".
 *
 * @property integer $medal_id
 * @property string $owner_address
 * @property string $recipient_address
 * @property integer $status
 * @property integer $addtime
 */

class MedalGive extends \common\models\MedalGive
{

    /**
     * 勋章转赠记录
     * @param $medal_id 勋章ID
     * @return  array
     */
    public static function getMedalGiveInfoByMedalId($medal_id)
    {
        return MedalGive::find()
            ->select("owner_address,recipient_address,addtime")
            ->where(["medal_id"=>$medal_id])
            ->orderBy("addtime ASC")
            ->asArray()->all();
    }


}
