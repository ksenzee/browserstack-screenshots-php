<?php

namespace BrowserstackScreenshots;

class ScreenshotsClient {

  /**
   * @var \Guzzle\Http\Client
   */
  protected $client;

  /**
   * Constructor.
   *
   * @param \stdClass $credentials
   *   An object with the following properties:
   *   - username: Your Browserstack API username.
   *   - password: Your Browserstack API key.
   *
   * @throws \InvalidArgumentException
   */
  function __construct($credentials) {
    if (empty($credentials->username) || empty($credentials->password)) {
      throw new \InvalidArgumentException('Username and password parameters are required.');
    }
    $this->client = new \Guzzle\Http\Client('http://www.browserstack.com/screenshots');
    $this->client->setDefaultOption('auth', array($credentials->username, $credentials->password, 'Basic'));
  }

  /**
   * Retrieves a list of available operating systems and browsers.
   *
   * @return array
   *   An array of browser/OS version objects.
   *
   * @throws \Guzzle\Http\Exception\RequestException
   */
  function getOsAndBrowsers() {
    $request = $this->client->get('browsers.json');
    $response = $request->send();
    return $response->json();
  }

  /**
   * Generate a set of screenshots.
   *
   * @param string $json
   *   JSON describing the screenshot set you are requesting.
   *
   * @return string
   *
   * @throws \Guzzle\Http\Exception\RequestException
   */
  function generateScreenshots($json) {
    $request = $this->client->post('', array('Content-Type' => 'application/json'), $json);
    $response = $request->send();
    return $response->json();
  }

  /**
   * Returns the current status of a screenshot job.
   *
   * @param string $job_id
   *
   * @return string
   *   A string indicating the status (e.g. 'done').
   *
   * @throws \Guzzle\Http\Exception\RequestException
   */
  function getStatus($job_id) {
    $info = $this->getJobInfo($job_id);
    return $info['state'];
  }

  /**
   * Returns a boolean indicating the status of a screenshot job.
   *
   * @param string $job_id
   *
   * @return boolean
   *   TRUE if the job is finished, FALSE otherwise.
   *
   * @throws \Guzzle\Http\Exception\RequestException
   */
  function jobFinished($job_id) {
    return $this->getStatus($job_id) == 'done';
  }

  /**
   * Retrieves all available information about a screenshot job.
   *
   * @param string $job_id
   *
   * @return \stdClass
   *   An object with all available job information.
   *
   * @throws \Guzzle\Http\Exception\RequestException
   */
  function getJobInfo($job_id) {
    $request = $this->client->get("$job_id.json");
    $response = $request->send();
    $info = $response->json();
    return $info;
  }

}
