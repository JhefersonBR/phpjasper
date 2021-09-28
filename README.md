# PHPJasper
_A simplified, easy-to-implement PHP report generator_

### About
PHPJasper is the best solution to compile and process JasperReports (.jrxml & .jasper files) just using PHP, in short: to generate reports using PHP.

## Installation

Install [Composer](http://getcomposer.org) if you don't have it.
```
composer require jheferson-br/phpjasper
```
Or in your file'composer.json' add:

```json
{
    "require": {
        "jheferson-br/phpjasper": "^3.2.0"
    }
}
```

And the just run:

    composer install

and thats it.

----------------------------------------------------------------------------------------------------------------------------

## Examples

#### Compiling

First we need to compile our `JRXML` file into a `JASPER` binary file. We just have to do this one time.

**Note 1:** You don't need to do this step if you are using *Jaspersoft Studio*. You can compile directly within the program.

```php

require __DIR__ . '/vendor/autoload.php';

$input = __DIR__ . '/vendor/geekcom/phpjasper/examples/hello_world.jrxml';   

$jasper = new PHPJasper;
$jasper->compile($input)->execute();
```

This commando will compile the `hello_world.jrxml` source file to a `hello_world.jasper` file.

#### Processing

Now lets process the report that we compile before:

```php

require __DIR__ . '/vendor/autoload.php';

$input = __DIR__ . '/vendor/geekcom/phpjasper/examples/hello_world.jasper';  
$output = __DIR__ . '/vendor/geekcom/phpjasper/examples';    
$options = [ 
    'format' => ['pdf', 'rtf'] 
];

$jasper = new PHPJasper;

$jasper->process(
    $input,
    $output,
    $options
)->execute();
```

Now check the examples folder! :) Great right? You now have 2 files, `hello_world.pdf` and `hello_world.rtf`.

Check the *methods* `compile` and `process` in `src/JasperPHP.php` for more details

#### Listing Parameters

Querying the jasper file to examine parameters available in the given jasper report file:

```php

require __DIR__ . '/vendor/autoload.php';


$input = __DIR__ . '/vendor/geekcom/phpjasper/examples/hello_world_params.jrxml';

$jasper = new PHPJasper;
$output = $jasper->listParameters($input)->execute();

foreach($output as $parameter_description)
    print $parameter_description . '<pre>';
```

### Using database to generate reports

We can also specify parameters for connecting to database:

```php
require __DIR__ . '/vendor/autoload.php';
 

$input = '/your_input_path/your_report.jasper';   
$output = '/your_output_path';
$options = [
    'format' => ['pdf'],
    'locale' => 'en',
    'params' => [],
    'db_connection' => [
        'driver' => 'postgres', //mysql, ....
        'username' => 'DB_USERNAME',
        'password' => 'DB_PASSWORD',
        'host' => 'DB_HOST',
        'database' => 'DB_DATABASE',
        'port' => '5432'
    ]
];

$jasper = new PHPJasper;

$jasper->process(
        $input,
        $output,
        $options
)->execute();
```

**Note 2:**

For a complete list of locales see [Supported Locales](http://www.oracle.com/technetwork/java/javase/java8locales-2095355.html)

### Using MSSQL DataBase

```php
require __DIR__ . '/vendor/autoload.php';


$input = '/your_input_path/your_report.jasper or .jrxml';   
$output = '/your_output_path';
$jdbc_dir = __DIR__ . '/vendor/geekcom/phpjasper/bin/jaspertarter/jdbc';
$options = [
    'format' => ['pdf'],
    'locale' => 'en',
    'params' => [],
    'db_connection' => [
        'driver' => 'generic',
        'host' => '127.0.0.1',
        'port' => '1433',
        'database' => 'DataBaseName',
        'username' => 'UserName',
        'password' => 'password',
        'jdbc_driver' => 'com.microsoft.sqlserver.jdbc.SQLServerDriver',
        'jdbc_url' => 'jdbc:sqlserver://127.0.0.1:1433;databaseName=Teste',
        'jdbc_dir' => $jdbc_dir
    ]
];

$jasper = new PHPJasper;

$jasper->process(
        $input,
        $output,
        $options
    )->execute();
```

### Reports from a XML

```php
require __DIR__ . '/vendor/autoload.php';


$input = '/your_input_path/your_report.jasper';   
$output = '/your_output_path';
$data_file = __DIR__ . '/your_data_files_path/your_xml_file.xml';
$options = [
    'format' => ['pdf'],
    'params' => [],
    'locale' => 'en',
    'db_connection' => [
        'driver' => 'xml',
        'data_file' => $data_file,
        'xml_xpath' => '/your_xml_xpath'
    ]
];

$jasper = new PHPJasper;

$jasper->process(
    $input,
    $output,
    $options
)->execute();
```

### Reports from a JSON

```php
require __DIR__ . '/vendor/autoload.php';


$input = '/your_input_path/your_report.jasper';   
$output = '/your_output_path';

$data_file = __DIR__ . '/your_data_files_path/your_json_file.json';
$options = [
    'format' => ['pdf'],
    'params' => [],
    'locale' => 'en',
    'db_connection' => [
        'driver' => 'json',
        'data_file' => $data_file,
        'json_query' => 'your_json_query'
    ]
];

$jasper = new PHPJasper;

$jasper->process(
    $input,
    $output,
    $options
)->execute();
```
