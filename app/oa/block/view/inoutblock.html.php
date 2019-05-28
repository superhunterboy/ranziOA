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
<table class='table table-data table-hover table-fixed' id='oaBlockInout'>
  <?php foreach($attends as $id => $attend):?>
  <tr>
      <?php $currentDate = date('Y-m-d', $startDate);?>

          <?php $status = $attend->status;?>
          <?php $reason = $attend->reason;?>
          <?php $date   = date('Ymd', $startDate);?>
          <?php $reviewStatus = isset($attend->reviewStatus) ? $attend->reviewStatus : '';?>
          <tr class="text-middle">
              <td><?php echo $attend->date;?></td>
              <td><?php echo $lang->datepicker->abbrDayNames[$dayIndex]?></td>
              <td class='attend-signin'>
                  <?php $signIn = substr($attend->signIn, 0, 5);?>
                  <?php if($attend->signIn == '00:00:00' || $attend->signIn == '') $status='none';?>

                  <?php if($status == 'none') $signIn = $lang->attend->statusList['none'];?>

                  <?php echo $signIn;?>
              </td>
              <td class='attend-signout'>
                  <?php $signOut = substr($attend->signOut, 0, 5);?>
                  <?php if($attend->signOut == '00:00:00' || $attend->signOut == '') $status='none';?>
                  <?php if($status == 'none') $signOut = $lang->attend->statusList['none'];?>
                  <?php echo $signOut;?>
              </td>
              <td>
                  <?php
                  $edit     = $reviewStatus == 'wait' ? $lang->attend->edited    : $lang->attend->edit;
                  $leave    = $reason == 'leave'      ? $lang->attend->leaved    : $lang->attend->leave;
                  $makeup   = $reason == 'makeup'     ? $lang->attend->makeuped  : $lang->attend->makeup;
                  $overtime = $reason == 'overtime'   ? $lang->attend->overtimed : $lang->attend->overtime;
                  $lieu     = $reason == 'lieu'       ? $lang->attend->lieud     : $lang->attend->lieu;
                  $trip     = $reason == 'trip'       ? $lang->attend->triped    : $lang->attend->trip;
                  $egress   = $reason == 'egress'     ? $lang->attend->egress    : $lang->attend->egress;
                  ?>

                  <?php if($status == 'rest'):?>
                      <span class='attend-<?php echo $status;?>'><?php echo $lang->attend->overtime; ?></span>
                  <?php elseif($status == 'normal'):?>
                      <span class='attend-<?php echo $status;?>'><?php echo $lang->attend->statusList[$status];?></span>
                  <?php endif;?>
              </td>
          </tr>

  </tr>
  <?php endforeach;?>
</table>
