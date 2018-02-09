<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "ug_red_packet_theme".
 *
 * @property string $img
 * @property string $title
 * @property string $thumb_img
 * @property string $share_img
 * @property integer $addtime
 */

class RedPacketTheme extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_red_packet_theme';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['addtime'], 'integer'],
            [['title','desc','img',"thumb_img",'share_img','addtime'], 'required'],
            [['title','desc','img','thumb_img','share_img'], 'string'],
        ];
    }

}
