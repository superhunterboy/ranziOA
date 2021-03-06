<?php
/**
 * The edit admin view file of user module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     user 
 * @version     $Id: edit.admin.html.php 4029 2016-08-26 06:50:41Z liugang $
 * @link        http://www.ranzhi.org
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/treeview.html.php';?>
<div class="col-md-12">
  <div class='row'> 
    <?php include './deptside.html.php';?>
    <div class='col-md-10'>
      <div class="panel">
        <div class="panel-heading"><strong><i class="icon-edit"></i> <?php echo $lang->user->editProfile;?></strong></div>
        <form method='post' id='ajaxForm' class='form-condensed'>
          <div class='panel-body'>
            <fieldset class='fieldset-primary'>
              <table class='table table-form'>
                  <tr>
                      <th class='w-50px text-left'><?php echo $lang->user->account;?></th>
                      <td><?php echo html::input('account', $user->account, "class='form-control disabled' disabled='disabled'");?></td>
                      <th><?php echo $lang->user->realname;?></th>
                      <td><?php echo html::input('realname', $user->realname, "class='form-control'");?></td>
                  </tr>
                  <tr>
                      <th class='w-50px text-left'><?php echo $lang->user->supervisor;?></th>
                      <td><?php echo html::input('supervisor', $user->supervisor, "class='form-control");?></td>
                      <th></th>
                      <td></td>
                  </tr>
              </table>
            </fieldset>
            <fieldset>
              <legend><?php echo $lang->user->basicInfo; ?></legend>
              <table class='table table-form'>
                <tr>
                  <th><?php echo $lang->user->super;?></th>
                  <td><input name='admin' type='checkbox' value='super' <?php if($user->admin == 'super') echo 'checked';?>></td><td></td>
                </tr>  
                <tr>
                  <th><?php echo $lang->user->gender;?></th>
                  <td><?php unset($lang->user->genderList->u); echo html::radio('gender', $lang->user->genderList, $user->gender);?></td>
                </tr>  
                <tr>
                  <th class='w-80px'><?php echo $lang->user->dept;?></th>
                  <td class='w-p40'><?php echo html::select('dept', $depts, $user->dept, "class='form-control'");?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->user->role;?></th>
                  <td><?php echo html::select('role', $lang->user->roleList, $user->role, "class='form-control'");?></td><td></td>
                </tr>
                <tr>
                  <th><?php echo $lang->user->password;?></th>
                  <td><?php echo html::password('password1', '', "class='form-control' autocomplete='off'")?></td><td></td>
                </tr>  
                <tr>
                  <th><?php echo $lang->user->password2;?></th>
                  <td><?php echo html::password('password2', '', "class='form-control'");?></td><td></td>
                </tr>  
              </table>
            </fieldset>
            <fieldset>
              <legend><?php echo $lang->user->contactInfo; ?></legend>
              <table class='table table-form'>
                <tr>
                  <th class='w-80px'><?php echo $lang->user->email;?></th>
                  <td><?php echo html::input('email', $user->email, "class='form-control'");?></td>
                  <th><?php echo $lang->user->zipcode;?></th>
                  <td><?php echo html::input('zipcode', $user->zipcode, "class='form-control'");?></td>
                </tr>
                <tr>
                  <th class='w-80px'><?php echo $lang->user->mobile;?></th>
                  <td><?php echo html::input('mobile', $user->mobile, "class='form-control'");?></td>
                  <th><?php echo $lang->user->phone;?></th>
                  <td><?php echo html::input('phone', $user->phone, "class='form-control'");?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->user->qq;?></th>
                  <td><?php echo html::input('qq', $user->qq, "class='form-control'");?></td>
                  <th><?php echo $lang->user->gtalk;?></th>
                  <td><?php echo html::input('gtalk', $user->gtalk, "class='form-control'");?></td>
                </tr>
                <tr>
                  <th><?php echo $lang->user->address;?></th>
                  <td colspan='3'><?php echo html::input('address', $user->address, "class='form-control'");?></td>
                </tr>
              </table>
            </fieldset>          
            <?php echo html::submitButton();?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
