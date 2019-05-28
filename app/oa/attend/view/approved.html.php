<?php
/**
 * The browse view file of leave module of Ranzhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     leave
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../../sys/common/view/treeview.html.php';?>
<?php js::set('confirmReview', $lang->attend->confirmReview)?>
<div id='menuActions'>
  <?php commonModel::printLink('oa.attend', 'exportapproved', "date=$currentYear$currentMonth", $lang->exportIcon . $lang->export, "class='btn btn-primary iframe' data-width='700'");?>
  </div>
<?php if($type != 'browseReview'):?>
<div class='with-side'>
  <div class='side'>
    <div class='panel panel-sm'>
      <div class='panel-body'>
        <ul class='tree' data-collapsed='true'>
          <?php foreach($yearList as $year):?>
          <li class='<?php echo $year == $currentYear ? 'active' : ''?>'>
            <?php commonModel::printLink('oa.attend', 'approved', "date=$year", $year);?>
            <ul>
              <?php foreach($monthList[$year] as $month):?>
              <li class='<?php echo ($year == $currentYear and $month == $currentMonth) ? 'active' : ''?>'>
                <?php commonModel::printLink('oa.attend', 'approved', "date=$year$month", $year . $month);?>
              </li>
              <?php endforeach;?>
            </ul>
          </li>
          <?php endforeach;?>
        </ul>
      </div>
    </div>
  </div>
  <div class='main'>
<?php endif;?>

    <div class='panel'>

      <table class='table table-hover text-center table-fixed tablesorter' id='leaveTable'>
        <thead>
          <tr class='text-center'>
            <?php $vars = "&date={$date}&orderBy=%s";?>
            <th class='w-50px'><?php commonModel::printOrderLink('id', $orderBy, $vars, $lang->attend->id);?></th>
            <th class='w-100px'><?php commonModel::printOrderLink('createdBy', $orderBy, $vars, $lang->attend->account);?></th>
            <th class='w-140px visible-lg'><?php echo $lang->user->dept;?></th>
            <th class='w-100px'><?php commonModel::printOrderLink('begin', $orderBy, $vars, $lang->attend->date);?></th>
            <th class='w-70px'><?php commonModel::printOrderLink('end', $orderBy, $vars, $lang->attend->manualIn);?></th>
            <th class='w-70px'><?php commonModel::printOrderLink('backDate', $orderBy, $vars, $lang->attend->manualOut);?></th>
            <th class='w-100px visible-lg'><?php commonModel::printOrderLink('reason', $orderBy, $vars, $lang->attend->reason);?></th>
            <th class='text-left'><?php echo $lang->attend->desc;?></th>
            <th class='w-100px'><?php commonModel::printOrderLink('status', $orderBy, $vars, $lang->attend->status);?></th>
              <th class='w-100px'><?php commonModel::printOrderLink('reviewedBy', $orderBy, $vars, $lang->attend->reviewedBy);?></th>
          </tr>
        </thead>
        <?php foreach($attends as $attend):?>
        <?php $viewUrl = commonModel::hasPriv('oa.leave', 'view') ? $this->createLink('oa.attend', 'view', "id={$leave->id}&type=$type") : '';?>
            <?php if(!isset($users[$attend->account])) continue;?>
            <?php $user = $users[$attend->account];?>
        <tr id='leave<?php echo $attend->id;?>' >
          <td class='idTD'>
            <?php echo $attend->id;?>
          </td>
          <td><?php echo $user->realname;?></td>
          <td class='visible-lg'><?php echo zget($deptList, $user->dept);?></td>
          <td><?php echo $attend->date;?></td>
          <td><?php echo substr($attend->manualIn, 0, 5)?></td>
          <td><?php echo substr($attend->manualOut, 0, 5);?></td>
          <td><?php echo zget($lang->attend->reasonList, $attend->reason);?></td>
          <td class='text-left' title='<?php echo $attend->desc;?>'><?php echo $attend->desc;?></td>
          <td class='attend-<?php echo $attend->status?>' title='<?php echo $attend->statusLabel;?>'><?php echo zget($lang->attend->statusList, $attend->status);?></td>
            <td><?php echo $attend->reviewedBy;?></td>
        </tr>
        <?php endforeach;?>
      </table>

      <?php if(!$attends):?>
      <div class='table-footer'>
        <div class='pager' style='float: right; clear: none'><?php echo $lang->pager->noRecord;?></div>
      </div>
      <?php endif;?>
    </div>

  </div>
</div>

<?php include '../../common/view/footer.html.php';?>
