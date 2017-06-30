<?php

namespace Inkl\PlentyAdmin\Client;

class AdminClient
{


	public function __construct($adminUrl, $username, $password)
	{

		$tmpfname = tempnam(sys_get_temp_dir(), 'COOKIE');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $adminUrl);
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['request' => '{"requests":[{"_dataName":"PlentyMarketsLogin", "_moduleName":"user/login", "_searchParams":{"username":"Magentoapi2", "password":"Magentoapi2789", "isGWT":true}, "_writeParams":{}, "_validateParams":{}, "_commandStack":[{"type":"read", "command":"read"}], "_dataArray":{}, "_dataList":{}}], "meta":{"token":""}}']));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie-name');  //could be empty, but cause problems on some hosts
		curl_setopt($ch, CURLOPT_COOKIEFILE, $tmpfname);  //could be empty, but cause problems on some hosts
		$answer = curl_exec($ch);

		if (curl_error($ch)) {
			echo curl_error($ch);
		}

		curl_setopt($ch, CURLOPT_URL, 'https://www.hofstein.de/plenty/admin/gui_call.php?Object=mod_export@GuiDynamicFieldExportView2&Params[gui]=AjaxExportData&gwt_tab_id=-1&presenter_id=&action=ExportDataFormat&formatDynamicUserName=Magento_DE_Item&offset=0&rowCount=6000');
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "");
		$answer = curl_exec($ch);
		print_r($answer);

		if (curl_error($ch)) {
			echo curl_error($ch);
		}

	}

}
