<?php
include_once "callback_json/BSBizMsgCrypt.php";

// 北森上事件订阅器设置的Key信息
$token = "";
$encodingAesKey = "";
$sReceiveId = "";//特殊场景使用，默认留空

$sMsgSig = $_GET['msg_signature'];//从接收消息的URL中获取的msg_signature参数
$sTimeStamp = $_GET['timestamp'];//从接收消息的URL中获取的timestamp参数
$sNonce = $_GET['nonce'];//从接收消息的URL中获取的nonce参数
$sEchoStr = $_GET['echostr'];//从接收消息的URL中获取的echostr参数。注意，此参数必须是urldecode后的值
$rEchoStr = "";//解密后的明文消息内容，用于回包。注意，必须原样返回，不要做加引号或其它处理
$bscpt = new BSBizMsgCrypt($token, $encodingAesKey, $sReceiveId);

/* 判断是否首次验证回调的场景，该场景请求会携带echostr,非首次场景不会携带该参数 */
if (!isset($sEchoStr) && !empty($sEchoStr)) {
	$errCode = $bscpt->VerifyURL($sMsgSig, $sTimeStamp, $sNonce, $sEchoStr, $rEchoStr);
	if ($errCode == 0) {
		echo $rEchoStr;
	} else {
		print("ERR: " . $errCode . "\n\n");
	}
}

/*获取application/json数据，接收并解析消息的body中获取的msg_encrypt数据 */
$sEncryptMsg = file_get_contents('php://input');
//var_dump($sEncryptMsg);
$sMsg = "";// 解析之后的明文

$errCode = $bscpt->DecryptMsg($sMsgSig, $sTimeStamp, $sNonce, $sEncryptMsg, $sMsg);
if ($errCode == 0) {
	// 解密成功，sMsg即为xml格式的明文
	print("done DecryptMsg, sMsg : \n");
    var_dump($sMsg);
	// TODO: 对明文的处理
} else {
	print("ERR: " . $errCode . "\n\n");
	//exit(-1);
}
