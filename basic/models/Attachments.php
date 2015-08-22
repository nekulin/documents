<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "attachments".
 *
 * @property integer $id
 * @property integer $document_id
 * @property string $hash
 * @property string $name
 * @property integer $size
 * @property string $ext
 *
 * @property Documents $document
 */
class Attachments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attachments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id', 'hash', 'name', 'size', 'ext'], 'required'],
            [['document_id', 'size'], 'integer'],
            [['hash'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 255],
            [['ext'], 'string', 'max' => 15],
            [['hash'], 'unique', 'message' => 'Файл уже существует']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_id' => 'Document ID',
            'hash' => 'Hash',
            'name' => 'Оригинальное название',
            'size' => 'Размер',
            'ext' => 'Расширение',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Documents::className(), ['id' => 'document_id']);
    }

    public function afterDelete()
    {
        /** @todo delete file */
    }
}
