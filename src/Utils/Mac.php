<?php
namespace TrueMoneyWallet\Utils;

class Mac {
  protected $value;
  function __construct() {
    $this->value = $this->getMac();
  }
  private function getMac() {
	    $configs = '';
	    $match = $mac = array();
      if(PHP_OS == 'Linux'){
        exec('ifconfig',$configs);
        foreach ($configs as $config){
          preg_match('/ethernet\s*hwaddr(.*)/i', $config,$match);
          if(!empty($match)){
            preg_match('/[0-9A-Za-z:]+/', $match[1],$mac);
            return str_replace(':', '', $mac[0]);
          }
        }
      } elseif(PHP_OS == 'WINNT'){
        exec('ipconfig /all',$configs);
        foreach ($configs as $config){
          preg_match('/[0-9A-Z]{2}-[0-9A-Z]{2}-[0-9A-Z]{2}-[0-9A-Z]{2}-[0-9A-Z]{2}-[0-9A-Z]{2}/i', $config,$match);
          if(!empty($match)){
            return  str_replace('-', '', $match[0]);
          }
        }
      }
    }
}
