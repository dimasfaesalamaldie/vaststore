<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property string $username
 * @property string $password
 * @property string $nama
 * @property int $role
 * @property string $access_token
 * @property string $auth_key
 *
 * @property Barang[] $barangs
 * @property Barang[] $barangs0
 * @property Pembelian[] $pembelians
 */
class Admin extends \yii\db\ActiveRecord implements IdentityInterface
{
    
    public function isAdmin() {
        return true;
    }

    public function isAktif() {
        return $this->aktif == 1;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if($this->auth_key === null || empty($this->auth_key)) {                
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            if($this->access_token === null|| empty($this->access_token)) {                        
                $this->access_token = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne(['username' => $id, 'aktif' => 1]);
    }
    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->username;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validatePassword($password)
    {
        return $this->password === md5($password);
    }

    public function validatePlainPassword($password)
    {
        return $this->password === $password;
    }
    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'nama', 'role'], 'required'],
            [['role', 'aktif'], 'integer'],
            [['username'], 'string', 'max' => 16],
            [['password'], 'string', 'max' => 32],
            [['nama'], 'string', 'max' => 50],
            [['access_token', 'auth_key'], 'string', 'max' => 123],
            [['username'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'password' => 'Password',
            'nama' => 'Nama',
            'role' => 'Role',
            'access_token' => 'Access Token',
            'auth_key' => 'Auth Key',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBarangs()
    {
        return $this->hasMany(Barang::className(), ['dibuat_oleh' => 'username']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBarangs0()
    {
        return $this->hasMany(Barang::className(), ['diubah_oleh' => 'username']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembelians()
    {
        return $this->hasMany(Pembelian::className(), ['dibuat_oleh' => 'username']);
    }
}
