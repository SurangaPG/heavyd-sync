<?php

require_once "phing/Task.php";

use Symfony\Component\Yaml\Yaml;

/**
 * Get all the date from all the different projects and map them to the
 * central data dir?
 */
class SyncDumpDataTask extends Task {

  /**
   * @var string
   */
  private $outputDir;

  /**
   * @var string
   */
  private $propertyDir;

  /**
   * @var string
   */
  private $projectBaseDir;

  /**
   * @var array
   */
  protected $dataArray = [];

  /**
   * @var array
   */
  protected $outputArray = [];

  /**
   * @var string
   */
  protected $fileName;

  /**
   * Main function.
   */
  public function main() {

    $files = glob($this->getPropertyDir() . '/*.yml');

    foreach ($files as $file) {
      $basename = str_replace('.yml', '', basename($file));
      $this->dataArray[$basename] = Yaml::parse(file_get_contents($file));
    }

    // Generate the file name.
    $this->fileName = $this->dataArray['project']['group'] . '-' . $this->dataArray['project']['machineName'] . '.yml';

    // Add in all the project Data.
    $this->writeProjectDataOutput();
    $this->writeCodeInfoOutput();
    $this->writeSecurityInfoOutput();

    // Add in all the confluence information if relevant.
    if (isset($this->dataArray['confluence'])) {
      $this->writeConfluenceInfoOutput();
    }

    // Add in all the devops information if relevant.
    if (isset($this->dataArray['dev_ops'])) {
      $this->writeDevOpsInfoOutput();
    }

    // Add in all the devops information if relevant.
    if (isset($this->dataArray['instructions'])) {
      $this->writeInstructionsOutput();
    }

    // Add in all the server information.
    if (isset($this->dataArray['server'])) {
      $this->writeServerDataOutput();
    }

    file_put_contents($this->getOutputDir() . '/' . $this->fileName, Yaml::dump($this->outputArray, 6, 2));
  }

  /**
   * Add the data for the "project key" in the standardized info.
   */
  public function writeProjectDataOutput() {
    $this->outputArray['project'] = [
      "auto_sync" => "on", // Flag this item as autosyncinc it's data from the source repository.
      "group" => $this->dataArray['project']['group'],
      "name" => $this->dataArray['project']['name'],
      "label" => $this->dataArray['project']['label'],
      "type" => isset($this->dataArray['project']['type']) ? $this->dataArray['project']['type'] : 'unspecified',
      "team" => isset($this->dataArray['project']['team']) ? $this->dataArray['project']['team'] : 'default',
    ];
  }

  /**
   * Add the data for the "code" subpart.
   */
  public function writeCodeInfoOutput() {
    $this->outputArray['code'] = [
      // @TODO Guess this from the git info.
      "repository" => isset($this->dataArray['project']['repository']['main']) ? $this->dataArray['project']['repository']['main'] : 'unspecified',
      "main_branch" => isset($this->dataArray['project']['branch']['main']) ? $this->dataArray['project']['branch']['main'] : 'unspecified',
    ];
  }

  /**
   * Add the data for the "confluence" subpart.
   */
  public function writeConfluenceInfoOutput() {
    $this->outputArray['confluence'] = $this->dataArray['confluence'];
  }

  /**
   * Add the data for the "confluence" subpart.
   */
  public function writeDevOpsInfoOutput() {
    $this->outputArray['dev_ops'] = $this->dataArray['dev_ops'];
  }

  /**
   * Add the data for the "confluence" subpart.
   */
  public function writeInstructionsOutput() {
    $this->outputArray['instructions'] = $this->dataArray['instructions'];
  }

  /**
   * Add the code for the "security" meta information.
   */
  public function writeSecurityInfoOutput() {
    if (isset($this->dataArray['security'])) {
      $this->outputArray['security'] = $this->dataArray['security'];
    }
    else {
      $this->outputArray['security'] = [
        'autopoll' => FALSE,
        'polled_servers' => [],
      ];
    }
  }

  /**
   * Add the data for the "server key" in the standardized info.
   */
  public function writeServerDataOutput() {
    foreach ($this->dataArray['server'] as $serverKey => $data) {
      // @TODO Might want to standardize this later on.
      $this->outputArray['servers'][$serverKey] = $data;
    }
  }

  /**
   * @return string
   */
  public function getPropertyDir() {
    return $this->propertyDir;
  }

  /**
   * @return string
   */
  public function getOutputDir() {
    return $this->outputDir;
  }

  /**
   * @return string
   */
  public function getProjectBaseDir() {
    return $this->projectBaseDir;
  }

  /**
   * @param string $outputDir
   */
  public function setOutputDir($outputDir) {
    $this->outputDir = $outputDir;
  }

  /**
   * @param string $propertyDir
   */
  public function setPropertyDir($propertyDir) {
    $this->propertyDir = $propertyDir;
  }

  /**
   * @param string $projectBaseDir
   */
  public function setProjectBaseDir($projectBaseDir) {
    $this->projectBaseDir = $projectBaseDir;
  }
}
