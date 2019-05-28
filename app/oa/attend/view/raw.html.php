<?php
/**
 * The detail view file of attend module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     attend 
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../../sys/common/view/treeview.html.php';?>
<?php include '../../../sys/common/view/chosen.html.php';?>
<?php include '../../../sys/common/view/datepicker.html.php';?>
<?php $lang->attend->abbrStatusList['rest'] = '';?>
<div id='menuActions'>

</div>
<div class='with-side'>
  <div class='side'>
    <div class='panel'>
      <div class='panel-heading'><strong><?php echo $currentYear . ($this->app->clientLang != 'en' ? $lang->year : '') . $lang->attend->raw;?></strong></div>
      <div class='panel-body'>
      <?php 
        $lastmonth = $currentYear == date('Y') ? date('m') : 12;
        for($month = 1; $month <= $lastmonth; $month++)
        {
            $class = $month == $currentMonth ? 'btn-primary' : '';
            $month = $month < 10 ? '0' . $month : $month;
            echo "<div class='col-xs-3 monthDIV'>" . html::a(inlink('raw', "date=$currentYear$month&account=$account"), $month . ($this->app->clientLang != 'en' ? $lang->month : ''), "class='btn btn-mini $class'") . '</div>';
        }
      ?>
      </div>
    </div>
      <?php if($admin):?>
    <div class='panel'>
      <div class='panel-heading'><strong><?php echo $lang->attend->search;?></strong></div>
      <div class='panel-body'>
        <form id='searchForm' method='post' action='<?php echo inlink('raw');?>'>
            <div class='form-group'>
                <div class='input-group'>
                    <span class='input-group-addon'><?php echo $lang->user->dept;?></span>
                    <?php echo html::select('dept', $deptList, $dept, "class='form-control chosen'");?>
                </div>
            </div>
            <div class='form-group'>
                <div class='input-group'>
                    <span class='input-group-addon'><?php echo $lang->attend->user;?></span>
                    <?php echo html::select('account', $userList, $account, "class='form-control chosen'");?>
                </div>
            </div>
            <div class='form-group'>
                <div class='input-group'>
                    <span class='input-group-addon'><?php echo $lang->attend->date;?></span>
                    <?php echo html::input('date', $date, "class='form-control form-month'");?>
                </div>
            </div>
          <div class='form-group'><?php echo html::submitButton($lang->attend->search);?></div>
        </form>
      </div>
    </div><?php endif;?>
  </div>
  <div class='main'>
    <div class='panel'>
      <div class='panel-heading text-center'>
        <strong><?php echo $fileName;?></strong>
      </div>
      <table class='table table-data table-bordered text-center table-fixedHeader'>
        <thead>
          <tr class='text-center'>
            <th><?php echo $lang->attend->user;?></th>
            <th><?php echo $lang->attend->date;?></th>

            <th><?php echo $lang->attend->detail;?></th>

          </tr>
        </thead>
          <?php foreach ($attends as $user){ ?>

              <?php $day=$date; for($i=0;$i<$dayNum;$i++) {

                  if($user['attend'][$day]) { $attend = $user['attend'][$day];
                      ?>
                      <tr>

          <td><?php echo $user['name'];?></td>
          <td><?php echo $day;?></td>
            <td class="text-left">
                <?php foreach ($attend['raw'] as $d):?>
                <span class="label label-info"><?php echo $d;?></span>
                <?php endforeach;?>
            </td>

        </tr>
        <?php
                  } else {
                      ?>
                      <tr>

          <td><?php echo $user['name'];?></td>
          <td><?php echo $day;?></td>
            <td class="text-left">

            </td>

        </tr>

        <?php   } $day = date('Y-m-d', strtotime("$day +1 day"));
            }  };?>
      </table>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
