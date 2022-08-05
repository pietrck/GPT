<?php

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://api.movidesk.com/public/v1/tickets?$filter=createdDate+ge+2022-08-01T00:00:00.00z&$expand=owner,actions($select=id;$expand=createdBy),actions($expand=timeAppointments),actions($select=description)&token=ea3a0753-5ba4-4548-b780-457e4c5b6a40&$select=id,subject,createdDate',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => array(
		"Content-Type: application/json",
	),
));

$tickets = json_decode(curl_exec($curl), true);

foreach ($tickets as $ticket) {
	print_r(json_encode($ticket));exit;
	foreach ($ticket['actions'] as $action) {
		if (count($action['timeAppointments']) > 0) {
			echo $action['timeAppointments'][0]['workTime'];exit;
		}
	}
}