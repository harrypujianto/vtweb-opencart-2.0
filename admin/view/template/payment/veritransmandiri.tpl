<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">

	<!--header, breadcrumb & button-->
		<div class="page-header">
			<div class="container-fluid">
			  <div class="pull-right">
					<button type="submit" form="form-ppexpress" onclick="$('#form').submit();" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
					<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			  </div>
			  <h1><?php echo $heading_title; ?></h1>
			  <ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			  </ul>
			</div>
		</div>
	<!--header, breadcrumb & button-->


	<div class="container-fluid">
		<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
				</div>

			<!--error-->
			<?php if (isset($error['error_warning'])) { ?>
			<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error['error_warning']; ?>
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
			<?php } ?>
			<!--error-->

			<div class="panel-body">
				  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">


						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-mode"><?php echo $entry_status; ?></label>
							<div class="col-sm-3">
							  <select name="veritransmandiri_status" id="input-mode" class="form-control">
								<?php $options = array('1' => $text_enabled, '0' => $text_disabled) ?>
								<?php foreach ($options as $key => $value): ?>
								  <option value="<?php echo $key ?>" <?php if ($key == $veritransmandiri_status) echo 'selected' ?> ><?php echo $value ?></option>
								<?php endforeach ?>
							  </select>
							</div>
						</div>
						<!-- Status -->


						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-merchant-id"><?php echo $entry_display_name; ?></label>
							<div class="col-sm-3">
							  <input type="text" name="veritransmandiri_display_name" value="<?php echo $veritransmandiri_display_name; ?>" id="input-merchant-id" class="form-control" />
							</div>
							<div class="col-sm-3">
								<?php if (isset($error['display_name'])) { ?>
								<div class="col-sm-3"> <?php echo $error['display_name']; ?> </div>
								<?php } ?>
							</div>
						</div>
						<!-- Display name -->

						<div class="form-group v2_settings sensitive required">
							<label class="col-sm-2 control-label" for="input-mode"><?php echo $entry_environment; ?></label>
							<div class="col-sm-3">
								<select name="veritransmandiri_environment" id="input-mode" class="form-control">
									<?php $options = array('development' => 'Sandbox', 'production' => 'Production') ?>
									<?php foreach ($options as $key => $value): ?>
									<option value="<?php echo $key ?>" <?php if ($key == $veritransmandiri_environment) echo 'selected' ?> ><?php echo $value ?></option>
									<?php endforeach ?>
								</select>
							</div>
							<div class="col-sm-3">
								<?php if (isset($error['environment'])) { ?>
								<div class="col-sm-3"> <?php echo $error['environment']; ?> </div>
								<?php } ?>
							</div>
						</div>
						<!-- Environment (v2-specific) -->

						<div class="form-group required v2_settings sensitive">
							<label class="col-sm-2 control-label" for="input-server-key"><?php echo $entry_server_key; ?></label>
							<div class="col-sm-3">
							  <input type="text" name="veritransmandiri_server_key_v2" value="<?php echo $veritransmandiri_server_key_v2; ?>" id="input-server-key" class="form-control" />
							</div>
							<div class="col-sm-3">
								<?php if (isset($error['server_key_v2'])) { ?>
								<div class="col-sm-3"> <?php echo $error['server_key_v2']; ?> </div>
								<?php } ?>
							</div>
						</div>
						<!-- Server Key (v2-specific) -->

						<div class="form-group v2_settings sensitive required">
						<label class="col-sm-2 control-label" for="input-merchant-id">Enable Installment</label>
							<div class="col-sm-3">
								<select name="veritransmandiri_installment_option" id="installmentOption" class="form-control">
								<?php $options = array('off' => 'Off', 'all_product' => 'All Products', 'certain_product' => 'Certain Product') ?>
								<?php foreach ($options as $key => $value): ?>
								<option value="<?php echo $key ?>" <?php if ($key == $veritransmandiri_installment_option) echo 'selected' ?> ><?php echo $value ?></option>
								<?php endforeach ?>
								</select>
							</div>
						</div>
						<!-- Select Installment Option (v2-specific) -->

						<div class="form-group required v2_settings sensitive">
							<label class="col-sm-2 control-label" for="input-installment-term"><?php echo $entry_installment_mandiri_term; ?></label>
							<div class="col-sm-3">
								<input type="text" name="veritransmandiri_installment_mandiri_term" value="<?php echo $veritransmandiri_installment_mandiri_term; ?>" id="input-installment-term" class="form-control" />
							  <span>use comma (,) to separate each term. e.g : 3,6,12</span>
							</div>
							<div class="col-sm-3">
								<?php if (isset($error['installment_mandiri_term'])) { ?>
								<div class="col-sm-3"> <?php echo $error['installment_mandiri_term']; ?> </div>
								<?php } ?>
							</div>
						</div>
						<!-- Installment term -->

						<div class="form-group required v2_settings sensitive">
							<label class="col-sm-2 control-label" for="input-threshold"><?php echo $entry_threshold; ?></label>
							<div class="col-sm-3">
							  <input type="textarea" name="veritransmandiri_threshold" value="<?php echo $veritransmandiri_threshold; ?>" id="input-threshold" class="form-control" />
							  <span>Minimum transaction Limit</span>
							</div>
							<div class="col-sm-3">
								<?php if (isset($error['threshold'])) { ?>
								<div class="col-sm-3"> <?php echo $error['threshold']; ?> </div>
								<?php } ?>
							</div>
						</div>
						<!-- Threshold -->

						<div class="form-group required v2_settings sensitive">
							<label class="col-sm-2 control-label" for="input-bin-number"><?php echo $entry_bin_number; ?></label>
							<div class="col-sm-3">
							  <input type="textarea" name="veritransmandiri_bin_number" value="<?php echo $veritransmandiri_bin_number; ?>" id="input-bin-number" class="form-control" />
							  <span>use comma (,) to separate each number. e.g: mandiri,bni,455612,4811,521111.</span>
							</div>
							<div class="col-sm-3">
								<?php if (isset($error['bin_number'])) { ?>
								<div class="col-sm-3"> <?php echo $error['bin_number']; ?> </div>
								<?php } ?>
							</div>
						</div>
						<!-- Bin number -->

					<?php foreach (array('vtweb_success_mapping', 'vtweb_failure_mapping', 'vtweb_challenge_mapping') as $status): ?>
						<div class="form-group required">
						<label class="col-sm-2 control-label" for="input-merchant-id"><?php echo ${'entry_' . $status} ?></label>
							<div class="col-sm-3">
								<select name="<?php echo 'veritransmandiri_' . $status ?>" id="veritransmandiriPaymentType" class="form-control">
							  <?php foreach ($order_statuses as $option): ?>
								<option value="<?php echo $option['order_status_id'] ?>" <?php if ($option['order_status_id'] == ${'veritransmandiri_' . $status}) echo 'selected' ?> ><?php echo $option['name'] ?></option>
							  <?php endforeach ?>
								</select>
							</div>
						</div>
					<?php endforeach ?>
					<!-- VTWeb Mapping -->


						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-merchant-id"><?php echo $entry_currency_conversion; ?></label>
							<div class="col-sm-3">
							  <input type="text" name="veritransmandiri_currency_conversion" value="<?php echo $veritransmandiri_currency_conversion; ?>" class="form-control" />
							  <span>Set to 1 if your default currency is IDR</span>
							</div>
							<div class="col-sm-3">
								<?php if (isset($error['currency_conversion'])) { ?>
								<div class="col-sm-3"> <?php echo $error['currency_conversion']; ?> </div>
								<?php } ?>
							</div>
						</div>
					<!-- Currency -->

					<div class="form-group v2_vtweb_settings">
						<label class="col-sm-2 control-label" for="input-merchant-id"><?php echo $entry_geo_zone; ?></label>
							<div class="col-sm-3">
								<select name="veritransmandiri_geo_zone_id"  class="form-control">
								<option value="0"><?php echo $text_all_zones; ?></option>
								<?php foreach ($geo_zones as $geo_zone) { ?>
									<?php if ($geo_zone['geo_zone_id'] == $veritransmandiri_geo_zone_id) { ?>
									<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
									<?php } else { ?>
									<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
									<?php } ?>
							<?php } ?>
								</select>
							</div>
						</div>
						<!-- Geo Zone -->

						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-merchant-id"><?php echo $entry_sort_order; ?></label>
							<div class="col-sm-1">
							  <input size="1" type="text" name="veritransmandiri_sort_order" value="<?php echo $veritransmandiri_sort_order; ?>" class="form-control" />
							</div>
						</div>
						<div>
							<center><font size="1">version 1.0</font></center>
						</div>

				  </form>
			 </div>
		</div>
	</div>
		<!-- content -->
