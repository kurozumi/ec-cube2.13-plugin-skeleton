<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *
 */

/**
 * プラグインの基底クラス
 *
 * @package Plugin
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
class PluginName extends SC_Plugin_Base
{
    /**
     * コンストラクタ
     *
     * @param  array $arrSelfInfo 自身のプラグイン情報
     * @return void
     */
    public function __construct(array $arrSelfInfo)
    {
        // プラグインを有効化したときの初期設定をココに追加する
        if($arrSelfInfo["enable"] == 1) {}

    }

    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param  array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    public function install($arrPlugin, $objPluginInstaller = null)
    {

    }

    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param  array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    public function uninstall($arrPlugin, $objPluginInstaller = null)
    {
        
    }

    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param  array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    public function enable($arrPlugin, $objPluginInstaller = null)
    {
        // 有効時、プラグイン情報に値を入れたい場合使う
        self::updatePlugin($arrPlugin["plugin_code"], array(
            "free_field1" => "text1",
            "free_field2" => "text2",
            "free_field3" => "text3",
            "free_field4" => "text4",
        ));
    }

    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param  array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    public function disable($arrPlugin, $objPluginInstaller = null)
    {
        // 無効時、プラグイン情報に値を初期化したい場合使う
        self::updatePlugin($arrPlugin["plugin_code"], array(
            "free_field1" => null,
            "free_field2" => null,
            "free_field3" => null,
            "free_field4" => null,
        ));
    }

    /**
     * プラグインヘルパーへ, コールバックメソッドを登録します.
     *
     * @param integer $priority
     */
    public function register(SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction("loadClassFileChange", array(&$this, "loadClassFileChange"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("prefilterTransform", array(&$this, "prefilterTransform"), $this->arrSelfInfo['priority']);
        $objHelperPlugin->addAction("outputfilterTransform", array(&$this, "outputfilterTransform"), $this->arrSelfInfo['priority']);

    }

    /**
     * SC_系のクラスをフックする
     * 
     * @param type $classname
     * @param type $classpath
     */
    public function loadClassFileChange(&$classname, &$classpath)
    {
        
    }

    /**
     * テンプレートをフックする
     *
     * @param string &$source
     * @param LC_Page_Ex $objPage
     * @param string $filename
     * @return void
     */
    public function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename)
    {
        $objTransform = new SC_Helper_Transform($source);
        $template_dir = PLUGIN_UPLOAD_REALDIR . basename(__DIR__) . "/templates/";
        switch ($objPage->arrPageLayout['device_type_id']) {
            case DEVICE_TYPE_PC:
                break;
            case DEVICE_TYPE_MOBILE:
                break;
            case DEVICE_TYPE_SMARTPHONE:
                break;
            case DEVICE_TYPE_ADMIN:
            default:
                break;
        }
        $source = $objTransform->getHTML();

    }

    /**
     * テンプレートをフックする
     * Smartyの編集はできない
     *
     * @param string &$source
     * @param LC_Page_Ex $objPage
     * @param string $filename
     * @return void
     */
    public function outputfilterTransform(&$source, LC_Page_Ex $objPage, $filename)
    {
        $objTransform = new SC_Helper_Transform($source);
        $template_dir = PLUGIN_UPLOAD_REALDIR . basename(__DIR__) . "/templates/";
        switch ($objPage->arrPageLayout['device_type_id']) {
            case DEVICE_TYPE_PC:
                break;
            case DEVICE_TYPE_MOBILE:
                break;
            case DEVICE_TYPE_SMARTPHONE:
                break;
            case DEVICE_TYPE_ADMIN:
            default:
                break;
        }
        $source = $objTransform->getHTML();

    }
    
    /**
     * プラグイン情報更新
     * 
     * @param string $plugin_code
     * @param array $free_fields
     */
    public static function updatePlugin($plugin_code, array $free_fields){
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        $objQuery->update("dtb_plugin", $free_fields, "plugin_code = ?", array($plugin_code));
    }

}
