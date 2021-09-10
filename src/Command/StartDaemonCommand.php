<?php
declare(ticks = 1);
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Psr\Log\LoggerInterface;

use Symfony\Component\Process\Process;

class StartDaemonCommand extends Command
{
    protected $pidPath;

    /**
     *
     * @var int
     */
    protected $pid;

    // Время выполнения команды в секундах
    protected $executionTime;

    // Максимально-допустимое время работы команды в секундах
    protected $maxExecutionTime;

    /** @var float */
    protected $usleepDelay;

    /** @var Process[] */
    protected $processList = [];

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->logger = new \Psr\Log\NullLogger;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('app:daemon:start')
            ->setDescription('Запускает демон, который запустит и будет следить за исполнением сконфигурированных фоновых процессов')
            ->addOption('max-execution-time', null, InputOption::VALUE_REQUIRED, 'Максимальное время выполнения команды в секундах', 360000)
            ->addOption('pid-file', null, InputOption::VALUE_REQUIRED, 'PID файл', __DIR__ . '/../../run/pid')
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Время задержки между внутренними циклами для экономии процессора', 100000)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->pidPath          = $input->getOption('pid-file');
        $this->maxExecutionTime = $input->getOption('max-execution-time');
        $this->usleepDelay      = (float) $input->getOption('sleep');


        register_shutdown_function([$this, 'shutdownFunction']);

        $this->executionTime    = 0;
        $startTime              = time();

        $this->pid = getmypid();

        // Проверим что процесс ещё не запущен
        if ($this->isPidFileExists($output)) {
            $this->logger->debug("Daemon process already running, exit now");
            return 0;
        }

        // Пробуем создать pid-файл
        if ( ! $this->createPidFile($output)) {
            $this->logger->debug("Creating PID file failed, exit now");
            return 0;
        }

        $this->logger->notice("Daemon started", [
            'pid_path' => $this->pidPath,
            'max_execution_time' => $this->maxExecutionTime,
            'pause_timeout' => $this->usleepDelay
        ]);

        // Зарегаем обработку сигналов
        pcntl_signal(SIGTERM, array($this, "signalHandler"));
        pcntl_signal(SIGINT, array($this, "signalHandler"));

        $this->logger->debug("Backgroud process list creating");

        $this->processList = $this->createProcess();
        $this->logger->debug("Going to start backgroud processes", ['count' => count($this->processList)]);

        // Запустим все фоновые процессы асинхронно
        foreach ($this->processList as $key => $item) {
            /* @var $p Process */
            $p = $item['process'];
            $p->start();
            $this->logger->info("start $key command");

            // Запустим команды с задержкой
            // чтобы не все сразу навалились на проц
            usleep($this->usleepDelay);
        }

        // Запустим процесс актуализации
        $actualizeCommand = sprintf(
            '/usr/bin/php  %s/../bin/console app:wiki:actualize',
            __DIR__
        );
        (new Process([$actualizeCommand]))->start();

        $this->logger->debug("Backgroud processes started");

        // Контролируем выполнение дочерних процессов
        while (file_exists($this->pidPath)) {

            // Проверим состояние фоновых процессов,
            // при необходимости перезапустим

            foreach ($this->processList as $key => &$item) {
                /* @var $p Process */
                $p = $item['process'];
                $this->checkProcessOutput($p);
                if (!$p->isRunning()) {
                    if (!isset($item['start'])) {
                        $this->logger->critical("Background process $key failed!", [
                            'command' => $p->getCommandLine(),
                            'exit_code' => $p->getExitCode(),
                            'output' => $p->getOutput(),
                            'err_output' => $p->getErrorOutput()
                        ]);

                        $item['start'] = time() + $item['interval'];
                    }

                    if ($item['start'] < time()) {
                        $p->start();
                        unset($item['start']);
                    }
                }

                // Запустим команды с задержкой
                // чтобы не все сразу навалились на проц
                usleep($this->usleepDelay);
            }

            $this->executionTime = time() - $startTime;
            usleep($this->usleepDelay);
        }

