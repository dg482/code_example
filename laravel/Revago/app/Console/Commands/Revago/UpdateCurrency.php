<?php namespace App\Console\Commands;

use App\Core\Currency;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Artisaninweb\SoapWrapper\Facades\SoapWrapper;

class UpdateCurrency extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'updateCurrency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ежедневное обновление курсов валют cbr.ru';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        SoapWrapper::add(function ($service) {
            $service->name('currency')
                ->wsdl('http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL')
                ->trace(true)
                ->cache(WSDL_CACHE_NONE);
        });
        $data = (object)[
            'On_date' => date('Y-m-d')
        ];
        SoapWrapper::service('currency', function ($service) use ($data) {
            $result = $service->call('GetCursOnDate', [$data]);
            $xml = simplexml_load_string($result->GetCursOnDateResult->any);
            foreach ($xml->children() as $item) {
                foreach ($item as $__item) {
                    if ((string)$__item->VchCode == 'EUR') {
                        Currency::where('code', 'RUB')->update([
                            'value' => (float)$__item->Vcurs
                        ]);
                        $this->msg('Курс валют обновлен в ' . date('Y-m-d H:i:s') . ' (' . $__item->Vcurs . ')');
                    }
                }
            }
        });
        /*
                $currency = [];
                $all = \DB::table('currency')
                    ->select('*')
                    ->get();
                foreach ($all as $item) {
                    $currency[$item->code] = [
                        'value' => $item->value,
                        'right' => $item->symbol_right,
                    ];
                }
                \Cache::put('currency', $currency, (60 * 60 * 24));
        */
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            //['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            //['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }

    /**
     * Вывод сообщения в консоль
     *
     * @param $msg
     */
    private function msg($msg)
    {
        echo $msg . PHP_EOL;
    }
}
