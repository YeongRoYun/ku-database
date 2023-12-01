<?php
namespace app;

$stuId = (int)$_POST["stu_ID"];
$stuName = $_POST["stu_name"];
$classCode = $_POST["class_code"];
$grade = (int)$_POST["grade"];
$stuRegister = $_POST["stu_register"];

$connect = mysqli_connect("127.0.0.1", "root", "1234", "ku-database");
$query = <<<QUERY
INSERT INTO students(id, name, class_code, grade, register)
VALUES ($stuId, "$stuName", "$classCode", $grade, "$stuRegister");
QUERY;
//print($query);
mysqli_query($connect, $query);

$result = mysqli_query($connect, "SELECT * FROM students");
$row_num = mysqli_num_rows($result);
echo "<table border=2 width=300><tr>
<td width=100><p align=center>학번</p></td>
<td width=100><p align=center>이름</p></td>
<td width=100><p align=center>학과코드</p></td>
<td width=100><p align=center>학년</p></td>
<td width=100><p align=center>학적사항</p></td></tr>";

for($i=1;$i<=$row_num;$i++){
    $array = mysqli_fetch_array($result);
    echo "<tr>
<td width=100><p align=center>$array[0]</p></td>
<td width=100><p align=center>$array[1]</p></td>
<td width=100><p align=center>$array[2]</p></td>
<td width=100><p align=center>$array[3]</p></td>
<td width=100><p align=center>$array[4]</p></td></tr>";}
echo "</table>";
mysqli_close($connect);
