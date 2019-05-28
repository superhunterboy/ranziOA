<?php
/**
 * The personal view file of attend module of Ranzhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     attend
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php js::set('confirmReview', $lang->attend->confirmReview);?>
<div class='panel'>
  <table class='table table-hover table-striped table-sorter table-data table-fixed text-center'>
    <thead>
      <tr class='text-center'>
        <th class='w-50px'><?php echo $lang->attend->id;?></th>
        <th class='w-100px'><?php echo $lang->user->dept;?></th>
        <th class='w-100px'><?php echo $lang->attend->account;?></th>
        <th class='w-100px'><?php echo $lang->attend->date;?></th>
        <th class='w-80px'><?php echo $lang->attend->manualIn;?></th>
        <th class='w-80px'><?php echo $lang->attend->manualOut;?></th>
        <th class='w-100px'><?php echo $lang->attend->reason;?></th>
        <th><?php echo $lang->attend->desc;?></th>
        <th class='w-100px'><?php echo $lang->attend->status;?></th>
        <th class='w-60px'><?php echo $lang->actions;?></th>
      </tr>
    </thead>
    <?php foreach($attends as $attend):?>
    <?php if(!isset($users[$attend->account])) continue;?>
    <?php $user = $users[$attend->account];?>
    <tr>
      <td><?php echo $attend->id;?></td>
      <td><?php echo zget($deptList, $user->dept, '');?></td>
      <td><?php echo $user->realname;?></td>
      <td><?php echo $attend->date?></td>
      <td><?php echo substr($attend->manualIn, 0, 5)?></td>
      <td><?php echo substr($attend->manualOut, 0, 5)?></td>
      <td><?php echo zget($lang->attend->reasonList, $attend->reason)?></td>
      <td><?php echo $attend->desc?></td>
      <td><?php echo zget($lang->attend->statusList, $attend->status)?></td>
      <td>

      </td>
    </tr>
    <?php endforeach;?>
  </table>
  <?php if(!$attends):?>
  <div class='table-footer'>
    <div class='pager' style='float: right; clear: none'><?php echo $lang->pager->noRecord;?></div>
  </div>
  <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php';?>
