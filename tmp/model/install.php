<?php
global $app;
helper::cd($app->getBasePath());
helper::import('/usr/share/nginx/html/oa/app/sys/install/model.php');
helper::cd();
class extinstallModel extends installModel 
{
public function createTable($version)
{
    $result = parent::createTable($version);
    if($result)
    {
        $sql  = "INSERT INTO `{$this->config->db->name}`.`{$this->config->db->prefix}sys_config` (`owner`, `app`, `module`, `section`, `key`, `value`) VALUES ('system', 'sys', 'xuanxuan', 'global', 'version', '{$this->config->xuanxuan->version}');";
        $sql .= "INSERT INTO `{$this->config->db->name}`.`{$this->config->db->prefix}sys_config` (`owner`, `app`, `module`, `section`, `key`, `value`) VALUES ('system', 'sys', 'xuanxuan', '', 'key', '" . md5(time()). "');";
        $this->dbh->query($sql);
    }
    return $result;
}
    public function createAdmin()
    {
$this->loadModel('upgrade')->installSSOEntry();
        $admin = new stdclass();
        $admin->account   = $this->post->account;
        $admin->realname  = $this->post->account;
        $admin->password  = $this->loadModel('user')->createPassword($this->post->password, $admin->account);
        $admin->password1 = $this->post->password; 
        $admin->admin     = 'super';
        $admin->join      = helper::now();
        $this->lang->user->password1 = $this->lang->user->password;
        $this->dao->insert(TABLE_USER)->data($admin, $skip = 'password1')->autoCheck()->batchCheck('account,password1', 'notempty')->check('account', 'account')->exec();

        if(dao::isError()) return false;

        /* Update category name by lang. */
        foreach($this->lang->install->categoryList as $id => $name)
        {
            $this->dao->update(TABLE_CATEGORY)->set('name')->eq($name)->where('id')->eq($id)->exec();
        }

        /* Update schema name by lang. */
        foreach($this->lang->install->schemaList as $id => $name)
        {
            $this->dao->update(TABLE_SCHEMA)->set('name')->eq($name)->where('id')->eq($id)->exec();
        }

        /* Update cron remark by lang. */
        foreach($this->lang->install->cronList as $id => $remark)
        {
            $this->dao->update(TABLE_CRON)->set('remark')->eq($remark)->where('id')->eq($id)->exec();
        }

        /* Update group name and desc on dafault lang. */
        $groups = $this->dao->select('*')->from(TABLE_GROUP)->orderBy('id')->fetchAll();
        foreach($groups as $group)
        {
            $data = zget($this->lang->install->groupList, $group->id, '');
            if($data) $this->dao->update(TABLE_GROUP)->data($data)->where('id')->eq($group->id)->exec();
        }

        return !dao::isError();
    }

//**//
}