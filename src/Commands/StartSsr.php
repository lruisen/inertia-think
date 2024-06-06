<?php


namespace Inertia\Commands;

use Inertia\Ssr\BundleDetector;
use Inertia\Ssr\SsrException;
use Symfony\Component\Process\Process;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\facade\Console;

class StartSsr extends Command
{
    protected function configure(): void
    {
        $this->setName('inertia:start-ssr')
            ->setDescription('Start the Inertia SSR server')
            ->addOption('runtime', null, Option::VALUE_OPTIONAL, 'The runtime to use (`node` or `bun`)');
    }

    /**
     * @throws SsrException
     */
    protected function execute(Input $input, Output $output): bool
    {
        if (!config('inertia.ssr.enabled', true)) {
            $output->writeln('<error>Inertia SSR is not enabled. Enable it via the `inertia.ssr.enabled` config option.</error>');
            return false;
        }

        $bundle = (new BundleDetector())->detect();
        $configuredBundle = config('inertia.ssr.bundle');

        if ($bundle === null) {
            $output->writeln('<error>' . ($configuredBundle
                    ? 'Inertia SSR bundle not found at the configured path: "' . $configuredBundle . '"'
                    : 'Inertia SSR bundle not found. Set the correct Inertia SSR bundle path in your `inertia.ssr.bundle` config.') . '</error>');
            return false;
        } elseif ($configuredBundle && $bundle !== $configuredBundle) {
            $output->writeln('<comment>Inertia SSR bundle not found at the configured path: "' . $configuredBundle . '"</comment>');
            $output->writeln('<comment>Using a default bundle instead: "' . $bundle . '"</comment>');
        }

        $runtime = $input->getOption('runtime');
        if (!in_array($runtime, ['node', 'bun'])) {
            $output->writeln('<error>Unsupported runtime: "' . $runtime . '". Supported runtimes are `node` and `bun`.</error>');
            return false;
        }

        // 调用另一个命令 `stop-ssr`
        $this->callSilent();

        // 启动新的SSR进程
        $process = new Process([$runtime, $bundle]);
        $process->setTimeout(null);
        $process->start();

        if (extension_loaded('pcntl')) {
            $stop = function () use ($process) {
                $process->stop();
            };
            pcntl_async_signals(true);
            pcntl_signal(SIGINT, $stop);
            pcntl_signal(SIGQUIT, $stop);
            pcntl_signal(SIGTERM, $stop);
        }

        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                $output->writeln('<info>' . trim($data) . '</info>');
            } else {
                $output->writeln('<error>' . trim($data) . '</error>');
                throw new SsrException($data);
            }
        }

        return true;
    }

    private function callSilent(): void
    {
        $output = Console::call('inertia:stop-ssr');
        $output->fetch();
    }
}
