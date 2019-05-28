<?php
/**
 * The create view file of task module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     task
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<?php include '../../../sys/common/view/header.lite.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/chosen.html.php';?>
<div class='xuanxuan-card'>
  <form method='post' id='ajaxForm' enctype='multipart/form-data' action="<?php echo $this->createLink('task', 'create', "projectID=$projectID")?>">
    <div class='panel'>
      <div class='panel-heading'><strong><?php echo $lang->task->create;?></strong></div>
        <div class='panel-body'>
          <table class='table table-form'>
            <tr>
              <th class='w-60px'><?php echo $lang->task->name?></th>
              <td>
                <div class='input-group'>
                  <?php echo html::input('name', '', "class='form-control'");?>
                  <span class='input-group-addon fix-border'><?php echo $lang->task->pri;?></span>
                  <?php echo html::select('pri', $lang->task->priList, '', "class='form-control'")?>
                </div>
              </td>
            </tr>
            <tr>
              <th><?php echo $lang->task->assignedTo;?></th>
              <td>
                <div class='input-group'>
                  <?php echo html::select('assignedTo', $members, '', "class='form-control chosen'");?>
                  <?php echo html::input('teamMember', '', "class='form-control team-group hidden' readonly='readonly'");?>
                  <span class='input-group-addon fix-border fix-padding team-group hidden'></span>
                  <?php echo html::a("#modalTeam", $lang->task->team, "class='form-control btn team-group hidden' data-toggle='modal' data-target='#modalTeam'");?>
                  <span class='input-group-addon'>
                    <label class='checkbox-inline'>
                      <input type='checkBox' name='multiple' value='1'/>
                      <?php echo $lang->task->multipleAB;?>
                    </label>
                  </span>
                </div>
              </td>
            </tr>
            <tr>
              <th><?php echo $lang->task->estimate;?></th>
              <td>
                <div class='input-group'>
                  <?php echo html::input('estimate', '', "class='form-control'")?>
                  <span class='input-group-addon fix-border'><?php echo $lang->task->deadline;?></span>
                  <?php echo html::input('deadline', '', "class='form-control form-date'");?>
                </div>
              </td>
            </tr>
            <tr>
              <th><?php echo $lang->task->desc?></th>
              <td><?php echo html::textarea('desc', '', "class='form-control'");?></td>
            </tr>
            <tr>
              <th><?php echo $lang->task->mailto;?></th>
              <td><?php echo html::select('mailto[]', $users, '', "class='form-control chosen' multiple data-placeholder='{$lang->task->mailtoPlaceholder}'");?></td>
            </tr>
            <?php if(commonModel::hasPriv('file', 'upload')):?>
            <tr>
              <th><?php echo $lang->files;?></th>
              <td><?php echo $this->fetch('file', 'buildForm')?></td>
            </tr>
            <?php endif;?>
            <tr>
              <th></th>
              <td><?php echo html::submitButton();?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class='modal fade modal-team' id='modalTeam'>
      <div class='modal-dialog'>
        <div class='modal-header'>
          <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>×</span><span class='sr-only'>关闭</span></button>
          <h4 class='modal-title'><?php echo $lang->task->team?></h4>
        </div>
        <div class='modal-content'>
          <table class='table-form'>
            <thead>
              <tr class='text-center'>
                <th><?php echo $lang->task->team;?></th>
                <th class='w-120px'><?php echo $lang->project->role;?></th>
                <th class='w-130px'><?php echo $lang->task->recordEstimate;?></th>
                <th class='w-90px'></th>
              </tr>
            </thead>
            <?php for($i = 0; $i < 6; $i++):?>
            <tr>
              <td><?php echo html::select("team[]", $members, '', "class='form-control chosen'")?></td>
              <td><?php echo html::input("role[]", '', "class='form-control'")?></td>
              <td>
                <div class='input-group'>
                  <?php echo html::input("teamEstimate[]", '', "class='form-control text-center' placeholder='{$lang->task->estimateAB}'")?>
                  <span class='input-group-addon'><?php echo $lang->task->hour?></span>
                </div>
              </td>
              <td>
                <a href='javascript:;' class='btn btn-move-up btn-sm'><i class='icon-arrow-up'></i></a>
                <a href='javascript:;' class='btn btn-move-down btn-sm'><i class='icon-arrow-down'></i></a>
              </td>
            </tr>
            <?php endfor;?>
            <tr><td colspan='4' class='text-center'><?php echo html::a('javascript:void(0)', $lang->confirm, "class='btn btn-primary' data-dismiss='modal'")?></td></tr>
          </table>
        </div>
      </div>
    </div>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>
