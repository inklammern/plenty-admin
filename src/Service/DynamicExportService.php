<?php

namespace Inkl\PlentyAdmin\Service;

use Inkl\PlentyAdmin\Client\AdminClient;
use Swap\Exception\Exception;

class DynamicExportService
{

	/** @var AdminClient */
	private $client;

	/**
	 * @param AdminClient $client
	 */
	public function __construct(AdminClient $client)
	{
		$this->client = $client;
	}

	public function exportFormat($formatName, $offset = 0, $rowCount = 6000)
	{

		$call = sprintf('admin/gui_call.php?Object=mod_export@GuiDynamicFieldExportView2&Params[gui]=AjaxExportData&gwt_tab_id=-1&presenter_id=&action=ExportDataFormat&formatDynamicUserName=%s&offset=%d&rowCount=%d', $formatName, $offset, $rowCount);

		$response = $this->client->get($call);

		if ($response->http_status_code !== 200)
		{
			throw new Exception(sprintf('invalid http code: %d', $response->http_status_code));
		}

		$stream = fopen('php://memory', 'r+');
		fwrite($stream, $response->response);
		rewind($stream);

		$items = [];
		while ($item = fgetcsv($stream, 100000, ';'))
		{
			$items[] = $item;
		}

		if (count($items) > 1)
		{
			return $items;
		}
		print_r($items);
		exit;

		print_r($response->response);
		exit;

		echo $response->http_status_code;
		exit;

		print_r($response);

		exit;

		return null;
	}

}
