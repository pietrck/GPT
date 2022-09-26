//Moviedesk
/*
$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://api.movidesk.com/public/v1/tickets?$filter=createdDate+ge+2021-11-25T00:00:00.00z&$expand=owner,clients($expand=organization),actions($select=id;$expand=createdBy),actions($expand=timeAppointments),actions($select=description)&token=ea3a0753-5ba4-4548-b780-457e4c5b6a40&$select=id,subject,createdDate',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => array(
		"Content-Type: application/json",
	),
));

$apontamentos = [];

$tickets = json_decode(curl_exec($curl), true);

foreach ($tickets as $ticket) {

	$id = $ticket['id'];
	$subject = $ticket['subject'];
	$organization = "Cliente sem organização";

	if ($ticket['clients'][0]['organization'] <> null) {
		$organization = $ticket['clients'][0]['organization']['businessName'];
	} 
	$requester = $ticket['clients'][0]['businessName'];

	foreach ($ticket['actions'] as $action) {
		if (count($action['timeAppointments']) > 0) {
			$temp = [
				$organization,
				$requester,
				$id,
				$subject,
				$action['id'],
				str_replace(["\n"], "", $action['description']),
				$action['createdBy']['businessName'],
				$action['timeAppointments'][0]['date'],
				$action['timeAppointments'][0]['workTime'],
			];

			array_push($apontamentos, $temp);
		}
	}

	$x++;
}

echo $x;

$spreadsheetId = '1my8BpdMsfNw2paA_0aFAGeYrrgnR1jw7N0Qsj8HuuTs';

$gsheet->insert("teste!A2",$apontamentos,"calendly",$spreadsheetId);

*/