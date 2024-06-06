<?php


namespace Inertia\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class StopSsr extends Command
{
    protected function configure(): void
    {
        $this->setName('inertia:stop-ssr')
            ->setDescription('Stop the Inertia SSR server');
    }

    /**
     */
    protected function execute(Input $input, Output $output): bool
    {
        $url = str_replace('/render', '', config('inertia.ssr.url', 'http://127.0.0.1:13714')) . '/shutdown';

        $ch = curl_init($url);
        curl_exec($ch);

        if (curl_error($ch) !== 'Empty reply from server') {
            $output->error('Unable to connect to Inertia SSR server.');

            return false;
        }

        $output->error('Inertia SSR server stopped.');

        curl_close($ch);

        return false;
    }
}
