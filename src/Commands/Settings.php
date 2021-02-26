<?php namespace CodeIgniter\Tasks\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

use CodeIgniter\Tasks\TaskRunner;

/**
 * Base functionality for enable/disable.
 */
trait Settings
{
	/**
	 * location to save.
	 */
	protected $path = WRITEPATH . 'tasks';

	/**
	 * Saves the settings.
	 */
	protected function saveSettings($status)
	{
		$settings = [
			'status' => $status,
		];

		$data = json_encode($settings);

		if (($fp = @fopen($this->path, 'wb')) === false)
		{
			return false;
		}

		flock($fp, LOCK_EX);

		for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($data, $written))) === false)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $settings;
	}

	/**
	 * Gets the settings. If they
	 * have never been saved then create them.
	 */
	protected function getSettings()
	{
		//if settings have never
		if (! is_file($this->path))
		{
			return $this->saveSettings('enabled');
		}

		return json_decode(file_get_contents($this->path), true);
	}
}
