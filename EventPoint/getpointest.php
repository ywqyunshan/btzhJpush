<?php
 header('content-type:text/html;charset=utf-8');
 //require_once "../../dbutil/db.oracle.param.php";
 //require_once '../../vendor/autoload.php';
require_once '../vendor/autoload.php';

use JPush\Model as M;
use JPush\JPushClient;
use JPush\JPushLog;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;
 

/*-----------------------------------------------------声明传送数据变量---------------------------------------------------*/
#$arr=$_REQUEST;
/*#消防系统用户名称
$departName=".$arr["departName"].";
#案件地址
$eventName=".$arr["eventName"].";
#案件编号
$eventId=".$arr["eventId"].";
#纬度
$lat=".$arr["lat"].";
#经度
$lon=".$arr["lon"].";
#案件类型
$type=".$arr["type"].";
#半径
$radius=".$arr["radius"].";
#警情平台用户id
$userId=".$arr["userId"].";
#立案用车信息
$vehicleInfo=".$arr["vehicleInfo"].";
*/

/*-----------------------------------------------------wed端数据保存到服务器-----------------------------------------------*/

 // $conn =get_conn();
 // if($arr["operate"]==5)
 // {
 //   $sql="INSERT INTO tbl_temp_aq(jlbh,eventId,lat,lon,type,eventName,userId,operate,time,departName,radius)
 //   VALUES(tbl_temp_aq_sq.nextval,'".$arr["eventId"]."',".$arr["lat"].",".$arr["lon"].",".$arr["type"].",'".$arr["eventName"]."','".
 //   $arr["userId"]."',".$arr["operate"].",'".$arr["time"]."','".$arr["departName"]."',".$arr["radius"].")";
 // }
 // $arr1=db_add_delete($sql, $conn);
 // if($conn)
 // {
 // 	$result=array("retCode"=>0,"retInfo"=>"OK");
 // 	//超时数据处理
 //    $sql1="update tbl_temp_aq set flag=3 where flag=0 and sysdate-createtime>5/60/24  ";
 //    $arr2=db_add_delete($sql1, $conn);
 // }else 
 // {
 // 	$result=array("retCode"=>1,"retInfo"=>"数据库连接失败！");
 // }

$arr=json_decode($_GET['p'],false);;

#消防系统用户名称
$departName=$arr->departName;
#echo $departName;
#案件地址
$eventName=$arr->eventName;
#案件编号
$eventId=$arr->eventId;
#纬度
$lat=$arr->lat;
#经度
$lon=$arr->lon;
#时间
$time=$arr->time;
#案件类型
$type=$arr->type;
#半径
$radius=$arr->radius;
#警情平台用户id
$userId=$arr->userId;
#立案用车信息
$vehicleInfo=$arr->vehicleInfo;


/*-------------------------------------------推送给 Android平台下alias为departName的用户-----------------------------------*/

$br = '<br/>';
$spilt = ' - ';
#极光推送消防系统 appkey
$app_key='e35922fb96eb1675964d9d4a';
$master_secret = 'd339fe143f779a1349cd712d';

#极光推送测试 appkey
#$app_key='97a3fddb7d18d05febda97a5';
#$master_secret = '970bddac3d8f6908742af258';
JPushLog::setLogHandlers(array(new StreamHandler('jpush.log', Logger::DEBUG)));
$client = new JPushClient($app_key, $master_secret);

 try {
    $result = $client->push()
        ->setPlatform(M\Platform('android'))
        ->setAudience(M\Audience(M\alias(array($departName))))
       /* ->setNotification(M\notification('发生火灾',
            M\android('发生火灾2', '发生火灾', 1, array("key1"=>"value1", "key2"=>"value2"))
        ))*/
         ->setMessage(M\message($departName.'迅速支援', $eventName.'发生火灾', 'text', array("departName"=>$departName,"eventName"=>$eventName,
         	"time"=>$time,"eventId"=>$eventId,"lat"=>$lat,"lon"=>$lon,"radius"=>$radius,"userId"=>$userId,"vehicleInfo"=>$vehicleInfo)))
        ->printJSON()
        ->send();
    echo 'Push Success.' . $br;
    echo 'sendno : ' . $result->sendno . $br;
    echo 'msg_id : ' .$result->msg_id . $br;
    echo 'Response JSON : ' . $result->json . $br;
} catch (APIRequestException $e) {
    echo 'Push Fail.' . $br;
    echo 'Http Code : ' . $e->httpCode . $br;
    echo 'code : ' . $e->code . $br;
    echo 'Error Message : ' . $e->message . $br;
    echo 'Response JSON : ' . $e->json . $br;
    echo 'rateLimitLimit : ' . $e->rateLimitLimit . $br;
    echo 'rateLimitRemaining : ' . $e->rateLimitRemaining . $br;
    echo 'rateLimitReset : ' . $e->rateLimitReset . $br;
} catch (APIConnectionException $e) {
    echo 'Push Fail: ' . $br;
    echo 'Error Message: ' . $e->getMessage() . $br;
    //response timeout means your request has probably be received by JPUsh Server,please check that whether need to be pushed again.
    echo 'IsResponseTimeout: ' . $e->isResponseTimeout . $br;
}