</div>


<script>
  $(function() {
    function sensitiveOptions() {
      var api_version = 2;
      var payment_type = $('#veritransPaymentType').val();
      var api_string = 'v' + api_version + '_settings';
      var payment_type_string = payment_type;
      var api_payment_type_string = 'v' + api_version + '_' + payment_type + '_settings';
      $('.sensitive').hide();
      $('.' + api_string).show();
      $('.' + payment_type_string).show();
      $('.' + api_payment_type_string).show();

      // temporarily hide vt-direct if the API version is 2
      if (api_version == 2)
      {
        $('#veritransPaymentTypeContainer').hide();
      } else{
        $('#veritransPaymentTypeContainer').show();
      }

    }

    function setupVisibility(){
      $('.installment').hide();
      var installmentOption = $("#installmentOption").val();
      var bankInstallment = [];
      $('.installmentBank:checked').each(function(){
          bankInstallment.push($(this).val());
      });

      $('.'+installmentOption).show();
      if (installmentOption == 'all_product'){
        $.each(bankInstallment, function(index,value){
          $('.'+installmentOption+'_'+value).show();
        });

      }
    }

    $("#veritransApiVersion").on('change', function(e, data) {
      sensitiveOptions();
    });

    $("#veritransPaymentType").on('change', function(e, data) {
      sensitiveOptions();
    });

	$('#installmentOption').ready(function(){

		var installmentOption = $('#installmentOption').val();
		if(installmentOption == 'off')
		{
			//alert(installmentOption);
			$('.install_term').hide();
		}
		else if(installmentOption == 'certain_product')
		{
			//alert(installmentOption);
			$('.install_term').hide();
		}
		else if(installmentOption == 'all_product')
		{
			$('.install_term').hide();
			<?php foreach (array('bni' => 'BNI', 'mandiri' => 'MANDIRI') as $name_bank => $display_bank): ?>
	      $('.installmentBank_<?=$name_bank?>').ready(function(){

				var thisCheck = $('.installmentBank_<?=$name_bank?>');
			if(thisCheck.is(':checked'))
				{
					//alert('aaasdad');
					$('.installmentTerm_<?=$name_bank?>').show();
				}
				else
				{
					//alert('qweqwe');
					$('.installmentTerm_<?=$name_bank?>').hide();
				}
			});


			$('.installmentBank_<?=$name_bank?>').click(function(){

				var thisCheck = $('.installmentBank_<?=$name_bank?>');
			if(thisCheck.is(':checked') && $("#installmentOption").val() =="all_product")
				{
					$('.installmentTerm_<?=$name_bank?>').show();
				}
				else
				{
					$('.installmentTerm_<?=$name_bank?>').hide();
				}
			});
			<?php endforeach?>
		}
	});

	<?php foreach (array('bni' => 'BNI', 'mandiri' => 'MANDIRI') as $name_bank => $display_bank): ?>
		$('.installmentBank_<?=$name_bank?>').click(function(){
				var thisCheck = $('.installmentBank_<?=$name_bank?>');
			if(thisCheck.is(':checked') && $("#installmentOption").val() =="all_product")
				{
					$('.installmentTerm_<?=$name_bank?>').show();
				}
				else
				{
					$('.installmentTerm_<?=$name_bank?>').hide();
				}
			});
	<?php endforeach?>

	$("#installmentOption").change(function(){
	var installmentOption = $('#installmentOption').val();

		if(installmentOption == "off")
		{
			$('.install_term').hide();
		}
		else if(installmentOption == "certain_product")
		{
			$('.install_term').hide();
		}
		else if(installmentOption == "all_product")
		{
			<?php foreach (array('bni' => 'BNI', 'mandiri' => 'MANDIRI') as $name_bank => $display_bank): ?>
				var thisCheck = $('.installmentBank_<?=$name_bank?>');
			if(thisCheck.is(':checked') && $("#installmentOption").val() =="all_product")
				{
					$('.installmentTerm_<?=$name_bank?>').show();
				}
				else
				{
					$('.installmentTerm_<?=$name_bank?>').hide();
				}
			<?php endforeach?>

		}

	});

    $("#installmentOption").on('change', function(e, data) {
      setupVisibility();
    });

    $(".installmentBank").click(function(){
           setupVisibility();
        });

    sensitiveOptions();
    setupVisibility();

  });
</script>
<?php echo $footer; ?>
