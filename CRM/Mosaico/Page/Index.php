<?php

class CRM_Mosaico_Page_Index extends CRM_Core_Page {
  const DEFAULT_MODULE_WEIGHT = 200;

  function run() {
    $this->registerResources(CRM_Core_Resources::singleton());

    $messages = array();
    $syscheck = CRM_Utils_Request::retrieve('runcheck', 'Boolean', CRM_Core_DAO::$_nullObject);
    $tplCount = CRM_Core_DAO::singleValueQuery("SELECT count(id) FROM civicrm_mosaico_msg_template");
    if ($syscheck || empty($tplCount)) {
      $config = CRM_Core_Config::singleton();
      if (!(extension_loaded('imagick') || class_exists("Imagick"))) {
        $messages[] = new CRM_Utils_Check_Message(
          'mosaico_imagick',
          ts('Email Template Builder extension will not work.'),
          ts('ImageMagick not installed')
        );
      }
      if (!extension_loaded('fileinfo')) {
        $messages[] = new CRM_Utils_Check_Message(
          'mosaico_fileinfo',
          ts('May experience mosaico template or thumbnail loading issues (404 errors).'),
          ts('PHP extension Fileinfo not loaded or enabled')
        );
      }
      if (empty($config->extensionsURL)) {
        $messages[] = new CRM_Utils_Check_Message(
          'mosaico_exturl',
          ts('Make sure "Extension Resource URL" is configured with Administer » System Settings » Resouce URLs.'),
          ts('Extension resource url not configured')
        );
      }
      if (!is_writable($config->imageUploadDir)) {
        $messages[] = new CRM_Utils_Check_Message(
          'mosaico_uploaddir',
          ts('%1 dir not writable or configured.', array(1 => $config->imageUploadDir)),
          ts('Upload dir not writable or configured')
        );
      }
      $staticDir = rtrim($config->imageUploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'static'; 
      if (!is_writable($staticDir)) {
        $messages[] = new CRM_Utils_Check_Message(
          'mosaico_staticdir',
          ts('%1 dir not writable or configured.', array(1 => $staticDir)),
          ts('Static dir not writable or configured')
        );
      }
    }
    foreach ($messages as $message) {
      CRM_Core_Session::setStatus($message->getMessage(), $message->getTitle(), 'error');
    }
    return parent::run();
  }

  /**
   * @param CRM_Core_Resources $res
   */
  public function registerResources(CRM_Core_Resources $res) {
    $weight = self::DEFAULT_MODULE_WEIGHT;
    
    $res->addStyleFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/mosaico-material.min.css', $weight++, 'html-header', TRUE);
    $res->addStyleFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/notoregular/stylesheet.css', $weight++, 'html-header', TRUE);

    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/knockout.js', $weight++, 'html-header', TRUE);

    // civi already has jquery.min
    //$res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/jquery.min.js', $weight++, 'html-header', TRUE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'js/index.js', $weight++, 'html-header', FALSE);


    $res->addStyleFile('uk.co.vedaconsulting.mosaico', 'css/index.css', $weight++, 'html-header', TRUE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'js/index2.js', $weight++, 'html-header', TRUE);
  }
}
