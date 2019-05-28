<?php
global $app;
helper::cd($app->getBasePath());
helper::import('/usr/share/nginx/html/oa/app/sys/common/model.php');
helper::cd();
class extcommonModel extends commonModel 
{

    public function isOpenMethod($module, $method)
    {
if($module == 'attach' and $method == 'upload')   return true;
if($module == 'attach' and $method == 'download') return true;
if($module == 'entry'  and $method == 'visit')    return true;   
        if($module == 'user' and strpos(',login|logout|deny|control', $method)) return true;
        if($module == 'api'  and $method == 'getsessionid') return true;
        if($module == 'misc' and $method == 'ping') return true;
        if($module == 'misc' and $method == 'ignorenotice') return true;
        if($module == 'action' and $method == 'read') return true;
        if($module == 'block') return true;
        if($module == 'notice') return true;
        if($module == 'sso' and strpos(',auth|check|gettodolist|leaveusers', $method)) return true;
        if($module == 'attend' and strpos(',signin|signout', $method)) return true;
        if($module == 'refund' and $method == 'createtrade') return true;
        if($module == 'file'   and $method == 'read') return true;
        if($module == 'file'   and $method == 'download') return true;

        if($this->loadModel('user')->isLogon() and stripos($method, 'ajax') !== false) return true;

        return false;
    }   

//**//
}