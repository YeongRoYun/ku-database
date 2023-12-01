<?php
$document = <<<DOC
<html>
<head><title> PHP와 MySQL연동</title></head>
<body>
<form name="form" action="app/register.php" method="post">
    <p>학번 : <input type="text" name="stu_ID"></p>
    <p>이름 : <input type="text" name="stu_name"></p>
    <p>학과코드 : <input type="text" name="class_code"></p>
    <p>학년 :
        <select name="grade">
            <option value="1">1학년</option>
            <option value="2">2학년</option>
            <option value="3">3학년</option>
            <option value="4">4학년</option>
        </select>
    </p>
    <p>학적사항 :
        <select name="stu_register">
            <option value="재학">재학</option>
            <option value="휴학">휴학</option>
            <option value="졸업">졸업</option>
        </select>
    </p>
    <p><input type="submit" name="formbutton1" value="Submit"/>
    </p>
</form>
</body>
</html>
DOC;
echo $document;
