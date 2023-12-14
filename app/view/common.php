<?php

namespace app\view;

function logoutButton(): string
{
    $button = <<<HTML
<form action="/auth/logout" method="post">
  <input type="submit" value="로그아웃">
</form>
HTML;
    return $button;
}




function menuBar(): string
{
    $css = <<<CSS
<style>
a {text-decoration: none;
    color: black;}

html,body {width: 100%; overflow-x: hidden; overflow-y: auto;}

* {margin: 0; padding: 0;}

.mobile_btn{
    position: absolute;
    top:70px;
    left: 15px;
}

input[id="hamburger"] {
  display: none;
}
input[id="hamburger"] + label {
  display: block;
  width: 60px;
  height: 40px;
  position: relative;
  cursor: pointer;
}
input[id="hamburger"] + label span {
  display: block;
  position: absolute;
  width: 100%;
  height: 5px;
  border-radius: 30px;
  background: #000;
  transition: all 0.35s;
}
input[id="hamburger"] + label span:nth-child(1) {
  top: 0;
}
input[id="hamburger"] + label span:nth-child(2) {
  top: 50%;
  transform: translateY(-50%);
}
input[id="hamburger"] + label span:nth-child(3) {
  bottom: 0;
}
input[id="hamburger"]:checked + label {
  z-index: 99;
}
input[id="hamburger"]:checked + label span {
  background: black;
}
input[id="hamburger"]:checked + label span:nth-child(1) {
  top: 50%;
  transform: translateY(-50%) rotate(45deg);
}
input[id="hamburger"]:checked + label span:nth-child(2) {
  opacity: 0;
}
input[id="hamburger"]:checked + label span:nth-child(3) {
  bottom: 50%;
  transform: translateY(50%) rotate(-45deg);
}
div[class="sidebar"] {
  width: 100%;
  height: 100%;
  background: white;
  position: fixed;
  top: 0;
  left: -100%;
  z-index: 98;
  transition: all 0.35s;
}

input[id="hamburger"]:checked + label + div {
  left: 0;
}

.nav_mobile {
    position: absolute;
    top:119px;
    width: 100%;
    }

.sidebar .nav_mobile li {
	color:black;
    position:relative;
    float:left;
    width:100%;
    list-style-type:none;
    font-size:20px;
    border-bottom:1px solid #ccc;
    font-family: 'Noto Sans KR';
    font-weight:bold;
    padding-top: 20px;
    padding-bottom: 20px;
    text-align: center;
    }

.sidebar a {
    padding: 0;
    text-decoration: none;
    display: block;
}

.sidebar li a{
    display:block;
    font-weight:bold;
    line-height:40px;
    margin:0px;
    padding:0;
    text-align:center;
    text-decoration:none;
    transition: all 0.4s;
    }

.sidebar li a:hover, .sidebar ul li:hover a {

    text-decoration: none;
    color: #eee;
    padding: 0;
    margin: 0;
    border: 0px;
}
</style>
CSS;


    $body = <<<BODY
<div class="mobile_btn">
	<input type="checkbox" id="hamburger" />
	<label for="hamburger">
	  <span></span>
	  <span></span>
	  <span></span>
	</label>
	<div class="sidebar">
    <h2 style="text-align: center; position: relative; top: 75px;"><a href="#">로고</a></h2>
    <hr style="position: relative; top:100px; border: solid 1px black;">
    <ul class="nav_mobile">
      <li><a href="#">메뉴1</a></li>
      <li><a href="#">메뉴2</a></li>
      <li><a href="#">메뉴3</a></li>
      <li><a href="#">메뉴4</a></li>
    </ul>
	</div>
	</div>
BODY;
}
