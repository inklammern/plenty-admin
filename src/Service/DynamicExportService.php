<?php

namespace Inkl\PlentyAdmin\Service;

use Inkl\PlentyAdmin\Client\AdminClient;
use League\Csv\Reader;
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

		$csvReader = Reader::createFromString($response->response);
		$csvReader->setDelimiter(';');

		$rows = [];
		foreach ($csvReader->fetchAssoc() as $row)
		{
			$rows[] = $row;
		}

		if (count($rows) > 0)
		{
			return $rows;
		}

		return null;
	}

}
