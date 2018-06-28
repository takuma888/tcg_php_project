<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/28
 * Time: 下午1:10
 */


namespace Offers\Command\Offer;

use Offers\Service\OfferImportService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('offers:offer.import')
            ->setDescription('导入第三方的offer数据')
            ->addOption('source', null, InputOption::VALUE_OPTIONAL, '来源', 0);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getOption('source');
        if (!$source) {
            return;
        }
        /** @var OfferImportService $offerImportService */
        $offerImportService = service(OfferImportService::class);
        $offerImportService->import($source);
    }
}