        return 0;
    }

    protected function createProcess()
    {
        $processes = [];

        $serverCommand = [
            '/usr/bin/php',
            '-S',
            '0.0.0.0:8000',
            '-t',
            '/var/www/app/public/',
            '/var/www/app/public/router.php',
        ];

        $processes['server'] = [
            'process'   => new Process($serverCommand, '/var/www/app/public'),
            'interval'  => 0
        ];

        $meilisearchCommand = sprintf(
            './meilisearch'
            //'--http-addr',
            //'0.0.0.0:7700'
        );

        $processes['meilisearch'] = [
            'process'   =>  new Process([$meilisearchCommand]),
            'interval'  => 0
        ];

        $wikiActualize = [
            '/usr/bin/php',
            '/var/www/app/bin/console',
            'app:wiki:actualize',
        ];

        $processes['wiki'] = [
            'process'   => new Process($wikiActualize, '/var/www/app'),
            'interval'  => 60
        ];

        return $processes;
    }

    public function shutdownFunction() {

        $error = error_get_last();
        if (is_array($error)) {
            $this->logger->error("Error occured", $error);
        }

        if ($this->executionTime > 0) {
            $this->shutdown();
        }
    }

    protected function shutdown()
    {
        $this->logger->notice("Daemon going to stop", ['exec_time' => $this->executionTime, 'pid_file_exist' => file_exists($this->pidPath)]);

        $this->logger->debug("Going to stop background processes", [
            'count' => count($this->processList)
        ]);

        // Грохнем все процессы
        foreach ($this->processList as  $item) {
            /* @var $p Process */
            $p = $item['process'];

            $p->stop();
        }

        $this->logger->debug("All background processes should be dead");

        // Удалим pid-file
        if (is_writable($this->pidPath)) {
            $unlinkResult = unlink($this->pidPath);
            $this->logger->debug(__FUNCTION__, [
                'line' => __LINE__,
                'unlinkResult' => $unlinkResult
            ]);
        } else {
            $this->logger->debug(__FUNCTION__, [
                'line' => __LINE__,
                'msg' => "PID file isn't writable"
            ]);
        }
    }
    protected function checkProcessOutput(Process $p)
    {
        // получать вывод можно только у процессов, которые были запущены
        if (!$p->isStarted()) {
            return;
        }

        $o = $p->getIncrementalOutput();
        $e = $p->getIncrementalErrorOutput();
        if ($o || $e) {
            $p->clearOutput();
            $p->clearErrorOutput();
            $this->logger->warning("Process output", [
                'pid' => $p->getPid(),
                'command' => $p->getCommandLine(),
                'output' => $o,
                'err_output' => $e,
            ]);
        }
    }

    protected function isPidFileExists(OutputInterface $output)
    {
        $this->logger->debug(__FUNCTION__, [
            'line' => __LINE__,
            'pidPath' => $this->pidPath,
        ]);

        $pidFileResetFunction = function($pidPath) {
            if ( ! unlink($pidPath)) {
                $this->logger->error(
                    'Cannot unlink PID file. Please remove it by hand and check permissions.',
                    [
                        'file' => $pidPath
                    ]
                );
                return true;
            } else {

                $this->logger->debug(__FUNCTION__, [
                    'line' => __LINE__,
                    'msg' => 'Old PID file was unlinked'
                ]);

                return false;
            }
        };

        if (is_readable($this->pidPath)) {

            $pid = (int) file_get_contents($this->pidPath);

            $this->logger->debug(__FUNCTION__, [
                'line' => __LINE__,
                'pid' => $pid
            ]);

            if (file_exists("/proc/$pid/cmdline")) {
                $myCmdLine      = file_get_contents("/proc/{$this->pid}/cmdline");
                $runningCmdLine = file_get_contents("/proc/$pid/cmdline");

                if ($myCmdLine == $runningCmdLine) {

                    $this->logger->debug(__FUNCTION__, [
                        'line' => __LINE__,
                        'message' => $myCmdLine . ' === ' . $runningCmdLine
                    ]);
                    return true;

                } else {

                    $this->logger->debug(__FUNCTION__, [
                        'line' => __LINE__,
                        'msg' => 'Another command running with recorded PID',
                        'myCmdLine' => $myCmdLine,
                        'runningCmdLine' => $runningCmdLine
                    ]);
                    return $pidFileResetFunction($this->pidPath, $this->logger);

                }
            } else {
                // Процесс не запущен, но файл не удален
                // Грохнем файл процесса

                $this->logger->debug(__FUNCTION__, [
                    'line' => __LINE__,
                    'msg' => 'No running process with recorded PID'
                ]);
                return $pidFileResetFunction($this->pidPath, $this->logger);
            }

        } else {

            $this->logger->debug(__FUNCTION__, [
                'line' => __LINE__,
                'is_readable' => false,
                'file_exists' => file_exists($this->pidPath)
            ]);

        }

        return false;
    }

    protected function createPidFile(OutputInterface $output)
    {
        $this->logger->debug(__FUNCTION__);

        $myPid = sprintf('%d', $this->pid);

        $this->logger->debug(__FUNCTION__, [
            'line' => __LINE__,
            'myPid' => $myPid
        ]);

        if ($file = fopen($this->pidPath, 'w')) {
            $writeResult = fwrite($file, $myPid, strlen($myPid));
            $closeResult = fclose($file);

            $this->logger->debug(__FUNCTION__, [
                'line' => __LINE__,
                'writeResult' => $writeResult,
                'closeResult' => $closeResult
            ]);
        } else {
            $this->logger->error(
                'Unable to create PID file',
                [
                    'file' => $this->pidPath
                ]
            );
            return false;
        }

        $chmodResult = chmod($this->pidPath, 0664);

        $this->logger->debug(__FUNCTION__, [
            'line' => __LINE__,
            'chmodResult' => $chmodResult
        ]);

        return true;
    }

    public function signalHandler($signo, $pid = null, $status = null)
    {
        switch ($signo) {

            case SIGTERM:
            case SIGINT:
            case SIGKILL:
                if (file_exists($this->pidPath)) {
                    unlink($this->pidPath);
                }
                break;

            default:
                break;
        }
    }

}
