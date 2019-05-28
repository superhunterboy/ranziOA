<?php
global $app;
helper::cd($app->getBasePath());
helper::import('/usr/share/nginx/html/oa/app/oa/attend/model.php');
helper::cd();
class extattendModel extends attendModel 
{

    public function getClient()
    {
if(strpos($_SERVER['HTTP_USER_AGENT'], 'easysoft/xuan.im') !== false) return 'xuanxuan';
        if($this->app->getViewType() == 'html') return 'desktop';
        return 'other';
    }

    public function signIn($account = '', $date = '')
    {
if($this->config->attend->signInClient == 'xuanxuan' && strpos($_SERVER['HTTP_USER_AGENT'], 'easysoft/xuan.im') === false) 
{
    return array('result' => 'fail', 'message' => sprintf($this->lang->attend->signInClientError, $this->lang->attend->clientList[$this->config->attend->signInClient]));
}
        if($this->config->attend->signInClient == 'desktop' && $this->app->getViewType() != 'html') 
        {
            return array('result' => 'fail', 'message' => sprintf($this->lang->attend->signInClientError, $this->lang->attend->clientList[$this->config->attend->signInClient]));
        }

        if(!$this->checkIP()) return array('result' => 'fail', 'message' => $this->lang->attend->note->IPDenied);

        if($account == '') $account = $this->app->user->account;
        if($date == '')    $date    = date('Y-m-d');

        $device = $this->app->getClientDevice();
        $client = $this->getClient();
        $attend = $this->dao->select('*')->from(TABLE_ATTEND)->where('account')->eq($account)->andWhere('`date`')->eq($date)->fetch();
        if(empty($attend))
        {
            $attend = new stdclass();
            $attend->account = $account;
            $attend->date    = $date;
            $attend->signIn  = helper::time();
            $attend->ip      = helper::getRemoteIp();
            $attend->device  = $device;
            $attend->client  = $client;
            $this->dao->insert(TABLE_ATTEND)->data($attend)->autoCheck()->exec();
            return !dao::isError();
        }

        if($attend->signIn == '' or $attend->signIn == '00:00:00')
        {
            $attend->signIn = helper::time();
            $attend->ip     = helper::getRemoteIp();
            $attend->device = $device;
            $attend->client = $client;
            $this->dao->update(TABLE_ATTEND)->data($attend)->where('id')->eq($attend->id)->exec();
            return !dao::isError();
        }

        return true;
    }

//**//
}