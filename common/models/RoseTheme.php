<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "ug_rose_theme".
 *
 * @property string $img
 * @property string $title
 * @property string $slogan
 * @property string $content
 * @property string $thumb_img
 * @property integer $addtime
 */

class RoseTheme extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ug_rose_theme';
    }

    /**
     * 参数规则
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['addtime'], 'integer'],
            [['title', "content", 'img', "thumb_img", 'banner_img', 'addtime', 'slogan'], 'required'],
            [['title', 'img', 'thumb_img', 'banner_img', 'content', 'slogan', 'share_img'], 'string'],
        ];
    }

}
