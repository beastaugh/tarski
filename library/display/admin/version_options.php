<?php if(get_tarski_option('update_notification')) { ?>
	<?php if($status == 'not_current') { ?>
		<div id="tarski_update_notification" class="updated">
			<p>
				<?php printf( __('A new version of the Tarski theme, version %1$s, %2$s. Your installed version is %3$s.','tarski'), '<strong>'. $latest. '</strong>', '<a href="'. $latest_link. '">'. __('is now available','tarski'). '</a>', '<strong>'. $current. '</strong>' ); ?>
			</p>
		</div>
	<?php } ?>
<?php } ?>