<?php

$email;$comment;$captcha;
if(isset($_POST['email'])){
    $email=$_POST['email'];
}if(isset($_POST['comment'])){
    $email=$_POST['comment'];
}if(isset($_POST['g-recaptcha-response'])){
    $captcha=$_POST['g-recaptcha-response'];
}
if(!$captcha){
    echo '<h2>Please check the the captcha form ( sa charge lentement )</h2>';
    exit;
}
$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lf_9SATAAAAACR3U0fm6MUsj2zmszCqk5n8UAYm&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
if($response.success==false)
{
    echo '<h2>no</h2>';
}else
{
    echo '<h2>Thanks for posting comment.</h2>';
}
?>