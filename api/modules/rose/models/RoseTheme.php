<?php
namespace api\modules\rose\models;

use Yii;

/**
 * This is the model class for table "ug_rose_theme".
 *
 * @property string $img
 * @property string $title
 * @property string $thumb_img
 * @property string $share_img
 * @property integer $addtime
 */

class RoseTheme extends \common\models\RoseTheme
{
    const uploadFile = "uploads/";
    /**
     * 上传图片
     */
    public function upload()
    {
        $object = self::uploadFile($this->img);
        $this->img = $object;
        $object = self::uploadFile($this->thumb_img);
        $this->thumb_img = $object;
        $object = self::uploadFile($this->banner_img);
        $this->banner_img = $object;
    }

    /**
     * 上传到本地uploads/
     * @param $file
     * @return string
     */
    public static function uploadFile($file)
    {
        $ext = $file->getExtension();
        $path = self::uploadFile . time() . rand(100, 999) . "." . $ext;
        try {
            $file->saveAs($path);
        } catch (Exception $e) {
            print $e->getMessage();
            exit;
        }
        return $path;
    }

    /**
     * 获取主题列表
     */
    public static function getList()
    {
        return RoseTheme::find()->asArray()->all();
    }

    /**
     * 获取单条主题信息
     */
    public static function getInfoById($id)
    {
        return RoseTheme::find()->where(["id"=>$id])->asArray()->one();
    }

}
