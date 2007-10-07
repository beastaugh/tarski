<?php if(get_tarski_option("update_notification")) { ?>
	<?php if($status == "not_current") ?>
		<div id="tarski_update_notification" class="updated">
			<p>
				<?php echo __('A new version of the Tarski theme, version ','tarski'). '<strong>'. $latest. '</strong>'. __(', ','tarski'). '<a href="'. $latest_link. '">'. __('is now available','tarski'). '</a>'. __('. Your installed version is ','tarski'). '<strong>'. $current. '</strong>'. __('.','tarski'); ?>
			</p>
		</div>
<?php } ?>