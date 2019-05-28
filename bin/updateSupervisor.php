<?php

define('CORE_PATH', dirname(strtr(__FILE__,'\\','/'))."/");

require CORE_PATH.'config.php';
require CORE_PATH.'iMysqli.class.php';

class UpdateSupervisor {

    public static function doing() {
        $i=0;
        $users = iDB::all("SELECT u.account,TRIM(BOTH',' FROM c.moderators) as moderator,c.parent from sys_user as u,sys_category as c where u.dept=c.id and c.type='dept'");
        foreach ($users as $user)
        {
            if($user['moderator'])
            {
                $moderator = $user['moderator'];
                if($moderator == $user['account'])  //如果直属上级是自己
                {
                    //查找上级部门的部门经理
                    $parent = iDB::row("SELECT TRIM(BOTH',' FROM moderators) as moderator,parent from sys_category where id=".$user['parent']);
                    if($parent && $parent->moderator)
                    {
                        $moderator = $parent->moderator;
                    }
                }
            }
            else
            {
                $parent = iDB::row("SELECT TRIM(BOTH',' FROM moderators) as moderator,parent from sys_category where id=".$user['parent']);
                if($parent && $parent->moderator)
                {
                    $moderator = $parent->moderator;
                }
                else $moderator = $user['account'];
            }
            $res = iDB::update('sys_user', ['supervisor'=>$moderator],['account'=>$user['account']],0);
            if($res) $i++;
        }
        echo 'update '.$i.' rows';
    }

}


UpdateSupervisor::doing();



