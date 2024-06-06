<?php


namespace Inertia\Commands;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\App;

class CreateMiddleware extends Command
{
    /**
     * 配置命令的基本信息
     */
    protected function configure(): void
    {
        $this->setName('inertia:middleware')
            ->setDescription('Create a new Inertia middleware')
            ->addArgument('name', Argument::OPTIONAL, 'Name of the Middleware that should be created', 'HandleInertiaRequests')
            ->addOption('force', null, Option::VALUE_OPTIONAL, 'Create the class even if the Middleware already exists');
    }

    /**
     * 执行命令
     */
    protected function execute(Input $input, Output $output): bool
    {
        $name = $input->getArgument('name');
        $force = $input->getOption('force');

        $namespace = $this->getDefaultNamespace(App::getNamespace());
        $path = $this->getPath($namespace, $name);

        if (file_exists($path) && !$force) {
            $output->writeln("<error>Middleware {$name} already exists!</error>");
            return false;
        }

        $this->makeDirectory(dirname($path));

        $stub = file_get_contents(__DIR__ . '/../../stubs/middleware.stub');
        $stub = str_replace(['{{ namespace }}', '{{ class }}'], [$namespace, $name], $stub);

        file_put_contents($path, $stub);

        $output->writeln("<info>Middleware {$name} created successfully.</info>");
        return true;
    }

    /**
     * 获取默认的命名空间
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\http\middleware';
    }

    /**
     * 获取目标文件路径
     */
    protected function getPath($namespace, $name): string
    {
        return App::getRootPath() . str_replace('\\', '/', $namespace) . '/' . $name . '.php';
    }

    /**
     * 创建目录
     */
    protected function makeDirectory($path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