//  try {
//     $result = $client->push()
//         ->setPlatform(M\Platform('android'))
//         ->setAudience(M\Audience(M\alias(array($departName))))
//         ->setNotification(
//             M\android($departName+'发生火灾', $departName+'发生火灾', 1, array("departName"=>$departName, "eventName"=>$eventName
//             	,"eventId"=>$eventId,"lat"=>$lat,"lon"=>$lon，"radius"=>$radius,"userId"=>$userId,"vehicleInfo"=$vehicleInfo))
//         )
//         ->setMessage(M\message($departName+'发生火灾', $departName+'发生火灾', 'Message Type', array("departName"=>$departName,"eventName"=>$eventName
//             	,"eventId"=>$eventId,"lat"=>$lat,"lon"=>$lon，"radius"=>$radius,"userId"=>$userId,"vehicleInfo"=$vehicleInfo)))
//         ->printJSON()
//         ->send();
//     $padsend=True;
//     echo 'Push Success.' . $br;
//     echo 'sendno : ' . $result->sendno . $br;
//     echo 'msg_id : ' .$result->msg_id . $br;
//     echo 'Response JSON : ' . $result->json . $br;
// } catch (APIRequestException $e) {
//     $padsend=False;
//     echo 'Push Fail.' . $br;
//     echo 'Http Code : ' . $e->httpCode . $br;
//     echo 'code : ' . $e->code . $br;
//     echo 'Error Message : ' . $e->message . $br;
//     echo 'Response JSON : ' . $e->json . $br;
//     echo 'rateLimitLimit : ' . $e->rateLimitLimit . $br;
//     echo 'rateLimitRemaining : ' . $e->rateLimitRemaining . $br;
//     echo 'rateLimitReset : ' . $e->rateLimitReset . $br;
// } catch (APIConnectionException $e) {
//     $padsend=False;
//     echo 'Push Fail: ' . $br;
//     echo 'Error Message: ' . $e->getMessage() . $br;
//     //response timeout means your request has probably be received by JPUsh Server,please check that whether need to be pushed again.
//     echo 'IsResponseTimeout: ' . $e->isResponseTimeout . $br;
// }

/*$conn=True;
$padsend=False;
if($conn==True&&$padsend==False){
    $result=array("retCode"=>0,"retInfo"=>"wed端联动成功","retCodemobile"=>1,"retCodeInfo"=>"移动端消息推送失败");
    //超时数据处理
 }else if ($conn==False&&$padsend==False) {
     $result=array("retCode"=>1,"retInfo"=>"数据库连接失败！","retCodemobile"=>1,"retCodeInfo"=>"移动端消息推送失败");
 }else if($conn==True&&$padsend==True){
      $result=array("retCode"=>1,"retInfo"=>"wed端联动成功","retCodemobile"=>1,"retCodeInfo"=>"移动端消息推送成功");
 }else if ($conn==False&&$padsend==True) {
      $result=array("retCode"=>1,"retInfo"=>"数据库连接失败！","retCodemobile"=>1,"retCodeInfo"=>"移动端消息推送成功");
 }

 echo json_encode($result);*/
?>