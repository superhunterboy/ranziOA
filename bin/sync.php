<?php
/**
 * Created by PhpStorm.
 * User: goslin
 * Date: 2018/11/19
 * Time: 13:48
 */

define('CORE_PATH', dirname(strtr(__FILE__,'\\','/'))."/");

ini_set('display_errors','ON');
error_reporting(E_ALL & ~E_NOTICE);

@ini_set('magic_quotes_sybase', 0);
@ini_set("magic_quotes_runtime",0);
@ini_set('date.timezone','Asia/Shanghai');//设置时区
function_exists('date_default_timezone_set') && date_default_timezone_set('Asia/Shanghai');


require CORE_PATH.'config.php';
require CORE_PATH.'iMysqli.class.php';
require CORE_PATH.'iMsSQL.class.php';

define('TABLE_ATTEND', 'oa_attend');

class SyncDB {
    public static function getCheckInOut($st,$ed) {
        $sql = 'select a.CHECKTIME AS CHECKTIME,a.CHECKTYPE AS CHECKTYPE,b.USERID AS USERID,b.Name AS Name from (CHECKINOUT a join USERINFO b on((a.USERID = b.USERID))) WHERE a.CHECKTIME>=\''.$st.'\' and a.CHECKTIME<=\''.$ed.'\' ORDER BY a.CHECKTIME';

        $db = new MSSQLDB();
        $data = $db->query_database($sql);

        self::importInOut($data);
    }

    public static function importInOut($data) {

        if($data) {
            //$row = $data;
            foreach($data as $row) {

                $tp = $row->CHECKTYPE;
                $datetime = strtotime($row->CHECKTIME);
                //$noontime = strtotime(date('Y-m-d 12:'))
                $name = trim(strtolower($row->Name));
                //$attend = $this->dao->select('id,account,signIn,signOut,date')->from(TABLE_ATTEND)->where('account')->eq($name)->andWhere('date')->eq(date('Y-m-d', $datetime))->fetch();
                $attend = iDB::row("select `id`,`account`,`signIn`,`signOut`,`date` from ".TABLE_ATTEND." where `account`='{$name}' and `date`='".date('Y-m-d', $datetime)."' limit 1", ARRAY_A);
                if($attend && $attend['id']) {

                    // 如果存在空记录，则先更新上班签到
                    if($attend['signIn'] == '00:00:00') {
                        $att = [];
                        $att['signIn']  = date('H:i:s', $datetime);
                        $att['status'] = '';
                        echo "signIn:".date('H:i:s', $datetime).' user:'.$name."\n";
                        iDB::update(TABLE_ATTEND, $att, ['id'=>$attend['id']]);
                        continue;
                    }
                    else {
                        $signIn = strtotime($attend['date'].' '.$attend['signIn']);
                        // 如果发现比签到时间更早的记录，则调整签到时间至更早的时间记录
                        // 这种情况一般发生在考勤机上报数据延时的情况下
                        // 同时考勤机又是门进卡的情况下会发生
                        if($signIn>$datetime) {
                            $att = [];
                            $att['signIn']  = date('H:i:s', $datetime);
                            $att['status'] = '';
                            iDB::update(TABLE_ATTEND, $att, ['id'=>$attend['id']]);
                            continue;
                        }
                    }
                    // 需要计算是下班签到，还是多次打卡
                    if(self::calculateTime($attend, $datetime))
                    {
                        $att = [];
                        $att['signOut']  = date('H:i:s', $datetime);
                        $att['status'] = '';
                        echo "signOut:".date('H:i:s', $datetime).' user:'.$name."\n";
                        iDB::update(TABLE_ATTEND, $att, ['id'=>$attend['id']]);
                    }
                }
                else {
                    $attend = [];
                    $attend['account'] = $name;
                    $attend['date'] = date('Y-m-d', $datetime);

                    // 第一个时间都算 上班签到
                    $attend['signIn'] = date('H:i:s', $datetime);

                    $attend['ip'] = '*';
                    $attend['device'] = 'zkt';
                    $attend['client'] = 'zkt';
                    iDB::insert(TABLE_ATTEND, $attend);
                    echo 'signIn:'.date('H:i:s', $datetime).' user:'.$name."\n";
                }
                //print_r($attend);
                //exit;

            }
        }
    }

    private static function calculateTime($attend, $time) {
        $signIn = strtotime($attend['date'].' '.$attend['signIn']);
        // 忽略1小时内的多次打卡
        if(($time-$signIn)<3600) return false;
        $today = date('Y-m-d');

        if($attend['date'] == $today ) {
            if((int)date('H')<18) return false;
        }

        $signOut = strtotime($attend['date'].' '.$attend['signOut']);
        // 如果打卡时间比记录在数据库内的signOut时间小，则不需要记录该条数据，下班签到看当天最后一条记录。
        if($signOut>=$time) return false;
        else return true;
    }
}

$params = getopt('d::m::');

if($params['m']) {

}


    $date = $params['d'];
    if(!$date) $date = date('Y-m-d');
    if($date=='yesterday') {
        $days = (int)date('d', time() - 86400);
        $start = strtotime(date('Y-m-1'));
        for($i=0;$i<$days;$i++) {
            $date = date('Y-m-d',$start + 86400 * $i);
            echo "process:".$date."\n";
            SyncDB::getCheckInOut($date.' 00:00:00', $date.' 23:59:59');
        }
    }
    else {
        echo "process:".$date."\n";
        SyncDB::getCheckInOut($date.' 00:00:00', $date.' 23:59:59');
    }




