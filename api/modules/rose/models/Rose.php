<?php
namespace api\modules\rose\models;

use Yii;
use yii\base\Exception;
use yii\web\UploadedFile;


/**
 * This is the model class for table "rose".
 *
 * @property integer $theme_id
 * @property string $token_id
 * @property string $theme_img
 * @property string $theme_thumb_img
 * @property string $theme_share_img
 * @property string $rose_name
 * @property string $theme_name
 * @property integer $material_type
 * @property string $amount
 * @property string $address
 * @property integer $status
 * @property integer $addtime
 */


class Rose extends \common\models\Rose
{
    const uploadFile = "uploads/";
    /**
     * 我的资产列表
     */
    public static function getList($address,$page = "1",$pageSize = "10")
    {
        $query = Rose::find();
        $query->where(['address' => $address]);
        $query->orderBy('update_time DESC');
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

    /**
     * 玫瑰详情信息
     */
    public static function getInfoById($id)
    {
        return Rose::find()
            ->select("id,token_id,theme_img,theme_thumb_img,rose_name,theme_name,material_type,amount,address,addtime")
            ->where(['id'=>$id])->asArray()->one();
    }
    /**
     * 上传图片
     */
    public function upload()
    {
        $object = self::uploadFile($this->theme_img);
        $this->theme_img = $object;
        $object = self::uploadFile($this->theme_thumb_img);
        $this->theme_thumb_img = $object;
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
     * 查询玫瑰持有者
     */
    public static function getMedalOwner($address, $medal_id)
    {
        return Rose::find()->select("id")->where(['id' => $medal_id, "address" => $address])->asArray()->one();
    }

    /**
     * 更新玫瑰持有者
     */
    public static function updateMedalOwner($address, $medal_id, $recipient_address)
    {
        $customer = Rose::findOne(['id' => $medal_id, 'address' => $address]);
        $customer->address = $recipient_address;
        $customer->update_time = time();
        return $customer->update();
    }
}
