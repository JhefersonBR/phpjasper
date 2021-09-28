<?php
class PHPJasper
{

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $executable;

    /**
     * @var string
     */
    protected $pathExecutable;

    /**
     * @var bool
     */
    protected $windows;

    /**
     * @var array
     */
    protected $formats = [
        'pdf',
        'rtf',
        'xls',
        'xlsx',
        'docx',
        'odt',
        'ods',
        'pptx',
        'csv',
        'html',
        'xhtml',
        'xml',
        'jrprint'
    ];

    /**
     * PHPJasper constructor
     */
    public function __construct()
    {
        $this->executable = 'jasperstarter';
        $this->pathExecutable = __DIR__ . '/../bin/jasperstarter/bin';
        $this->windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? true : false;
    }

    /**
     * @return string
     */
    private function checkServer()
    {
        return $this->command = $this->windows ? $this->executable : $this->executable;
    }

    /**
     * @param string $input
     * @param string $output optional
     * @return $this
     * @throws Exception\InvalidInputFile
     */
    public function compile(string $input, string $output = '')
    {
        if (!is_file($input)) {
            throw new Exception('Invalid Input File');
        }

        $this->command = $this->checkServer();
        $this->command .= ' compile ';
        $this->command .= '"' . realpath($input) . '"';

        if (!empty($output)) {
            $this->command .= ' -o ' . "\"$output\"";
        }

        return $this;
    }

    /**
     * @param string $input
     * @param string $output
     * @param array $options
     * @return $this
     * @throws Exception\InvalidInputFile
     * @throws Exception\InvalidFormat
     */
    public function process(string $input, string $output, array $options = [])
    {
        $options = $this->parseProcessOptions($options);

        if (!$input) {
            throw new Exception('Invalid Input File');
        }

        $this->validateFormat($options['format']);

        $this->command = $this->checkServer();

        if ($options['locale']) {
            $this->command .= " --locale {$options['locale']}";
        }

        $this->command .= ' process ';
        $this->command .= "\"$input\"";
        $this->command .= ' -o ' . "\"$output\"";

        $this->command .= ' -f ' . join(' ', $options['format']);

        if ($options['params']) {
            $this->command .= ' -P ';
            foreach ($options['params'] as $key => $value) {
                $this->command .= " " . $key . '="' . $value . '" ' . " ";
            }
        }

        if ($options['db_connection']) {
            $mapDbParams = [
                'driver' => '-t',
                'username' => '-u',
                'password' => '-p',
                'host' => '-H',
                'database' => '-n',
                'port' => '--db-port',
                'jdbc_driver' => '--db-driver',
                'jdbc_url' => '--db-url',
                'jdbc_dir' => '--jdbc-dir',
                'db_sid' => '--db-sid',
                'xml_xpath' => '--xml-xpath',
                'data_file' => '--data-file',
                'json_query' => '--json-query'
            ];

            foreach ($options['db_connection'] as $key => $value) {
                $this->command .= " {$mapDbParams[$key]} {$value}";
            }
        }

        if ($options['resources']) {
            $this->command .= " -r {$options['resources']}";
        }

        return $this;
    }

    /**
     * @param array $options
     * @return array
     */
    protected function parseProcessOptions(array $options)
    {
        $defaultOptions = [
            'format' => ['pdf'],
            'params' => [],
            'resources' => false,
            'locale' => false,
            'db_connection' => []
        ];

        return array_merge($defaultOptions, $options);
    }

    /**
     * @param $format
     * @throws Exception\InvalidFormat
     */
    protected function validateFormat($format)
    {
        if (!is_array($format)) {
            $format = [$format];
        }

        foreach ($format as $value) {
            if (!in_array($value, $this->formats)) {
                throw new Exception('Invalid Format');
            }
        }
    }

    /**
     * @param string $input
     * @return $this
     * @throws \Exception
     */
    public function listParameters(string $input)
    {
        if (!is_file($input)) {
            throw new Exception('Invalid Input File');
        }

        $this->command = $this->checkServer();
        $this->command .= ' list_parameters ';
        $this->command .= '"' . realpath($input) . '"';

        return $this;
    }

    /**
     * @param bool $user
     * @return mixed
     */
    public function execute($user = false)
    {
        $this->validateExecute();
        $this->addUserToCommand($user);

        $returnVar = 0;

        chdir($this->pathExecutable);
        $eturnVar = shell_exec($this->command);
        
        if ($returnVar !== 0) {
            throw new Exception($returnVar);
        }

        return $output;
    }

    /**
     * @return string
     */
    public function output()
    {
        return $this->command;
    }

    /**
     * Prints the command.
     *
     * @return void
     */
    public function printOutput()
    {
        print $this->command . "\n";
    }

    /**
     * @param $user
     */
    protected function addUserToCommand($user)
    {
        if ($user && !$this->windows) {
            $this->command = 'su -u ' . $user . " -c \"" . $this->command . "\"";
        }
    }

    protected function validateExecute()
    {
        if (!$this->command) {
            throw new Exception('Command invalid');
        }

        if (!is_dir($this->pathExecutable)) {
            throw new Exception('Invalid Resource Directory');
        }
    }
}
