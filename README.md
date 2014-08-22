## Installation

1. Get Composer: Download the [`composer.phar`](https://getcomposer.org/composer.phar) executable or use the installer.

    ``` sh
    $ curl -sS https://getcomposer.org/installer | php
    ```

    If you already have Composer installed globally, skip this step.

2. Create a composer.json file specifying BrowserstackScreenshots as a
dependency. You can have Composer do this for you by using this command:

    ``` sh
    $ php composer.phar require ksenzee/browserstack-screenshots-php:*
    ```

    Or, if you already have Composer installed:

    ``` sh
    $ composer require ksenzee/browserstack-screenshots-php:*
    ```

    Or if you'd rather, you can create your composer.json file by hand:

    ``` json
    {
        "require": {
            "ksenzee/browserstack-screenshots-php": "*"
        }
    }
    ```

3. Use Composer to install BrowserstackScreenshots (and anything else you
listed in your composer.json file):

    ``` sh
    $ php composer.phar install
    ```

    Or, if you have Composer installed globally:

    ``` sh
    $ composer install
    ```

## Instantiating the client

``` php

require_once 'vendor/autoload.php';

$credentials = array(
    'username' => 'janedoe',
    'password' => 'abc123',
);
$client = new \BrowserstackScreenshots\ScreenshotsClient($credentials);
```


## Requesting a set of screenshots

``` php
// The JobConfig class is designed to help you generate JSON that meets the
// criteria at http://www.browserstack.com/screenshots/api#generate-screenshots.
$config = new \BrowserstackScreenshots\JobConfig();
$config->setUrl('http://www.google.com');
$config->setWaitTime(5);
$config->setQuality('original');
$config->addBrowser(array('os' => 'Windows', 'os_version' => '7', 'browser' => 'ie', 'browser_version' => '11.0'));
$config->addBrowser(array('os' => 'ios', 'os_version' => '6.0', 'device' => 'iPhone 4S (6.0)'));

// You can use it to generate the JSON to describe your job:
$json = $config->getJson();

// ... or you can skip the JobConfig class entirely and write your own JSON:
$json = '{"url":"https:\/\/www.google.com","wait_time":5,"quality":"original","browsers":[{"os":"Windows","os_version":"7","browser":"ie","browser_version":"11.0"},{"os":"ios","os_version":"6.0","device":"iPhone 4S (6.0)"}]}';

// Once you have valid JSON describing a URL and a set of browsers, use it to
// send a POST request to start a screenshot job:
print "Requesting screenshots:\n";
$request_info = $client->generateScreenshots($json);
$job_id = $request_info['job_id'];
```

## Checking on job status

``` php
// See whether the job is finished:
$finished = $client->jobFinished($job_id);

// Get more details on the job status (whether it's done, queued, or timed out):
$job_status = $client->getStatus($job_id);

// Or just retrieve all available information about your job:
$job_info = $client->getJobInfo($job_id);

```
