<?php
 header('content-type:text/html;charset=utf-8');
 require_once "../../dbutil/db.oracle.param.php";
 $conn =get_conn();
 $arr=$_REQUEST;
 $sql="update tbl_temp_aq set flag=2 where jlbh=".$arr["jlbh"];
 $arr1=db_add_delete($sql, $conn);
 echo json_encode($arr1);
?>