<?php
include FRAME_ROOT . '/JWT.class.php';

/**
 * The control file of attend of Ranzhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     salary
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
class salary extends control
{
    /**
     * Browse personal salary.
     * 
     * @param  string $date
     * @access public
     * @return void
     */
    public function personal($date = '')
    {
        if($date == '' or strlen($date) != 6) $date = date('Y-m');

        $monthList = ['2018-10','2018-11'];
        //$yearList  = array_keys($monthList);

        $date = '2018-10';
        $account = 'libaishizhu';

        //sub=获取员工工资, username=andy, month=2018-10
        //$token = JWT::encode(['sub'=>'获取员工工资','username'=>$this->app->user->account,'month'=>$date], '234567p[]poiuytrewXGHJOIP09685644QQSDFG', 'HS512');
        //echo  $this->config->salary->key;
        $token = JWT::encode(['sub'=>'','username'=>$account,'month'=>$date], $this->config->salary->key, 'HS512');

        //echo $token;

        $data = commonModel::http2($this->config->salary->api, ['key'=>$token]);
        $salary = json_decode($data);

        print_r($salary);exit;

        $this->view->title        = $this->lang->salary->person;
        $this->view->salary       = $salary;
        $this->view->monthList    = $monthList;

        $this->display();
    }
}
