<?php

	###########################################################
	### Name: telephonylist.php                             ###
	### Functions: Manage List and Upload Leads             ###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016            ###
	### Version: 4.0                                        ###
	### Written by: Alexander Abenoja & Noel Umandap        ###
	### License: AGPLv2                                     ###
	###########################################################

	require_once('./php/UIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	$perm = $ui->goGetPermissions('list,customfields', $_SESSION['usergroup']);
	$checkbox_all = $ui->getCheckAll("list");
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("Lists"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

        <!-- Wizard Form style -->
        <link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />

        <!-- DATA TABLES CSS -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

        <?php print $ui->creamyThemeCSS(); ?>

        <!-- Datetime picker CSS -->
		<link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

        <!-- Data Tables JS -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <!-- Date Picker JS -->
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/0.71/jquery.csv-0.71.min.js"></script>
		<!-- SELECT2 CSS -->
   		<link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">
		<style type="text/css">
			#progress-wrp {
				border: 1px solid #0099CC;
				border-radius: 3px;
				position: relative;
				width: 100%;
				height: 30px;
				background-color: #367fa9;
			}
			
			#progress-wrp .progress-bar {
				border-radius: 3px;
				position: absolute;
				width: 1%;
				height: 100%;
				background-color: #00a65a;
			  /* background-color: #4CAF50; */
			}
			
			#progress-wrp .status {
				top:3px;
				left:50%;
				position:absolute;
				display:inline-block;
				color: white;
				font-style: bold;
				/* color: #000000; */
			}
		</style>

		<style type="text/css">
			.select2-container{
				width: 100% !important;
			}
		</style>
		
        <script type="text/javascript">
			
			// Progress bar function
			function goProgressBar() {
				
				var formData = new FormData($('#upload_form')[0]);
				var progress_bar_id 		= '#progress-wrp'; //ID of an element for response output
				var percent = 0;
				
				var result_output 			= '#output'; //ID of an element for response output
				var my_form_id 				= '#upload_form'; //ID of an element for response output
				var submit_btn  = $(this).find("input[type=button]"); //btnUpload
	
				formData.append('tax_file', $('input[type=file]')[0].files);
				
				$.ajax({
					url : "./php/AddLoadLeads.php",
					type: "POST",
					data : formData,
					contentType: false,
					cache: false,
					processData:false,
					maxChunkSize: 1000000000,
					maxRetries: 100000000,
					retryTimeout: 5000000000,
					xhr: function(){
						//upload Progress
						var xhr = $.ajaxSettings.xhr();
						if (xhr.upload) {
							xhr.upload.addEventListener('progress', function(event) {
								
								var position = event.loaded || event.position;
								var total = event.total;
								if (event.lengthComputable) {
									percent = Math.ceil(position / total * 100);
								}
								
								//update progressbar
								$(progress_bar_id +" .progress-bar").css("width", + percent +"%");
								$(progress_bar_id + " .status").text(percent +"%");
								//$(progress_bar_id + " .status").innerHTML = percent + '%';
								
								if(percent === 100) {
									
									//$('#dStatus').css("display", "block");
									//$('#dStatus').css("color", "#4CAF50");
									//$('#qstatus').text("File Uploaded Successfully. Please wait for the TOTAL of leads uploaded.(Do not refresh the page)");
									//$('#qstatus').text("Data Processing. Please Wait.");
									//sweetAlert("Oops...", "Something went wrong!", "error");
									
									//var uploadMsgTotal = "Total Leads Uploaded: "+res;
					
									swal({
										title: "<?php $lh->translateText('csv_upload_complete'); ?>",
										text: "<?php $lh->translateText('data_processing'); ?>",
										type: "info",
										showCancelButton: false,
										closeOnConfirm: false
									  });
									
								}
								
							}, true);
							
						}
						return xhr;
					},
					mimeType:"multipart/form-data"
				}).done(function(res){
					
					//$(result_output).html(res); //output response from server
					//submit_btn.val("Upload").prop( "disabled", false); //enable submit button once ajax is done
					//$(my_form_id)[0].reset(); //reset form
					//$('#dStatus').css("display", "block");
					//$('#dStatus').css("color", "#4CAF50");
					//$('#qstatus').text("Total leads uploaded: "+res);
					
					var uploadMsgTotal = "<?php $lh->translateText('total_leads_upload'); ?>: "+res;
					
					swal({
							title: "<?php $lh->translateText('data_processing_complete'); ?>",
							text: uploadMsgTotal,
							type: "success"
						},
						function(){
							location.reload();
							$(".preloader").fadeIn();
						}
					);
					
				});
								
			}
			// End Progress bar function
			
		</script>


    </head>

    <?php print $ui->creamyBody(); ?>

        <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("list_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-home"></i> <?php $lh->translateText("home"); ?></a></li>
                       <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("lists"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
		<?php
			if ($perm->list->list_read !== 'N') {
				/****
				** API to get data of tables
				****/
				$lists = $ui->API_goGetAllLists($_SESSION['usergroup']);
		?>
                	<div class="row">
                        <div class="col-lg-<?=($perm->list->list_upload === 'N' ? '12' : '9')?>">
		                <div class="panel panel-default">
							<div class="panel-body">
							<legend id="legend_title"><?php $lh->translateText("lists"); ?></legend>
								<div role="tabpanel">
							
									<ul role="tablist" class="nav nav-tabs nav-justified">
			
									<!-- List panel tabs-->
										 <li role="presentation" class="active">
											<a href="#list_tab" aria-controls="list_tab" role="tab" data-toggle="tab" class="bb0">
												<?php $lh->translateText("list"); ?></a>
										 </li>
									<!-- DNC panel tab -->
										 <li role="presentation">
											<a href="#dnc_tab" aria-controls="dnc_tab" role="tab" data-toggle="tab" class="bb0">
												<?php $lh->translateText("dnc"); ?> </a>
										 </li>
									</ul>
									  
									<!-- Tab panes-->
									<div class="tab-content bg-white">
										<!--==== List ====-->
										<div id="list_tab" role="tabpanel" class="tab-pane active">
											<table class="table table-striped table-bordered table-hover" id="table_lists">
												<thead>
													<tr>
													<th style="color: white;">Pic</th>
													<th><?php echo $checkbox_all;?></th>
													<th class='hide-on-medium hide-on-low'><?php $lh->translateText("list_id"); ?></th>
													<th><?php $lh->translateText("name"); ?></th>
													<th class='hide-on-medium hide-on-low'><?php $lh->translateText("status"); ?></th>
													<th class='hide-on-medium hide-on-low'><?php $lh->translateText("leads_count"); ?></th>
													<th class='hide-on-medium hide-on-low'><?php $lh->translateText("campaign"); ?></th>
													<th class='hide-on-medium hide-on-low'><?php $lh->translateText("field"); ?></th>
													<th class='hide-on-medium hide-on-low'><?php $lh->translateText("action"); ?></th>
													</tr>
												</thead>
												<tbody>
												<?php
												for($i=0;$i < count($lists->list_id);$i++){
												// if no entry in user list
												
												if($lists->active[$i] == "Y"){
												$lists->active[$i] = $lh->translationFor("active");
												}else{
												$lists->active[$i] = $lh->translationFor("inactive");
												}
												
												$action_list = $ui->getUserActionMenuForLists($lists->list_id[$i], $lists->list_name[$i], $perm);
												$checkbox = '<label for="'.$lists->list_id[$i].'"><div class="checkbox c-checkbox"><label><input name="" class="check_list" id="'.$lists->list_id[$i].'" type="checkbox" value="Y"><span class="fa fa-check"></span> </label></div></label>';
												?>
												<tr>
												<td><avatar username='<?php echo $lists->list_name[$i];?>' :size='36'></avatar></td>
												<td style="width:10%;"><?php echo $checkbox;?></td>
												<td class='hide-on-low'><strong>
													<?php
													if (($perm->list->list_update !== 'N' && !preg_match("/^(998|999)$/", $lists->list_id[$i]))) {
													?>
													<a class='edit-list' data-id='<?php echo $lists->list_id[$i];?>'>
													<?php
													}
													?><?php echo $lists->list_id[$i];?>
												</strong></td>
												<td><?php echo $lists->list_name[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $lists->active[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $lists->tally[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $lists->campaign_id[$i];?></td>
												<td class='hide-on-medium hide-on-low'><?php echo $lists->cf_count[$i];?></td>
												<td><?php echo $action_list;?></td>
												</tr>
												<?php
												
												}
												?>
												</tbody>
											</table>
										</div><!-- /.list-tab -->
										<!--==== DNC ====-->
										<div id="dnc_tab" role="tabpanel" class="tab-pane">
											<table class="table table-striped table-bordered table-hover" id="table_dnc">
												<thead>
													<tr>
													<th><?php $lh->translateText("phone_number"); ?></th>
													<th class='hide-on-medium hide-on-low'><?php $lh->translateText("campaign"); ?></th>
													<th><?php $lh->translateText("action"); ?></th>
													</tr>
												</thead>
												<tbody>
													<tr id="#dnc_result">
														<td colspan="3"><center><span id="dnc_error">- - - <?php $lh->translateText("search_filter_dnc");?> - - -</span></center></td>
													</tr>
												</tbody>
											</table>
										</div><!-- /.dnc-tab -->
										
									</div><!-- /.tab-content -->
								</div><!-- /.tab-panel -->
							</div><!-- /.body -->
						</div><!-- /.panel -->
					</div><!-- /.col-lg-9 -->

<?php
if ($perm->list->list_upload !== 'N') {
?>
	<div class="col-lg-3" id="list_sidebar">
	<h3 class="m0 pb-lg"><?php $lh->translateText("upload_import"); ?></h3>
		<form action="./php/AddLoadLeads.php" method="POST" enctype="multipart/form-data" id="upload_form" name="upload_form">
			<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
			<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
			<div class="form-group">
				<label><?php $lh->translateText("list_id"); ?>:</label>
					<div class="form-group">
					<!-- <select id="select2-1" class="form-control" name="list_id"> -->
						<select id="list_id" class="form-control select2" name="list_id" required>
						<option value="" selected disabled></option>
						<?php
						for($i=0;$i<count($lists->list_id);$i++){
						echo '<option value="'.$lists->list_id[$i].'">'.$lists->list_id[$i].' - '.$lists->list_name[$i].'</option>';
						}
						?>
						</select>
					</div>
				<div class="form-group">
					<label><?php $lh->translateText("duplicate_check"); ?> :</label>
					<SELECT size="1" NAME="goDupcheck" ID="goDupcheck" TITLE="Duplicate Check - Will check phone numbers on the lead file and cross reference it with all phone numbers on a specific campaign or in all List ID." class="form-control select2">
					<OPTION value="NONE"><?php $lh->translateText("no_duplicate_check"); ?></OPTION>
					<OPTION value="DUPLIST"><?php $lh->translateText("check_phones_in_list_id"); ?></OPTION>
					<OPTION value="DUPCAMP"><?php $lh->translateText("check_phones_in_campaign-lists"); ?></OPTION>
					</SELECT>
				</div>
			</div>
			<div class="form-group">
			
			<label><?php $lh->translateText("csv_file"); ?>:</label>
				<div class="form-group" id="dvImportSegments">
				<div class="input-group">
				<input type="text" class="form-control file-name" name="file_name" placeholder="<?php $lh->translateText("csv_file"); ?>" required>
				<span class="input-group-btn">
				<button type="button" class="btn browse-btn  btn-primary" type="button"><?php $lh->translateText("browse"); ?></button>
				</span>
				</div>
				<input type="file" class="file-box hide" name="file_upload" id="txtFileUpload" accept=".csv">
			</div>
			
			<div id="goMappingContainer"></div>
			<div id="goValuesContainer"></div>
			</div>
			
			<!-- Progress bar -->
			<div class="form-group">
				<div id="progress-wrp">
				<div class="progress-bar"></div >
				<div class="status">0%</div>
				</div>
				<div id="output"><!-- error or success results --></div>
				<br />
				<div>
				<div class="alert alert-success" style="display:none;" id="dStatus"> 
				<div id="qstatus">  </div>
				</div>
				</div>
			</div>
			<!-- End Progress bar -->
			
			<div class="form-group">
			<input type="button" id="btnUpload" name="btnUpload" value="<?php $lh->translateText("update"); ?>" class="btn btn-primary" onClick="goProgressBar();">
			<!--										<div class="col-lg-12" style="margin-top: 10px;">
			<div class="alert alert-success" style="display:none;" id="dStatus"> 
			<div id="qstatus">  </div>
			</div>
			</div>-->
			</div>
			
			<div id="jMapFieldsdiv">
			<span id="jMapFieldsSpan"></span>
			</div>
		</form>
	<?php
	if(isset($_GET['message'])){
	echo '<div class="col-lg-12" style="margin-top: 10px;">';
	if($_GET['message'] == "success"){
	echo '<div class="alert alert-success"> <strong>Succes: </strong>'.$_GET['RetMesg']." leads uploaded</div>";
	}else{
	echo '<div class="alert alert-success"> <strong>Error: </strong>'.$_GET['RetMesg']."</div>";
	}
	echo '</div>';
	}
	#var_dump($_GET);
	?>
	
	</div><!-- ./upload leads -->
	
	<div class="col-lg-3" id="dnc_sidebar" style="display:none;">
	<h3 class="m0 pb-lg"><?php $lh->translateText("filter_dnc"); ?></h3>
		<div class="form-group">
			<label for="search_dnc"><?php $lh->translateText("search"); ?></label>
			<div class="has-clear">
				<input type="text" placeholder="<?php $lh->translateText("search_phone"); ?>" id="search_dnc" class="form-control mb">
				<span class="form-control-clear fa fa-close form-control-feedback"></span>
			</div>
		</div>
		<div class="clearfix">
			<button type="button" class="pull-left btn btn-default" id="dnc_search_button"> <?php $lh->translateText("search"); ?></button>
		</div>
	</div><!-- ./ dnc search -->
<?php
}
?>

</div>
<?php
} else {
print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
}
?>
</section><!-- /.content -->
</aside><!-- /.right-side -->
<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
</div><!-- ./wrapper -->

<!-- FIXED ACTION BUTTON -->
<div class="action-button-circle" data-toggle="modal" data-target="#list-modal" id="list_fab" title="<?php $lh->translateText("list_wizard"); ?>">
<?php print $ui->getCircleButton("list_and_call_recording", "plus"); ?>
</div>
<div class="action-button-circle" data-toggle="modal" data-target="#dnc-modal" id="dnc_fab" style="display:none;" title="<?php $lh->translateText("add_delete_dnc"); ?>">
<?php print $ui->getCircleButton("list_and_call_recording", "pencil-square-o"); ?>
</div>
<?php
	/*
	* APIs for add form
	*/
	$campaign = $ui->API_getListAllCampaigns();
	$max_list = max($lists->list_id);
	$min_list = min($lists->list_id);
	
	if($max_list >= 99999999){
		for($i=1;$i < $max_list;$i++){
			if(!in_array($i, $lists->list_id)){
				$next_list = $i;
				$i = $max_list;
			}
		}
	}else{
		$next_list = $max_list + 1;
	}
	$next_listname = "ListID ".$next_list;
	$datenow = date("j-n-Y");
	$next_listdesc = "Auto-generated - ListID - ".$datenow;
?>
	<div class="modal fade" id="list-modal" tabindex="-1"aria-labelledby="list-modal" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header" id="scripts"><b><?php $lh->translateText("list_wizard"); ?> » <?php $lh->translateText("add_list"); ?></b></h4>
				</div>
				<div class="modal-body wizard-content">

				<form method="POST" id="create_form" role="form">
					<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
					<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
				<div class="row">
				<h4><?php $lh->translateText("list_info"); ?>
				   <br>
				   <small><?php $lh->translateText("list_details"); ?></small>
				</h4>
				<fieldset>
					<div class="form-group mt">
						<label class="col-sm-3 control-label" for="auto_generate"><?php $lh->translateText("auto_generated"); ?>:</label>
						<div class="col-sm-9 mb">
							<label class="col-sm-3 checkbox-inline c-checkbox" for="auto_generate">
								<input type="checkbox" id="auto_generate" checked>
								<span class="fa fa-check"></span>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="add_list_id"><?php $lh->translateText("list_id"); ?>:</label>
						<div class="col-sm-9 mb">
							<input type="number" class="form-control" name="add_list_id" id="add_list_id" placeholder="<?php $lh->translateText("list_id"); ?>" value="<?php echo $next_list;?>" minlength="1" maxlength="8" disabled required/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="list_name"><?php $lh->translateText("list_name"); ?>:</label>
						<div class="col-sm-9 mb">
							<input type="text" class="form-control" name="list_name" id="list_name" placeholder="<?php $lh->translateText("list_name"); ?>" value="<?php echo $next_listname;?>" maxlength="30" required/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="list_desc"><?php $lh->translateText("list_description"); ?>:</label>
						<div class="col-sm-9 mb">
							<input type="text" class="form-control" name="list_desc" id="list_desc" placeholder="<?php $lh->translateText("list_description"); ?>"  value="<?php echo $next_listdesc;?>" maxlength="255" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="campaign_select"><?php $lh->translateText("campaign"); ?>: </label>
						<div class="col-sm-9 mb">
							<select name="campaign_select" class="form-control">
								<?php
									for($i=0; $i < count($campaign->campaign_id);$i++){
										echo "<option value='".$campaign->campaign_id[$i]."'> ".$campaign->campaign_name[$i]." </option>";
									}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="status"><?php $lh->translateText("active"); ?>: </label>
						<div class="col-sm-9 mb">
							<select name="status" class="form-control">
								<option value="Y" selected><?php $lh->translateText("go_yes"); ?></option>
								<option value="N" ><?php $lh->translateText("go_no"); ?></option>
							</select>
						</div>
					</div>
				</fieldset>
				</div>
				</form>

				</div> <!-- end of modal body -->
			</div>
		</div>
	</div><!-- end of modal -->

	<div id="modal_custom_field_copy" class="modal fade" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?php $lh->translateText("copy_custom_wizard"); ?></h4>
				</div>
				<div class="modal-body">
					<form id="copy_cf_form" class="form-horizontal" style="margin-top: 10px;">
						<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
						<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
						<div class="form-group">
							<label class="control-label col-lg-4"><?php $lh->translateText("copy_from_list_id"); ?>:</label>
							<div class="col-lg-8">
								<input type="hidden" class="form-control list-from" value="" name="list_from">
								<input type="text" class="form-control list-from-label" value="" readonly>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-4"><?php $lh->translateText("copy_to_list_id"); ?>:</label>
							<div class="col-lg-8">
								<select class="form-control select2" name="list_to">
									<?php for($i=0;$i < count($lists->list_id);$i++){ ?>
										<option value="<?php echo $lists->list_id[$i]; ?>"><?php echo $lists->list_id[$i].' - '.$lists->list_name[$i];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-4"><?php $lh->translateText("copy_option"); ?>:</label>
							<div class="col-lg-8">
								<select class="form-control select2" name="copy_option">
									<option value="APPEND"><?php $lh->translateText("append"); ?></option>
									<option value="UPDATE"><?php $lh->translateText("UPDATE"); ?></option>
									<option value="REPLACE"><?php $lh->translateText("replace"); ?></option>
								</select>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php $lh->translateText("close"); ?></button>
					<button type="button" class="btn btn-success btn-copy-cf" data-dismiss="modal"><?php $lh->translateText("copy"); ?></button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- End of modal -->
	
	<!-- Modal -->
	<div id="dnc-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b><?php $lh->translateText("add_delete_dnc"); ?></b></h4>
	      </div>
	      <div class="modal-body">
			<form id="dnc_form">
				<input type="hidden" name="user_id" value="<?php echo $user->getUserId();?>">
				<input type="hidden" name="log_user" value="<?php echo $_SESSION['user'];?>">
				<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup'];?>">
				<div class="form-group mt">
					<label class="col-md-3 control-label"><?php $lh->translateText("list"); ?>:</label>
					<div class="col-md-9 mb">
						<select id="campaign_id" class="form-control select2" name="campaign_id" required>
							<option value="INTERNAL"><?php $lh->translateText("internal_dnc"); ?></option>
							<?php
								for($i=0;$i<count($campaign->campaign_id);$i++){
									echo '<option value="'.$campaign->campaign_id[$i].'">'.$campaign->campaign_id[$i].' - '.$campaign->campaign_name[$i].'</option>';
								}
							?>
						</select>
					</div>
				</div>
				<div class="form-group mt">
					<label class="col-md-3 control-label"><?php $lh->translateText("phone_number"); ?>:</label>
					<div class="col-md-9 mb">
						<textarea rows="15" cols="17" name="phone_numbers" id="phone_numbers" style="resize:none"></textarea><br/>
						<small class="text-danger"><?php $lh->translateText("limit25_per_submit"); ?></small>
					</div>
				</div>
				<div class="form-group mt">
					<label class="col-md-3 control-label"><?php $lh->translateText("add_delete_dnc"); ?>:</label>
					<div class="col-md-4">
						<select id="stageDNC" class="form-control" name="stageDNC" required>
							<option value="ADD"><?php $lh->translateText("ADD"); ?> DNC LIST</option>
							<option value="DELETE"><?php $lh->translateText("DELETE"); ?> DNC LIST</option>
						</select>
					</div>
				</div>
			</form>
	      </div>
		  
	      <div class="modal-footer">
			<button type="button" class="btn btn-primary" id="submit_dnc"><?php $lh->translateText("submit"); ?></button>
	        <button type="button" class="btn btn-default" data-dismiss="modal"><?php $lh->translateText("close"); ?></button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	
		<?php print $ui->standardizedThemeJS();?>
		<!-- JQUERY STEPS-->
  		<script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>
		
		<!-- SELECT2-->
   		<script src="theme_dashboard/select2/dist/js/select2.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				
				// on tab change, change sidebar
				$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
					var target = $(e.target).attr("href"); // activated tab
					
					if(target == "#list_tab"){
						$("#list_sidebar").show();
						$("#list_fab").show();
						$("#dnc_sidebar").hide();
						$("#dnc_fab").hide();
						$("#legend_title").text("Lists");
					}
					if(target == "#dnc_tab"){
						$("#dnc_sidebar").show();
						$("#dnc_fab").show();
						$("#list_sidebar").hide();
						$("#list_fab").hide();
						$("#legend_title").text("DNC");
					}
				});
				
				$('body').on('keypress', '#search_dnc', function(args) {
				    if (args.keyCode == 13) {
				        $("#dnc_search_button").click();
				        return false;
				    }
				});
				
				/*****
				** Functions for List
				*****/

					// initialize datatable
					$('#table_lists').DataTable( {
			            deferRender:    true,
				    	select: true,
				    	stateSave: true,
						"aaSorting": [[ 1, "asc" ]],
						"aoColumnDefs": [{
							"bSearchable": false,
							"aTargets": [ 0, 1, 8 ]
						},{
							"bSortable": false,
							"aTargets": [ 0, 1, 8 ]
						}]
					});
					
					/**
					* Add list
					**/
					var form = $("#create_form"); // init form wizard

				    form.validate({
				        errorPlacement: function errorPlacement(error, element) { element.after(error); }
				    });
					
					form.children("div").steps({
						headerTag: "h4",
						bodyTag: "fieldset",
						transitionEffect: "slideLeft",
						onStepChanging: function (event, currentIndex, newIndex)
						{
							// Allways allow step back to the previous step even if the current step is not valid!
							if (currentIndex > newIndex) {
								return true;
							}
		
							// Clean up if user went backward before
							if (currentIndex < newIndex)
							{
								// To remove error styles
								$(".body:eq(" + newIndex + ") label.error", form).remove();
								$(".body:eq(" + newIndex + ") .error", form).removeClass("error");
							}
		
							form.validate().settings.ignore = "";
							return form.valid();
						},
						onFinishing: function (){
							form.validate().settings.ignore = "";
							return form.valid();
						},
						onFinished: function (){
							$('#finish').text("<?php $lh->translateText("loading"); ?>");
							$('#finish').attr("disabled", true);
							$('#add_list_id').attr("disabled", false);
							// Submit form via ajax
							$.ajax({
	                            url: "./php/AddTelephonyList.php",
	                            type: 'POST',
	                            data: $('#create_form').serialize(),
	                            success: function(data) {
	                              // console.log(data);
									$('#finish').text("<?php $lh->translateText("submit"); ?>");
									$('#finish').attr("disabled", false);
									if(data == 1){
									  swal({title: "<?php $lh->translateText("add_list_success"); ?>",text: "<?php $lh->translateText("add_list_success"); ?>",type: "success"},function(){window.location.href = 'telephonylist.php';});
									}else{
										sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>", "error");
									}
	                            }
	                        });
						}
					});

					/**
					  * Edit user details
					 */
					$(document).on('click','.edit-list',function() {
						var url = './edittelephonylist.php';
						var id = $(this).attr('data-id');
						//alert(extenid);
						var form = $('<form action="' + url + '" method="post"><input type="hidden" name="modifyid" value="'+id+'" /></form>');
						$('body').append(form);  // This line is not necessary
						
						$(form).submit();
					});
					
					/**
					  * Edit user details
					 */
					$(document).on('click','.download-list',function() {
						var url = 'php/ExportList.php';
						var id = $(this).attr('data-id');
						//alert(extenid);
						var form = $('<form action="' + url + '" method="post"><input type="hidden" name="listid" value="'+id+'" /></form>');
						$('body').append(form);  // This line is not necessary
						$(form).submit();
					});
					

					/***
					** Delete
					***/
		            $(document).on('click','.delete-list',function() {
						var id = $(this).attr('data-id');
						var log_user = '<?=$_SESSION['user']?>';
						var log_group = '<?=$_SESSION['usergroup']?>';
						swal({
							title: "<?php $lh->translateText("are_you_sure"); ?>?",
							text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "<?php $lh->translateText("list_delete"); ?>!",
							cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
							closeOnConfirm: false,
							closeOnCancel: false
							},
							function(isConfirm){
								if (isConfirm) {
	
									$.ajax({
										url: "./php/DeleteTelephonyList.php",
										type: 'POST',
										data: {
											listid: id,
											log_user: log_user,
											log_group: log_group
										},
										success: function(data) {
										console.log(data);
											if(data == 1){
												swal({title: "<?php $lh->translateText("delete_list_success"); ?>",text: "<?php $lh->translateText("delete_list_success_msg"); ?>",type: "success"},function(){window.location.href = 'telephonylist.php';});
											}else{
											   sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>!", "error");
											}
										}
									});
	
								} else {
									swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");
								}
							}
						);
		            });
					
					$(document).on('click','.delete-multiple',function() {
					var arr = $('input:checkbox.check_list').filter(':checked').map(function () {
						return this.id;
					}).get();
					var log_user = '<?=$_SESSION['user']?>';
					var log_group = '<?=$_SESSION['usergroup']?>';
					swal({
							title: "<?php $lh->translateText("are_you_sure"); ?>",
							text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "<?php $lh->translateText("delete_multiple_list"); ?>!",
							cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
							closeOnConfirm: false,
							closeOnCancel: false
						},
							function(isConfirm){
								if (isConfirm) {
									$.ajax({
										url: "./php/DeleteTelephonyList.php",
										type: 'POST',
										data: {
											listid:arr,
											action: "delete_selected",
											log_user: log_user,
											log_group: log_group
										},
										success: function(data) {
										console.log(data);
											if(data == 1){
												swal({title: "<?php $lh->translateText("delete_list_success"); ?>",text: "<?php $lh->translateText("delete_list_success_msg"); ?>!",type: "success"},function(){window.location.href = 'telephonylist.php';});
											}else{
												sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>! "+data, "error");
											}
										}
									});
								} else {
										swal("Cancelled", "<?php $lh->translateText("cancel_msg"); ?>", "error");
								}
							}
			            );
			        });
					
					$(document).on('click', '.copy-custom-fields', function(){
						var list_id = $(this).data('id');
						var list_name = $(this).data('name');

						$('.list-from').val(list_id);
						$('.list-from-label').val(list_id + ' - ' + list_name);
						$('#modal_custom_field_copy').modal('show');
					});

					$(document).on('click', '.btn-copy-cf', function(){
						var form_data = new FormData($("#copy_cf_form")[0]);
						swal({
							title: "<?php $lh->translateText("are_you_sure"); ?>",
							text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "<?php $lh->translateText("yes_copy_custom_fields"); ?>",
							cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>",
							closeOnConfirm: false,
							closeOnCancel: false
							},
							function(isConfirm){
								if (isConfirm) {
									$.ajax({
										url: "./php/CopyCustomFields.php",
										type: 'POST',
										data: form_data,
										// dataType: 'json',
										cache: false,
										contentType: false,
										processData: false,
										success: function(data) {
											// console.log(data);
											if(data == "success"){
												swal({
														title: "<?php $lh->translateText("success"); ?>",
														text: "<?php $lh->translateText("custom_fields_copied"); ?>",
														type: "success"
													},
													function(){
														location.reload();
														$(".preloader").fadeIn();
													}
												);
											}else{
													sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>! "+ data, "error");
											}
										}
									});
								} else {
								swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");
								}
							}
						);
					});

					// $('#call-playback-modal').modal('show');
					
					$(document).on('click', '#auto_generate', function(){
					//  alert( this.value ); // or $(this).val()
						if($('#auto_generate').is(":checked")){
							$('#add_list_id').val("<?php echo $next_list;?>");
							$('#list_name').val("<?php echo $next_listname;?>");
							$('#list_desc').val("<?php echo $next_listdesc;?>");
							$('#add_list_id').prop("disabled", true);
						}
						if(!$('#auto_generate').is(":checked")){
							$('#add_list_id').val("");
							$('#list_name').val("");
							$('#list_desc').val("");
							$('#add_list_id').prop("disabled", false);
						}
					});

				/****
				** Functions for upload leads
				*****/
					//initialize single selecting
					$('#select2-1').select2({
				        theme: 'bootstrap'
				    });

						$('.select2').select2({
									theme: 'bootstrap'
						});

					$('.browse-btn').click(function(){
						$('.file-box').click();
					});

					$('.file-box').change(function(){
						var myFile = $(this).prop('files');
						var Filename = myFile[0].name;

						$('.file-name').val(Filename);
						console.log($(this).val());
					});

				//-- end
				
				// DNC Search
					$(document).on('click','#dnc_search_button',function() {
					//init_contacts_table.destroy();
						if($('#search_dnc').val() != ""){
							$('#dnc_search_button').text("<?php $lh->translateText("searching"); ?>");
							$('#dnc_search_button').attr("disabled", true);
						}else{
							$('#dnc_search_button').text("<?php $lh->translateText("search"); ?>");
							$('#dnc_search_button').attr("disabled", false);
						}
						
						$.ajax({
							url: "search_dnc.php",
							type: 'POST',
							data: {
								search_dnc : $('#search_dnc').val()
							},
							success: function(data) {
								$('#dnc_search_button').text("<?php $lh->translateText("searching"); ?>");
								$('#dnc_search_button').attr("disabled", false);
								//console.log(data);
								if(data != ""){
									$('#table_dnc').html(data);
									$('#table_dnc').DataTable({
									"bDestroy" : true
									});
									$('#dnc_error').html("");
								}else{
									$('#dnc_error').text("<?php $lh->translateText("no_results"); ?>");
								}
							}
						});
					});
				
				// DNC Submit
					$(document).on('click','#submit_dnc',function() {
						$('#submit_dnc').text("<?php $lh->translateText("submitting"); ?>");
						$('#submit_dnc').attr("disabled", true);
						
						if ($('#phone_numbers').val() !== ''){
							$.ajax({
								url: "php/ActionDNC.php",
								type: 'POST',
								data: $('#dnc_form').serialize(),
								success: function(data) {
									$('#submit_dnc').text("<?php $lh->translateText("add_delete_dnc"); ?>");
									$('#submit_dnc').attr("disabled", false);
									
									if(data == "added"){
										swal({title: "<?php $lh->translateText("added_new"); ?> DNC", text: "<?php $lh->translateText("add_dnc"); ?>", type: "success"},function(){location.reload();});
									} else if(data == "deleted"){
										swal({title: "<?php $lh->translateText("deleted"); ?> DNC", text: "<?php $lh->translateText("delete_dnc"); ?>", type: "success"},function(){location.reload();});
									} else if(data == "already exist"){
										sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("dnc_already_exist"); ?>", "error");
									} else if(data == "does not exist"){
										sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("dnc_do_not_exist"); ?>", "error");
									} else{
										sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?> "+ data, "error");
									}
								}
							});
						} else {
							$('#submit_dnc').text("<?php $lh->translateText("submit"); ?>");
							$('#submit_dnc').attr("disabled", false);
							swal("<?php $lh->translateText("dnc_incomplete"); ?>", "<?php $lh->translateText("dnc_incomplete_msg"); ?>", "error");
						}
					});
				
				// Delete DNC
					$(document).on('click','.delete-dnc',function() {
						var phone_number = $(this).data('id');
						var campaign = $(this).data('campaign');
						
						$.ajax({
							url: "php/ActionDNC.php",
							type: 'POST',
							data: {
								phone_numbers : phone_number,
								campaign_id : campaign,
								stageDNC : "DELETE",
								user_id : <?php echo $user->getUserId();?>,
								log_user: '<?php echo $_SESSION['user'];?>',
								log_group: '<?php echo $_SESSION['usergroup'];?>'
							},
							success: function(data) {
								//console.log(data);
								if(data == "deleted"){
									swal({title: "<?php $lh->translateText("deleted"); ?>", text: "<?php $lh->translateText("delete_dnc"); ?>", type: "success"},function(){location.reload();});
								} else if(data == "already exist"){
									swal({title: "<?php $lh->translateText("oups"); ?>", text: "<?php $lh->translateText("dnc_already_exist"); ?>", type: "error"},function(){location.reload();});
								} else if(data == "does not exist"){
									sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("dnc_do_not_exist"); ?>", "error");
								} else{
									sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?> "+ data, "error");
								}
							}
						});
					});
				
				$('#phone_numbers').keypress(function(event){
					if((event.ctrlKey === false && ((event.which < 48 || event.which > 57) && event.which !== 13 && event.which !== 8)) && (event.keyCode !== 9 && event.keyCode !== 46 && (event.keyCode < 37 || event.keyCode > 40)))
					return false;
				});
				
				var lines = 25;
				
				$('#phone_numbers').keydown(function(e) {
					newLines = $(this).val().split("\n").length;
				
					if(e.keyCode == 13 && newLines >= lines) {
						return false;
					}
				});

				$('#phone_numbers').blur(function() {
					this.value = this.value.replace('/[^0-9\r\n]/g','');
				});
				
			});
		</script>

		<?php print $ui->creamyFooter();?>
    </body>
</html>
