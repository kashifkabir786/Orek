<html>
<head>
<title> Non-Seamless-kit</title>
</head>
<body>
<center>

<?php include('Crypto.php')?>
<?php 

	error_reporting(0);
	
	$merchant_data='';
	$working_key='7345AA75227ACD6013D9EE9E814839B0';//Shared by CCAVENUES
	$access_code='AVVB49LA10AZ92BVZA';//Shared by CCAVENUES
	
	foreach ($_POST as $key => $value){
		$merchant_data.=$key.'='.$value.'&';
	}

	$encrypted_data=encrypt($merchant_data,$working_key); // Method for encrypting the data.

	$process_url='https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
	$process_test_url='https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

?>
<form method="post" name="redirect" action="<?php echo $process_test_url; ?>"> 
<?php
echo "<input type=hidden name=encRequest value=$encrypted_data>";
echo "<input type=hidden name=access_code value=$access_code>";
?>
</form>
</center>
<script language='javascript'>document.redirect.submit();</script>
</body>
</html>

