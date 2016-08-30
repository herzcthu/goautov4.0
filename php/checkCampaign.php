<?php

	###################################################
	### Name: checkCampaign.php 					###
	### Functions: Check Add/Edit Campaign			###
	###           Disposition & Lead Filter Details ###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Alexander Jim H. Abenoja		###
	### License: AGPLv2								###
	###################################################

	require_once('goCRMAPISettings.php');

	$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass; #Password goes here. (required)
	$postfields["goAction"] = "goCheckCampaign"; #action performed by the [[API:Functions]]. (required)
	$postfields["responsetype"] = responsetype; #json. (required)
	$postfields["campaign_id"] = $_POST['campaign_id']; 

	if(isset($_POST['status'])){
		$postfields["status"] = $_POST['status'];
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);
	$output = json_decode($data);

	if($output->result == "success"){
		echo $output->result;
	}else{
		if($output->result == "fail"){
			echo $output->status;
		}
	}

?>