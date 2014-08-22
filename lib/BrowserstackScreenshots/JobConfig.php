<?php

namespace BrowserstackScreenshots;

class JobConfig {

  protected $config = array();

  /**
   * Constructor.
   *
   * @param array $config
   *   An array of configuration options to be validated against the
   *   Browserstack schema. Optional.
   */
  function __construct($config = array()) {
    if (empty($config)) {
      return;
    }

    $json = json_decode($config);
    if ($json) {
      $this->config = $json;
    }
    else {
      $this->config = $config;
    }
    $this->validate();
  }

  /**
   * Returns a JSON string representing the screenshot job request.
   *
   * @param bool $validate
   *   If TRUE, will perform some basic checks to try to determine whether the
   *   request is valid according to the Browserstack API documentation.
   *
   * @return string
   *   A JSON representation of the screenshot job request.
   *
   * @throws \Exception
   */
  function getJson($validate = TRUE) {
    if ($validate) {
      $this->validate();
    }
    return json_encode($this->config);
  }

  /**
   * Sets the URL of the page you wish to screenshot.
   *
   * @param string $url
   */
  function setUrl($url) {
    $this->config['url'] = $url;
  }

  /**
   * Sets the callback URL that you want Browserstack to call on job completion.
   *
   * @param string $url
   */
  function setCallbackUrl($url) {
    $this->config['callback_url'] = $url;
  }

  /**
   * Sets the screen resolution to use on Windows screenshots.
   *
   * @param string $resolution
   *   One of '1024x768' or '1280x1024'.
   * @throws \Exception
   */
  function setWinRes($resolution) {
    if (in_array($resolution, array('1024x768', '1280x1024'))) {
      $this->config['win_res'] = $resolution;
    }
    else {
      throw new \Exception('Windows resolution must be 1024x768 or 1280x1024; ' . $resolution . ' given.');
    }
  }

  /**
   * Sets the screen resolution to use on OS X screenshots.
   *
   * @param string $resolution
   *   One of '1024x768', '1280x960', '1280x1024', '1600x1200', or '1920x1080'.
   * @throws \Exception
   */
  function setMacRes($resolution) {
    if (in_array($resolution, array('1024x768','1280x960','1280x1024','1600x1200','1920x1080'))) {
      $this->config['mac_res'] = $resolution;
    }
    else {
      throw new \Exception('OSX resolution must be 1024x768, 1280x960, 1280x1024, 1600x1200, or 1920x1080; ' . $resolution . ' given.');
    }
  }

  /**
   * Sets the quality of the desired screenshot.
   *
   * @param string $quality
   *   Either 'original' or 'compressed'.
   *
   * @throws \Exception
   */
  function setQuality($quality) {
    if (in_array($quality, array('original', 'compressed'))) {
      $this->config['quality'] = $quality;
    }
    else {
      throw new \Exception('Quality must be "original" or "compressed"; ' . $quality . ' given.');
    }
  }

  /**
   * Sets the amount of time (in seconds) to wait before taking the screenshot.
   *
   * @param int $seconds
   *   The number of seconds to wait.
   *
   * @throws \Exception
   */
  function setWaitTime($seconds) {
    if (is_numeric($seconds)) {
      $this->config['wait_time'] = intval($seconds);
    }
    else {
      throw new \Exception('Wait time must be a number of seconds; ' . $seconds . ' given.');
    }
  }

  /**
   * Sets the screen orientation to use.
   *
   * @param string $orientation
   *   Either 'portrait' or 'landscape'.
   *
   * @throws \Exception
   */
  function setOrientation($orientation) {
    if (in_array($orientation, array('portrait', 'landscape'))) {
      $this->config['orientation'] = $orientation;
    }
    else {
      throw new \Exception('Orientation must be "portrait" or "landscape"; ' . $orientation . ' given.');
    }
  }

  /**
   * Determines whether the request should go through a tunnel.
   *
   * @see http://www.browserstack.com/local-testing#command-line
   *
   * @param bool $local
   *   TRUE if the request should go through a tunnel you have previously
   *   set up on your server, FALSE if the request can go over the public Web.
   *
   * @throws \Exception
   */
  function setLocal($local) {
    if (is_bool($local)) {
      $this->config['local'] = $local;
    }
    else {
      throw new \Exception('Local must be a boolean; ' . $local . ' given.');
    }
  }

  /**
   * Adds a browser to the request.
   *
   * @param array $browser_config
   *   An associative array representing one browser, with the following
   *   keys:
   *   - os: "Windows", "OS X", "ios", "android"
   *   - os_version: "7", "8.1", "Mavericks"
   *   - browser
   *   - browser_version
   *   - device
   *
   */
  function addBrowser($browser_config) {
    $this->validateBrowser($browser_config);
    $this->config['browsers'][] = $browser_config;
  }

  /**
   * Validates a browser configuration array.
   *
   * @param array $browser_config
   *   An array representing one browser.
   *
   * @throws \Exception
   */
  function validateBrowser($browser_config) {
    $valid_options = array('os', 'os_version', 'browser', 'browser_version', 'device');
    $required_options = array('os', 'os_version');

    // Make sure no invalid keys exist.
    $invalid_options = array_diff(array_keys($browser_config), $valid_options);
    if (!empty($invalid_options)) {
      throw new \Exception('Invalid browser option(s) ' . join(', ', $invalid_options) . ' were given. Valid options are ' . join(', ', $valid_options));
    }

    // Make sure all required keys exist.
    $omitted_options = array_diff($required_options, array_keys($browser_config));
    if (!empty($omitted_options)) {
      throw new \Exception('The following required browser options were omitted: ' . join(', ', $omitted_options));
    }

    // Make sure either a device or a browser version was included.
    if (empty($browser_config['device']) && empty($browser_config['browser_version'])) {
      throw new \Exception('For each browser, either a device or a browser_version must be specified.');
    }
  }

  /**
   * Validates a configuration array against the Browserstack schema.
   *
   * This is a best-effort attempt only.
   *
   * @param array $config
   *   The configuration array to be validated.
   *
   * @throws \Exception
   */
  function validate($config = array()) {
    if (empty($config)) {
      $config = $this->config;
    }
    if (!isset($config['url'])) {
      throw new \Exception('The URL key is required.');
    }
    if (empty($config['browsers'])) {
      throw new \Exception('At least one browser is required.');
    }
    foreach ($config['browsers'] as $browser) {
      $this->validateBrowser($browser);
    }
  }
}
