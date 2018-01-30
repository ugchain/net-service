<?php
namespace api\modules\redpacket\models;

use Yii;

/**
 * This is the model class for table "ug_red_packet_theme".
 *
 * @property string $img
 * @property string $title
 * @property string $thumb_img
 * @property string $share_img
 * @property integer $addtime
 */

class RedPacketTheme extends \common\models\RedPacketTheme
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
        $object = self::uploadFile($this->share_img);
        $this->share_img = $object;
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
        return RedPacketTheme::find()->asArray()->all();
    }

}
