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
        self::setupAutoloader();
        
        // プラグインを有効化したときの初期設定をココに追加する
        if($arrSelfInfo["enable"] == 1) {}

    }
    
    protected static $isAutoloaderRegistered = false;
    
    /**
     * 独自ライブラリのパス設定
     */
    public static function setupAutoloader() {
        if (!self::$isAutoloaderRegistered) {
            $path = dirname(__FILE__) . "/lib";
            ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . $path);
        }
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
        self::setupAutoloader();
        
        // htmlディレクトリにファイルを配置。
        $src_dir = PLUGIN_UPLOAD_REALDIR . "{$arrPlugin["plugin_code"]}/html/";
        $dest_dir = HTML_REALDIR;
        SC_Utils::copyDirectory($src_dir, $dest_dir);

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
        self::setupAutoloader();
        
        // htmlディレクトリのファイルを削除。
        $target_dir = HTML_REALDIR;
        $source_dir = PLUGIN_UPLOAD_REALDIR . "{$arrPlugin["plugin_code"]}/html/";
        self::deleteDirectory($target_dir, $source_dir);


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
        self::setupAutoloader();
        
        // 有効時、プラグイン情報に値を入れたい場合使う
        self::updatePlugin($arrPlugin["plugin_code"], array(
            "free_field1" => "text1",
            "free_field2" => "text2",
            "free_field3" => "text3",
            "free_field4" => "text4",
        ));
        
        self::copyTemplate($arrPlugin);
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
        self::setupAutoloader();
        
        // 無効時、プラグイン情報に値を初期化したい場合使う
        self::updatePlugin($arrPlugin["plugin_code"], array(
            "free_field1" => null,
            "free_field2" => null,
            "free_field3" => null,
            "free_field4" => null,
        ));
        
        self::deleteTemplate($arrPlugin);
    }

    /**
     * プラグインヘルパーへ, コールバックメソッドを登録します.
     *
     * @param integer $priority
     */
    public function register(SC_Helper_Plugin $objHelperPlugin, $priority)
    {
        $objHelperPlugin->addAction("loadClassFileChange", array(&$this, "loadClassFileChange"), $priority);
        $objHelperPlugin->addAction("prefilterTransform", array(&$this, "prefilterTransform"), $priority);
        $objHelperPlugin->addAction("outputfilterTransform", array(&$this, "outputfilterTransform"), $priority);

    }

    /**
     * SC_系のクラスをフックする
     * 
     * @param type $classname
     * @param type $classpath
     */
    public function loadClassFileChange(&$classname, &$classpath)
    {
        $base_path = PLUGIN_UPLOAD_REALDIR . basename(__DIR__) . "/data/class/";
        $helper_path = $base_path . "helper/";
        
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
       
        switch ($objPage->arrPageLayout['device_type_id']) {
            case DEVICE_TYPE_PC:
                if (strpos($filename, "header.tpl") !== false) {
                    $template_path = 'plg_PluginName_header.tpl';
                    $template = "<!--{include file='{$template_path}'}-->";
                    $objTransform->select('#header_wrap')->appendChild($template);
                }
                break;
            case DEVICE_TYPE_MOBILE:
                break;
            case DEVICE_TYPE_SMARTPHONE:
                break;
            case DEVICE_TYPE_ADMIN:
            default:
                // 管理画面編集
                if (strpos($filename, "customer/subnavi.tpl") !== false) {
                    $template_path = 'customer/plg_PluginName_subnavi.tpl';
                    $template = "<!--{include file='{$template_path}'}-->";
                    $objTransform->select('ul')->appendChild($template);
                }

                // ブロック編集(PC)
                $template_dir = $template_dir . "default/frontparts/";
                
                if (strpos($filename, TEMPLATE_NAME . "/frontparts/bloc/login.tpl") !== false) {
                    $template_path = "bloc/plg_PluginName_login.tpl";
                    $objTransform->select(".block_body")->appendChild(
                            file_get_contents($template_dir . $template_path));
                }
                
                // ブロック編集(SMARTPHONE)
                $template_dir = $template_dir . "sphone/frontparts/";
                
                if (strpos($filename, SMARTPHONE_TEMPLATE_NAME . "/frontparts/bloc/login.tpl") !== false) {
                    $template_path = "bloc/plg_PluginName_login.tpl";
                    $objTransform->select("nav.top_menu")->appendChild(
                            file_get_contents($template_dir . $template_path));
                }
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
        $template_dir = PLUGIN_UPLOAD_REALDIR . basename(__DIR__) . "/data/Smarty/templates/";
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
    
    /**
     * 次に割り当てるMasterDataのIDを取得する
     * 
     * @param string $mtb
     * @return int
     */
    public static function getNextMasterDataId($mtb)
    {
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        return $objQuery->max("id", $mtb) + 1;
    }

    /**
     * 次に割り当てるMasterDataのRANKを取得する
     * 
     * @param string $mtb
     * @return int
     */
    public static function getNextMasterDataRank($mtb)
    {
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        return $objQuery->max("rank", $mtb) + 1;
    }
    
    /**
     * MasterDataに追加
     * 
     * @param type $mtb
     * @param type $name
     * @return int
     */
    public static function insertMasterDataId($mtb, $name, $id=null)
    {
        if(is_null($id))
            $id = self::getNextMasterDataId($mtb);

        $objQuery = & SC_Query_Ex::getSingletonInstance();
        $objQuery->insert($mtb, array(
            'id'   => $id,
            'name' => $name,
            'rank' => self::getNextMasterDataRank($mtb)));

        $masterData = new SC_DB_MasterData_Ex();
        $masterData->clearCache($mtb);
        
        return $id;
    }
    
    /**
     * MasterDataの指定IDを削除
     * 
     * @param SC_Query $objQuery
     * @param string $mtb
     * @param int $id
     */
    public static function deleteMasterDataId($mtb, $id)
    {
        $objQuery = & SC_Query_Ex::getSingletonInstance();
        $objQuery->delete($mtb, "id=?", array($id));

        $masterData = new SC_DB_MasterData_Ex();
        $masterData->clearCache($mtb);

    }
    
    /**
     * 指定されたパスを比較して再帰的に削除します。
     * 
     * @param string $target_dir 削除対象のディレクトリ
     * @param string $source_dir 比較対象のディレクトリ
     */
    public static function deleteDirectory($target_dir, $source_dir)
    {
        if($dir = opendir($source_dir)) {
            while ($name = readdir($dir)) {
                if ($name == '.' || $name == '..') {
                    continue;
                }

                $target_path = $target_dir . '/' . $name;
                $source_path = $source_dir . '/' . $name;

                if (is_file($source_path)) {
                    if (is_file($target_path)) {
                        unlink($target_path);
                        GC_Utils::gfPrintLog("$target_path を削除しました。");
                    }
                } elseif (is_dir($source_path)) {
                    if (is_dir($target_path)) {
                        self::deleteDirectory($target_path, $source_path);
                    }
                }
            }
            closedir($dir);
        }
    }
    
    /**
     * 本体にテンプレートをコピー
     * 
     * @param type $arrPlugin
     */
    public static function copyTemplate($arrPlugin)
    {
        $src_dir = PLUGIN_UPLOAD_REALDIR . "{$arrPlugin["plugin_code"]}/data/Smarty/templates/";

        // 管理画面テンプレートを配置。
        $dest_dir = TEMPLATE_ADMIN_REALDIR;
        SC_Utils::copyDirectory($src_dir . "admin/", $dest_dir);

        // PCテンプレートを配置。
        $dest_dir = SC_Helper_PageLayout_Ex::getTemplatePath(DEVICE_TYPE_PC);
        SC_Utils::copyDirectory($src_dir . "default/", $dest_dir);

        // スマホテンプレートを配置。
        $dest_dir = SC_Helper_PageLayout_Ex::getTemplatePath(DEVICE_TYPE_SMARTPHONE);
        SC_Utils::copyDirectory($src_dir . "sphone/", $dest_dir);

        // モバイルテンプレートを配置。
        $dest_dir = SC_Helper_PageLayout_Ex::getTemplatePath(DEVICE_TYPE_MOBILE);
        SC_Utils::copyDirectory($src_dir . "mobile/", $dest_dir);

    }

    /**
     * 本体にコピーしたテンプレートを削除
     * 
     * @param type $arrPlugin
     */
    public static function deleteTemplate($arrPlugin)
    {
        $src_dir = PLUGIN_UPLOAD_REALDIR . "{$arrPlugin["plugin_code"]}/data/Smarty/templates/";

        // 管理画面テンプレートを削除。 
        $target_dir = TEMPLATE_ADMIN_REALDIR;
        self::deleteDirectory($target_dir, $src_dir . "admin/");

        // PCテンプレートを削除。
        $target_dir = SC_Helper_PageLayout_Ex::getTemplatePath(DEVICE_TYPE_PC);
        self::deleteDirectory($target_dir, $src_dir . "default/");

        // スマホテンプレートを削除。
        $target_dir = SC_Helper_PageLayout_Ex::getTemplatePath(DEVICE_TYPE_SMARTPHONE);
        self::deleteDirectory($target_dir, $src_dir . "sphone");

        // モバイルテンプレートを削除。
        $target_dir = SC_Helper_PageLayout_Ex::getTemplatePath(DEVICE_TYPE_MOBILE);
        self::deleteDirectory($target_dir, $src_dir . "mobile");

    }

}
