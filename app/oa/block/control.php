<?php
/**
 * The control file for block module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     block
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
class block extends control
{
    /**
     * Block Index Page.
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $lang = $this->get->lang;
        $this->app->setClientLang($lang);
        $this->app->loadLang('block');

        $mode = strtolower($this->get->mode);
        if($mode == 'getblocklist')
        {   
            echo $this->block->getAvailableBlocks();
        }   
        elseif($mode == 'getblockform')
        {   
            $code = strtolower($this->get->blockid);
            $func = 'get' . ucfirst($code) . 'Params';
            echo $this->block->$func();
        }   
        elseif($mode == 'getblockdata')
        {   
            $code = strtolower($this->get->blockid);
            $func = 'print' . ucfirst($code) . 'Block';
            //exit($func);

            $this->$func();
        }
    }

    /**
     * Block Admin Page.
     * 
     * @param  int    $index 
     * @param  string $blockID 
     * @access public
     * @return void
     */
    public function admin($index = 0, $blockID = '')
    {
        $this->app->loadLang('block', 'sys');
        $title = $index == 0 ? $this->lang->block->createBlock : $this->lang->block->editBlock;

        if(!$index) $index = $this->block->getLastKey('oa') + 1;

        if($_POST)
        {
            $this->block->save($index, 'system', 'oa');
            if(dao::isError())  $this->send(array('result' => 'fail', 'message' => dao::geterror())); 
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->server->http_referer));
        }

        $block   = $this->block->getBlock($index, 'oa');
        $blockID = $blockID ? $blockID : ($block ? $block->block : '');

        $blocks = json_decode($this->block->getAvailableBlocks(), true);
        $this->view->blocks  = array_merge(array(''), $blocks);

        $this->view->title   = $title;
        $this->view->params  = $blockID ? json_decode($this->block->{'get' . ucfirst($blockID) . 'Params'}(), true) : array();
        $this->view->blockID = $blockID;
        $this->view->block   = $block;
        $this->view->index   = $index;
        $this->display();
    }

    /**
     * Sort block. 
     * 
     * @param  string    $oldOrder 
     * @param  string    $newOrder 
     * @access public
     * @return void
     */
    public function sort($oldOrder, $newOrder)
    {
        $this->locate($this->createLink('sys.block', 'sort', "oldOrder=$oldOrder&newOrder=$newOrder&app=oa"));
    }

    /**
     * Resize block 
     * 
     * @param  int    $id 
     * @param  string $type 
     * @param  string $data 
     * @access public
     * @return void
     */
    public function resize($id, $type, $data)
    {
        $this->locate($this->createLink('sys.block', 'resize', "id=$id&type=$type&data=$data"));
    }

    /**
     * Delete block. 
     * 
     * @param  int    $index 
     * @access public
     * @return void
     */
    public function delete($index)
    {
        $this->locate($this->createLink('sys.block', 'delete', "index=$index&app=oa"));
    }

    /**
     * Print announce block.
     * 
     * @access public
     * @return void
     */
    public function printAnnounceBlock()
    {
        $this->lang->announce = new stdclass();
        $this->app->loadLang('announce', 'oa');
        $this->app->loadLang('article', 'sys');

        $this->processParams();

        $this->view->announces = $this->dao->select('*')->from(TABLE_ARTICLE)
            ->where('type')->eq('announce')
            ->orderBy('createdDate desc')
            ->limit($this->params->num)
            ->fetchAll('id');

        $this->view->users = $this->loadModel('user')->getPairs();
        $this->display();
    }

    public function printAttendBlock() {
        $this->loadModel('attend', 'oa');
        $this->app->loadLang('attend', 'oa');
        $this->processParams();

        $date     = date('Y-m-d');
        $dateTime = strtotime($date);

        $currentYear  = substr($date, 0, 4);
        $currentMonth = substr($date, 5, 2);
        $startDate    = "{$currentYear}-{$currentMonth}-01";
        $endDate      = date('Y-m-d', strtotime("$startDate +1 month"));
        $dayNum       = (int)date('d', strtotime("$endDate -1 day"));
        $weekNum      = (int)ceil($dayNum / 7);

        if($this->config->attend->workingDays > 7)
        {
            $startDate = date('w', $dateTime) == 0 ? date('Y-m-d', $dateTime) : date('Y-m-d', strtotime("last Sunday $date"));
            $endDate   = date('Y-m-d', strtotime("next Saturday $startDate"));
        }
        else
        {
            $startDate = date('w', $dateTime) == 1 ? date('Y-m-d', $dateTime) : date('Y-m-d', strtotime("last Monday $date"));
            $endDate   = date('Y-m-d', strtotime("next Sunday $startDate"));
        }
        $attends = $this->attend->getByAccount($this->app->user->account, $startDate, $endDate);

        $this->view->attends   = $attends;
        $this->view->date      = $date;
        $this->view->startDate = $startDate;
        $this->view->endDate   = $endDate;
        $this->view->dayNum       = $dayNum;
        $this->view->weekNum      = $weekNum;
        $this->display();
    }

    public function printReviewBlock() {
        $this->loadModel('attend', 'oa');
        $this->app->loadLang('attend', 'oa');
        $this->processParams();

        $type='all'; $orderBy = 'status';
        if(!commonModel::isAvailable($type)) $this->loadModel('common')->deny('my', 'review');

        $this->loadModel('attend', 'oa');
        $this->loadModel('leave', 'oa');
        $this->loadModel('makeup', 'oa');
        $this->loadModel('overtime', 'oa');
        $this->loadModel('lieu', 'oa');
        $this->loadModel('refund', 'oa');

        $account  = $this->app->user->account;
        $isAdmin  = $this->app->user->admin == 'super';
        $allDepts = $this->loadModel('tree')->getPairs('', 'dept');
        $allDepts['0'] = '/';
        $managedDepts  = $this->loadModel('tree')->getDeptManagedByMe($account);
        $managedDepts  = array_keys($managedDepts);

        /* Get attend list. */
        $attends = array();
        if($type == 'all' || $type == 'attend')
        {
            if($isAdmin or (!empty($this->config->attends->reviewedBy) && $this->config->attends->reviewedBy == $account))
            {
                $attends = $this->attend->getWaitAttends();
            }
            else
            {
                if($managedDepts) $attends = $this->attend->getWaitAttends($managedDepts);
            }
        }

        /* Get leave list. */
        $leaves = array();
        if($type == 'all' || $type == 'leave')
        {
            $reviewedBy = $this->leave->getReviewedBy();
            if($isAdmin or ($reviewedBy && $reviewedBy == $account))
            {
                $leaves = $this->leave->getList('browseReview', '', '', '', '', '', $orderBy);
            }
            else
            {
                if($managedDepts) $leaves = $this->leave->getList('browseReview', '', '', '', $managedDepts, '', $orderBy);
            }
        }

        /* Get overtime list. */
        $overtimes = array();
        if($type == 'all' || $type == 'overtime')
        {
            $reviewedBy = $this->overtime->getReviewedBy();
            if($isAdmin or ($reviewedBy && $reviewedBy == $account))
            {
                $overtimes = $this->overtime->getList('browseReview', '', '', '', '', '', $orderBy);
            }
            else
            {
                if($managedDepts) $overtimes = $this->overtime->getList('browseReview', '', '', '', $managedDepts, '', $orderBy);
            }
        }

        /* Get makeup list. */
        $makeups = array();
        if($type == 'all' || $type == 'makeup')
        {
            $reviewedBy = $this->makeup->getReviewedBy();
            if($isAdmin or ($reviewedBy && $reviewedBy == $account))
            {
                $makeups = $this->makeup->getList('browseReview', '', '', '', '', '', $orderBy);
            }
            else
            {
                if($managedDepts) $makeups = $this->makeup->getList('browseReview', '', '', '', $managedDepts, '', $orderBy);
            }
        }

        /* Get lieu list. */
        $lieus = array();
        if($type == 'all' || $type == 'lieu')
        {
            $reviewedBy = $this->lieu->getReviewedBy();
            if($isAdmin or ($reviewedBy && $reviewedBy == $account))
            {
                $lieus = $this->lieu->getList('browseReview', '', '', '', '', '', $orderBy);
            }
            else
            {
                if($managedDepts) $lieus = $this->lieu->getList('browseReview', '', '', '', $managedDepts, '', $orderBy);
            }
        }

        /* Get refund list. */
        $refunds = array();
        if($type == 'all' || $type == 'refund')
        {
            $firstRefunds  = array();
            $secondRefunds = array();
            if($isAdmin or (!empty($this->config->refund->secondReviewer) and $this->config->refund->secondReviewer == $account))
            {
                $secondRefunds = $this->refund->getList('browseReview', '', '', '', 'doing', '', $orderBy);
            }

            /* Get refund list for firstReviewer. */
            if($isAdmin or (!empty($this->config->refund->firstReviewer) and $this->config->refund->firstReviewer == $account))
            {
                $firstRefunds = $this->refund->getList('browseReview', '', '', '', 'wait', '', $orderBy);
            }
            else
            {
                if($managedDepts) $firstRefunds = $this->refund->getList('browseReview', '', '',  $managedDepts, 'wait', '', $orderBy);
            }

            $refunds = array_merge($secondRefunds, $firstRefunds);
        }

        $this->view->title        = $this->lang->refund->review;
        $this->view->attends      = $attends;
        $this->view->leaveList    = $leaves;
        $this->view->overtimeList = $overtimes;
        $this->view->makeupList   = $makeups;
        $this->view->lieuList     = $lieus;
        $this->view->refunds      = $refunds;
        $this->view->deptList     = $allDepts;
        $this->view->users        = $this->loadModel('user')->getPairs();
        $this->view->type         = $type;
        $this->view->orderBy      = $orderBy;
        //$this->view->categories   = $this->refund->getCategoryPairs();
        //$this->view->currencySign = $this->loadModel('common', 'sys')->getCurrencySign();

        $this->display();
    }

    /**
     * Print attend block. 
     * 
     * @access public
     * @return void
     */
    public function printAttendBlock1()
    {
        $this->loadModel('attend', 'oa');
        $date     = date('Y-m-d');
        $dateTime = strtotime($date);
        if($this->config->attend->workingDays > 7)
        {
            $startDate = date('w', $dateTime) == 0 ? date('Y-m-d', $dateTime) : date('Y-m-d', strtotime("last Sunday $date"));
            $endDate   = date('Y-m-d', strtotime("next Saturday $startDate"));
        }
        else
        {
            $startDate = date('w', $dateTime) == 1 ? date('Y-m-d', $dateTime) : date('Y-m-d', strtotime("last Monday $date"));
            $endDate   = date('Y-m-d', strtotime("next Sunday $startDate"));
        }
        $attends = $this->attend->getByAccount($this->app->user->account, $startDate, $endDate);

        $dateLimit = array();
        $dateLimit['begin'] = $startDate;
        $dateLimit['end']   = $endDate;
        $todos = $this->loadModel('todo')->getList('self', $this->app->user->account, $dateLimit);

        /* Process todos. */
        $newTodos = array();
        foreach($todos as $todo)
        {
            if(!isset($newTodos[$todo->date])) $newTodos[$todo->date] = array();
            $time = date('H', strtotime("{$todo->date} {$todo->begin}")) > 12 ? 'PM' : 'AM';
            if(!isset($newTodos[$todo->date][$time])) $newTodos[$todo->date][$time] = array();
            $newTodos[$todo->date][$time][] = $todo;
        }

        $this->processParams();
        $this->view->attends   = $attends;
        $this->view->todos     = $newTodos;
        $this->view->date      = $date;
        $this->view->startDate = $startDate;
        $this->view->endDate   = $endDate;
        $this->display();
    }

    /**
     * Process params.
     * 
     * @access public
     * @return void
     */
    public function processParams()
    {
        $params = $this->get->param;
        $this->params = json_decode(base64_decode($params));

        $this->view->sso  = base64_decode($this->get->sso);
        $this->view->code = $this->get->blockid;
    }
}
