<?php
/**
 * The side common view file of blog module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Xiying Guan <guanxiying@xirangit.com>
 * @package     blog
 * @version     $Id: side.html.php 4029 2016-08-26 06:50:41Z liugang $
 * @link        http://www.ranzhi.org
 */
?>
<?php if(isset($mode)) js::set('mode', $mode);?>
<?php $treeMenu = $this->tree->getTreeMenu('blog', 0, array('treeModel', 'createBlogBrowseLink'));?>
<div class='col-xs-2'>
  <div class='panel'> 
    <?php commonModel::printLink('blog', 'create', !empty($category->id) ? "categoryID={$category->id}" : '', $lang->blog->create, "class='btn btn-primary btn-lg btn-block'");?>
  </div>
  <div class='input-group'>
    <?php echo html::input('searchInput', isset($searchText) ? $searchText : '', "class='form-control search-query' placeholder=''"); ?>
    <span class='input-group-btn'>
      <?php echo html::a('###', "<i class='icon icon-search'></i>", "class='btn btn-primary' id='searchButton'"); ?>
    </span>
    <div id='querybox' class='hide'></div>
  </div>
  <div class='panel'> 
    <div class='panel-body'> <?php echo $treeMenu;?> </div>
    <?php if(count($tags) > 1):?>
    <div class='panel-body div-tags'>
      <?php foreach($tags as $tag):?>
      <?php if($tag) echo html::a(inlink('index', 'category=0&author=&month=&tag=' . $tag), $tag, "class='label label-info'");?>
      <?php endforeach;?>
    </div>
    <?php endif;?>

    <?php if(count($authors) > 1):?>
    <div class='panel-body'>
      <?php foreach($authors as $author):?>
      <?php echo html::a(inlink('index', "category=0&author={$author->account}"), $author->realname, "class='label label-success'");?>
      <?php endforeach;?>
    </div>
    <?php endif;?>

    <?php if(count($months) > 1):?>
    <div class='panel-body'>
      <ul class="nav nav-stacked ul-months">
        <?php foreach(array_keys($months) as $month):?>
        <li><?php echo html::a(inlink('index', 'category=0&author=&month=' . str_replace('-', '_', $month)), $month);?></li>
        <?php endforeach;?>
      </ul>
    </div>
    <?php endif;?>

  </div>
</div>
