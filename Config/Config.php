<?php

namespace HueControl\Config;

/**
 * @author Bert Slagter
 */
class Config
{
	/**
	 * @var array
	 */
	private $data;

	public function __construct()
	{
		$this->loadConfig();
		$this->checkConfig();
	}

	/**
	 * @return string The user config file
	 */
	private function getUserConfigPath()
	{
		return __DIR__ . '/../' . 'config.yaml';
	}

	/**
	 * @return string The default config file
	 */
	private function getDefaultConfigPath()
	{
		return __DIR__ . '/' . 'config-default.yaml';
	}

	/**
	 * Loads config from file
	 */
	private function loadConfig()
	{
		if (!file_exists($this->getUserConfigPath())) {
			$this->createConfig();
		}

		$this->data = yaml_parse_file($this->getUserConfigPath());
	}

	/**
	 * Creates new config file, based on default, or empty if no default found
	 */
	private function createConfig()
	{
		if (file_exists($this->getDefaultConfigPath())) {
			copy($this->getDefaultConfigPath(), $this->getUserConfigPath());
		} else {
			file_put_contents($this->getUserConfigPath(), yaml_emit(array()));
		}
	}

	/**
	 * Save current config to file
	 */
	private function saveConfig()
	{
		file_put_contents($this->getUserConfigPath(), yaml_emit($this->data));
	}

	private function checkConfig()
	{
		$bridgeIp = $this->getConfigValue('bridgeIp');

		if (empty($bridgeIp)) {
			$SSDPClient = new SSDPClient();
			$bridgeIp = $SSDPClient->findBridge();

			if (!empty($bridgeIp)) {
				$this->setConfigValue('bridgeIp', $bridgeIp);
			} else {
				die ('Could not find bridge');
			}
		}

		$bridgeKey = $this->getConfigValue('bridgeKey');

		if (empty($bridgeKey)) {
			$Register = new Register($bridgeIp);
			$bridgeKey = $Register->getNewKey();

			if (!empty($bridgeKey)) {
				$this->setConfigValue('bridgeKey', $bridgeKey);
			} else {
				die ('Could not register at the bridge');
			}
		}
	}

	public function getConfigValue($name)
	{
		if (isset($this->data[$name])) {
			return $this->data[$name];
		} else {
			return null;
		}
	}

	public function setConfigValue($name, $value)
	{
		$this->data[$name] = $value;
		$this->saveConfig();
	}
}
