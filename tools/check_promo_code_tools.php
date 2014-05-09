<?php
check_tourcms_promo_code();

function check_tourcms_promo_code() {
	$promo_code = $_POST['promo_code'];
	require("inc/config.php");
	$channel_id = SiteConfig::get("channel_id");
	if ($promo_code) {
		$promo_code = esc_sql(htmlspecialchars($promo_code));
	
		$code_check = $tourcms->show_promo($promo_code, $channel_id);
		
		if($code_check->error == "OK") {
			echo json_encode(array(
				'value'=>strip_tags($code_check->promo->value->asXML()), 
				'value_type'=>strip_tags($code_check->promo->value_type->asXML())
			));
			exit;
		} else {
			exit;
		}
	}
}
?>