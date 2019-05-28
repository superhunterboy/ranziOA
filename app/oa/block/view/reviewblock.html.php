<?php
/**
 * The announce block view file of block module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     block
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<table class='table table-hover table-striped table-sorter table-data table-fixed text-center <?php echo $appendClass?>'>
    <thead>
    <tr class='text-center'>
        <th><?php echo $lang->my->review->module;?></th>
        <th><?php echo $lang->attend->account;?></th>
        <th><?php echo $lang->attend->date;?></th>
        <th><?php echo $lang->attend->reason;?></th>
        <th><?php echo $lang->attend->status;?></th>
        <th><?php echo $lang->actions;?></th>
    </tr>
    </thead>
    <?php foreach($attends as $key=>$attend):?>
    <?php if($key>5) break;?>
        <tr>
            <td><?php echo $lang->attend->common;?></td>
            <td><?php echo zget($users, $attend->account);?></td>
            <td><?php echo $attend->date?></td>
            <td><?php echo zget($lang->attend->reasonList, $attend->reason)?></td>
            <td><?php echo zget($lang->attend->statusList, $attend->status)?></td>
            <td>
                <?php commonModel::printLink('oa.attend', 'review', "attendID={$attend->id}&status=pass",   $lang->attend->reviewStatusList['pass'],   "data-status='pass' data-toggle='ajax'")?>
                <?php commonModel::printLink('oa.attend', 'review', "attendID={$attend->id}&status=reject", $lang->attend->reviewStatusList['reject'], "data-toggle='modal'")?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
