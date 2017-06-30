<?php

namespace Inkl\PlentyApi\Service;

use Inkl\Csv\Service\StringService as CsvStringService;
use Inkl\PlentyApi\Client\ClientInterface;

class DynamicExportService
{

	/** @var ClientInterface */
	private $client;
	/** @var CsvStringService */
	private $csvStringService;

	/**
	 * DynamicExportService constructor.
	 * @param ClientInterface $client
	 * @param CsvStringService $csvStringService
	 */
	public function __construct(ClientInterface $client, CsvStringService $csvStringService)
	{
		$this->client = $client;
		$this->csvStringService = $csvStringService;
	}

	public function exportFormat($formatId, $formatName, $offset = 0, $rowCount = 1000)
	{
		$result = $this->client->call('GetDynamicExport', [
			'FormatID' => $formatId,
			'FormatName' => $formatName,
			'Offset' => $offset,
			'RowCount' => $rowCount
		]);

		if (!isset($result->Success) || $result->Success != '1' || !isset($result->Content->item)) throw new \Exception('dynamic export failed');

		$content = '';
		foreach ($result->Content->item as $item)
		{

			if (!isset($item->Value)) continue;

			$content .= (string)$item->Value . "\n";
		}

		$items = $this->csvStringService->toArray($content, ';');
		if (count($items) > 0)
		{
			return $items;
		}

		return null;
	}

}
