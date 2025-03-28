<?php

namespace Pelmered\FilamentMoneyField\Currencies\Providers;

use PhpStaticAnalysis\Attributes\Type;
use RuntimeException;

class CryptoCurrenciesProvider implements CurrenciesProvider
{
    #[Type('array<string, array{alphabeticCode: string, currency: string, minorUnit: int, numericCode: int}>')]
    public function loadCurrencies(): array
    {
        return $this->getCurrencyData();
    }

    private function getCurrencyData(): array
    {
        /**
         * @see https://github.com/moneyphp/money/blob/master/resources/binance.php
         * Updated: 2025-03-28
         */
        return [
            '1000SATS' =>
                [
                    'symbol'    => '1000SATS',
                    'minorUnit' => 8,
                ],
            '1INCH'    =>
                [
                    'symbol'    => '1INCH',
                    'minorUnit' => 8,
                ],
            'AAVE'     =>
                [
                    'symbol'    => 'AAVE',
                    'minorUnit' => 8,
                ],
            'ACA'      =>
                [
                    'symbol'    => 'ACA',
                    'minorUnit' => 8,
                ],
            'ACE'      =>
                [
                    'symbol'    => 'ACE',
                    'minorUnit' => 8,
                ],
            'ACH'      =>
                [
                    'symbol'    => 'ACH',
                    'minorUnit' => 8,
                ],
            'ACM'      =>
                [
                    'symbol'    => 'ACM',
                    'minorUnit' => 8,
                ],
            'ADA'      =>
                [
                    'symbol'    => 'ADA',
                    'minorUnit' => 8,
                ],
            'ADX'      =>
                [
                    'symbol'    => 'ADX',
                    'minorUnit' => 8,
                ],
            'AERGO'    =>
                [
                    'symbol'    => 'AERGO',
                    'minorUnit' => 8,
                ],
            'AEUR'     =>
                [
                    'symbol'    => 'AEUR',
                    'minorUnit' => 8,
                ],
            'AGIX'     =>
                [
                    'symbol'    => 'AGIX',
                    'minorUnit' => 8,
                ],
            'AGLD'     =>
                [
                    'symbol'    => 'AGLD',
                    'minorUnit' => 8,
                ],
            'AI'       =>
                [
                    'symbol'    => 'AI',
                    'minorUnit' => 8,
                ],
            'AKRO'     =>
                [
                    'symbol'    => 'AKRO',
                    'minorUnit' => 8,
                ],
            'ALCX'     =>
                [
                    'symbol'    => 'ALCX',
                    'minorUnit' => 8,
                ],
            'ALGO'     =>
                [
                    'symbol'    => 'ALGO',
                    'minorUnit' => 8,
                ],
            'ALICE'    =>
                [
                    'symbol'    => 'ALICE',
                    'minorUnit' => 8,
                ],
            'ALPACA'   =>
                [
                    'symbol'    => 'ALPACA',
                    'minorUnit' => 8,
                ],
            'ALPHA'    =>
                [
                    'symbol'    => 'ALPHA',
                    'minorUnit' => 8,
                ],
            'ALPINE'   =>
                [
                    'symbol'    => 'ALPINE',
                    'minorUnit' => 8,
                ],
            'ALT'      =>
                [
                    'symbol'    => 'ALT',
                    'minorUnit' => 8,
                ],
            'AMB'      =>
                [
                    'symbol'    => 'AMB',
                    'minorUnit' => 8,
                ],
            'AMP'      =>
                [
                    'symbol'    => 'AMP',
                    'minorUnit' => 8,
                ],
            'ANKR'     =>
                [
                    'symbol'    => 'ANKR',
                    'minorUnit' => 8,
                ],
            'ANT'      =>
                [
                    'symbol'    => 'ANT',
                    'minorUnit' => 8,
                ],
            'APE'      =>
                [
                    'symbol'    => 'APE',
                    'minorUnit' => 8,
                ],
            'API3'     =>
                [
                    'symbol'    => 'API3',
                    'minorUnit' => 8,
                ],
            'APT'      =>
                [
                    'symbol'    => 'APT',
                    'minorUnit' => 8,
                ],
            'AR'       =>
                [
                    'symbol'    => 'AR',
                    'minorUnit' => 8,
                ],
            'ARB'      =>
                [
                    'symbol'    => 'ARB',
                    'minorUnit' => 8,
                ],
            'ARDR'     =>
                [
                    'symbol'    => 'ARDR',
                    'minorUnit' => 8,
                ],
            'ARK'      =>
                [
                    'symbol'    => 'ARK',
                    'minorUnit' => 8,
                ],
            'ARKM'     =>
                [
                    'symbol'    => 'ARKM',
                    'minorUnit' => 8,
                ],
            'ARPA'     =>
                [
                    'symbol'    => 'ARPA',
                    'minorUnit' => 8,
                ],
            'ARS'      =>
                [
                    'symbol'    => 'ARS',
                    'minorUnit' => 8,
                ],
            'ASR'      =>
                [
                    'symbol'    => 'ASR',
                    'minorUnit' => 8,
                ],
            'AST'      =>
                [
                    'symbol'    => 'AST',
                    'minorUnit' => 8,
                ],
            'ASTR'     =>
                [
                    'symbol'    => 'ASTR',
                    'minorUnit' => 8,
                ],
            'ATA'      =>
                [
                    'symbol'    => 'ATA',
                    'minorUnit' => 8,
                ],
            'ATM'      =>
                [
                    'symbol'    => 'ATM',
                    'minorUnit' => 8,
                ],
            'ATOM'     =>
                [
                    'symbol'    => 'ATOM',
                    'minorUnit' => 8,
                ],
            'AUCTION'  =>
                [
                    'symbol'    => 'AUCTION',
                    'minorUnit' => 8,
                ],
            'AUDIO'    =>
                [
                    'symbol'    => 'AUDIO',
                    'minorUnit' => 8,
                ],
            'AVA'      =>
                [
                    'symbol'    => 'AVA',
                    'minorUnit' => 8,
                ],
            'AVAX'     =>
                [
                    'symbol'    => 'AVAX',
                    'minorUnit' => 8,
                ],
            'AXS'      =>
                [
                    'symbol'    => 'AXS',
                    'minorUnit' => 8,
                ],
            'BADGER'   =>
                [
                    'symbol'    => 'BADGER',
                    'minorUnit' => 8,
                ],
            'BAKE'     =>
                [
                    'symbol'    => 'BAKE',
                    'minorUnit' => 8,
                ],
            'BAL'      =>
                [
                    'symbol'    => 'BAL',
                    'minorUnit' => 8,
                ],
            'BAND'     =>
                [
                    'symbol'    => 'BAND',
                    'minorUnit' => 8,
                ],
            'BAR'      =>
                [
                    'symbol'    => 'BAR',
                    'minorUnit' => 8,
                ],
            'BAT'      =>
                [
                    'symbol'    => 'BAT',
                    'minorUnit' => 8,
                ],
            'BCH'      =>
                [
                    'symbol'    => 'BCH',
                    'minorUnit' => 8,
                ],
            'BEAMX'    =>
                [
                    'symbol'    => 'BEAMX',
                    'minorUnit' => 8,
                ],
            'BEL'      =>
                [
                    'symbol'    => 'BEL',
                    'minorUnit' => 8,
                ],
            'BETA'     =>
                [
                    'symbol'    => 'BETA',
                    'minorUnit' => 8,
                ],
            'BICO'     =>
                [
                    'symbol'    => 'BICO',
                    'minorUnit' => 8,
                ],
            'BIDR'     =>
                [
                    'symbol'    => 'BIDR',
                    'minorUnit' => 8,
                ],
            'BIFI'     =>
                [
                    'symbol'    => 'BIFI',
                    'minorUnit' => 8,
                ],
            'BLUR'     =>
                [
                    'symbol'    => 'BLUR',
                    'minorUnit' => 8,
                ],
            'BLZ'      =>
                [
                    'symbol'    => 'BLZ',
                    'minorUnit' => 8,
                ],
            'BNB'      =>
                [
                    'symbol'    => 'BNB',
                    'minorUnit' => 8,
                ],
            'BNT'      =>
                [
                    'symbol'    => 'BNT',
                    'minorUnit' => 8,
                ],
            'BNX'      =>
                [
                    'symbol'    => 'BNX',
                    'minorUnit' => 8,
                ],
            'BOND'     =>
                [
                    'symbol'    => 'BOND',
                    'minorUnit' => 8,
                ],
            'BONK'     =>
                [
                    'symbol'    => 'BONK',
                    'minorUnit' => 8,
                ],
            'BSW'      =>
                [
                    'symbol'    => 'BSW',
                    'minorUnit' => 8,
                ],
            'BTC'      =>
                [
                    'symbol'    => 'BTC',
                    'minorUnit' => 8,
                ],
            'BTTC'     =>
                [
                    'symbol'    => 'BTTC',
                    'minorUnit' => 8,
                ],
            'BURGER'   =>
                [
                    'symbol'    => 'BURGER',
                    'minorUnit' => 8,
                ],
            'C98'      =>
                [
                    'symbol'    => 'C98',
                    'minorUnit' => 8,
                ],
            'CAKE'     =>
                [
                    'symbol'    => 'CAKE',
                    'minorUnit' => 8,
                ],
            'CELO'     =>
                [
                    'symbol'    => 'CELO',
                    'minorUnit' => 8,
                ],
            'CELR'     =>
                [
                    'symbol'    => 'CELR',
                    'minorUnit' => 8,
                ],
            'CFX'      =>
                [
                    'symbol'    => 'CFX',
                    'minorUnit' => 8,
                ],
            'CHESS'    =>
                [
                    'symbol'    => 'CHESS',
                    'minorUnit' => 8,
                ],
            'CHR'      =>
                [
                    'symbol'    => 'CHR',
                    'minorUnit' => 8,
                ],
            'CHZ'      =>
                [
                    'symbol'    => 'CHZ',
                    'minorUnit' => 8,
                ],
            'CITY'     =>
                [
                    'symbol'    => 'CITY',
                    'minorUnit' => 8,
                ],
            'CKB'      =>
                [
                    'symbol'    => 'CKB',
                    'minorUnit' => 8,
                ],
            'CLV'      =>
                [
                    'symbol'    => 'CLV',
                    'minorUnit' => 8,
                ],
            'COMBO'    =>
                [
                    'symbol'    => 'COMBO',
                    'minorUnit' => 8,
                ],
            'COMP'     =>
                [
                    'symbol'    => 'COMP',
                    'minorUnit' => 8,
                ],
            'COS'      =>
                [
                    'symbol'    => 'COS',
                    'minorUnit' => 8,
                ],
            'COTI'     =>
                [
                    'symbol'    => 'COTI',
                    'minorUnit' => 8,
                ],
            'CREAM'    =>
                [
                    'symbol'    => 'CREAM',
                    'minorUnit' => 8,
                ],
            'CRV'      =>
                [
                    'symbol'    => 'CRV',
                    'minorUnit' => 8,
                ],
            'CTK'      =>
                [
                    'symbol'    => 'CTK',
                    'minorUnit' => 8,
                ],
            'CTSI'     =>
                [
                    'symbol'    => 'CTSI',
                    'minorUnit' => 8,
                ],
            'CTXC'     =>
                [
                    'symbol'    => 'CTXC',
                    'minorUnit' => 8,
                ],
            'CVC'      =>
                [
                    'symbol'    => 'CVC',
                    'minorUnit' => 8,
                ],
            'CVP'      =>
                [
                    'symbol'    => 'CVP',
                    'minorUnit' => 8,
                ],
            'CVX'      =>
                [
                    'symbol'    => 'CVX',
                    'minorUnit' => 8,
                ],
            'CYBER'    =>
                [
                    'symbol'    => 'CYBER',
                    'minorUnit' => 8,
                ],
            'DAI'      =>
                [
                    'symbol'    => 'DAI',
                    'minorUnit' => 8,
                ],
            'DAR'      =>
                [
                    'symbol'    => 'DAR',
                    'minorUnit' => 8,
                ],
            'DASH'     =>
                [
                    'symbol'    => 'DASH',
                    'minorUnit' => 8,
                ],
            'DATA'     =>
                [
                    'symbol'    => 'DATA',
                    'minorUnit' => 8,
                ],
            'DCR'      =>
                [
                    'symbol'    => 'DCR',
                    'minorUnit' => 8,
                ],
            'DEGO'     =>
                [
                    'symbol'    => 'DEGO',
                    'minorUnit' => 8,
                ],
            'DENT'     =>
                [
                    'symbol'    => 'DENT',
                    'minorUnit' => 8,
                ],
            'DEXE'     =>
                [
                    'symbol'    => 'DEXE',
                    'minorUnit' => 8,
                ],
            'DF'       =>
                [
                    'symbol'    => 'DF',
                    'minorUnit' => 8,
                ],
            'DGB'      =>
                [
                    'symbol'    => 'DGB',
                    'minorUnit' => 8,
                ],
            'DIA'      =>
                [
                    'symbol'    => 'DIA',
                    'minorUnit' => 8,
                ],
            'DOCK'     =>
                [
                    'symbol'    => 'DOCK',
                    'minorUnit' => 8,
                ],
            'DODO'     =>
                [
                    'symbol'    => 'DODO',
                    'minorUnit' => 8,
                ],
            'DOGE'     =>
                [
                    'symbol'    => 'DOGE',
                    'minorUnit' => 8,
                ],
            'DOT'      =>
                [
                    'symbol'    => 'DOT',
                    'minorUnit' => 8,
                ],
            'DREP'     =>
                [
                    'symbol'    => 'DREP',
                    'minorUnit' => 8,
                ],
            'DUSK'     =>
                [
                    'symbol'    => 'DUSK',
                    'minorUnit' => 8,
                ],
            'DYDX'     =>
                [
                    'symbol'    => 'DYDX',
                    'minorUnit' => 8,
                ],
            'EDU'      =>
                [
                    'symbol'    => 'EDU',
                    'minorUnit' => 8,
                ],
            'EGLD'     =>
                [
                    'symbol'    => 'EGLD',
                    'minorUnit' => 8,
                ],
            'ELF'      =>
                [
                    'symbol'    => 'ELF',
                    'minorUnit' => 8,
                ],
            'ENJ'      =>
                [
                    'symbol'    => 'ENJ',
                    'minorUnit' => 8,
                ],
            'ENS'      =>
                [
                    'symbol'    => 'ENS',
                    'minorUnit' => 8,
                ],
            'EOS'      =>
                [
                    'symbol'    => 'EOS',
                    'minorUnit' => 8,
                ],
            'EPX'      =>
                [
                    'symbol'    => 'EPX',
                    'minorUnit' => 8,
                ],
            'ETC'      =>
                [
                    'symbol'    => 'ETC',
                    'minorUnit' => 8,
                ],
            'ETH'      =>
                [
                    'symbol'    => 'ETH',
                    'minorUnit' => 8,
                ],
            'FARM'     =>
                [
                    'symbol'    => 'FARM',
                    'minorUnit' => 8,
                ],
            'FDUSD'    =>
                [
                    'symbol'    => 'FDUSD',
                    'minorUnit' => 8,
                ],
            'FET'      =>
                [
                    'symbol'    => 'FET',
                    'minorUnit' => 8,
                ],
            'FIDA'     =>
                [
                    'symbol'    => 'FIDA',
                    'minorUnit' => 8,
                ],
            'FIL'      =>
                [
                    'symbol'    => 'FIL',
                    'minorUnit' => 8,
                ],
            'FIO'      =>
                [
                    'symbol'    => 'FIO',
                    'minorUnit' => 8,
                ],
            'FIRO'     =>
                [
                    'symbol'    => 'FIRO',
                    'minorUnit' => 8,
                ],
            'FIS'      =>
                [
                    'symbol'    => 'FIS',
                    'minorUnit' => 8,
                ],
            'FLM'      =>
                [
                    'symbol'    => 'FLM',
                    'minorUnit' => 8,
                ],
            'FLOKI'    =>
                [
                    'symbol'    => 'FLOKI',
                    'minorUnit' => 8,
                ],
            'FLOW'     =>
                [
                    'symbol'    => 'FLOW',
                    'minorUnit' => 8,
                ],
            'FLUX'     =>
                [
                    'symbol'    => 'FLUX',
                    'minorUnit' => 8,
                ],
            'FOR'      =>
                [
                    'symbol'    => 'FOR',
                    'minorUnit' => 8,
                ],
            'FORTH'    =>
                [
                    'symbol'    => 'FORTH',
                    'minorUnit' => 8,
                ],
            'FRONT'    =>
                [
                    'symbol'    => 'FRONT',
                    'minorUnit' => 8,
                ],
            'FTM'      =>
                [
                    'symbol'    => 'FTM',
                    'minorUnit' => 8,
                ],
            'FTT'      =>
                [
                    'symbol'    => 'FTT',
                    'minorUnit' => 8,
                ],
            'FUN'      =>
                [
                    'symbol'    => 'FUN',
                    'minorUnit' => 8,
                ],
            'FXS'      =>
                [
                    'symbol'    => 'FXS',
                    'minorUnit' => 8,
                ],
            'GAL'      =>
                [
                    'symbol'    => 'GAL',
                    'minorUnit' => 8,
                ],
            'GALA'     =>
                [
                    'symbol'    => 'GALA',
                    'minorUnit' => 8,
                ],
            'GAS'      =>
                [
                    'symbol'    => 'GAS',
                    'minorUnit' => 8,
                ],
            'GFT'      =>
                [
                    'symbol'    => 'GFT',
                    'minorUnit' => 8,
                ],
            'GHST'     =>
                [
                    'symbol'    => 'GHST',
                    'minorUnit' => 8,
                ],
            'GLM'      =>
                [
                    'symbol'    => 'GLM',
                    'minorUnit' => 8,
                ],
            'GLMR'     =>
                [
                    'symbol'    => 'GLMR',
                    'minorUnit' => 8,
                ],
            'GMT'      =>
                [
                    'symbol'    => 'GMT',
                    'minorUnit' => 8,
                ],
            'GMX'      =>
                [
                    'symbol'    => 'GMX',
                    'minorUnit' => 8,
                ],
            'GNO'      =>
                [
                    'symbol'    => 'GNO',
                    'minorUnit' => 8,
                ],
            'GNS'      =>
                [
                    'symbol'    => 'GNS',
                    'minorUnit' => 8,
                ],
            'GRT'      =>
                [
                    'symbol'    => 'GRT',
                    'minorUnit' => 8,
                ],
            'GTC'      =>
                [
                    'symbol'    => 'GTC',
                    'minorUnit' => 8,
                ],
            'HARD'     =>
                [
                    'symbol'    => 'HARD',
                    'minorUnit' => 8,
                ],
            'HBAR'     =>
                [
                    'symbol'    => 'HBAR',
                    'minorUnit' => 8,
                ],
            'HFT'      =>
                [
                    'symbol'    => 'HFT',
                    'minorUnit' => 8,
                ],
            'HIFI'     =>
                [
                    'symbol'    => 'HIFI',
                    'minorUnit' => 8,
                ],
            'HIGH'     =>
                [
                    'symbol'    => 'HIGH',
                    'minorUnit' => 8,
                ],
            'HIVE'     =>
                [
                    'symbol'    => 'HIVE',
                    'minorUnit' => 8,
                ],
            'HOOK'     =>
                [
                    'symbol'    => 'HOOK',
                    'minorUnit' => 8,
                ],
            'HOT'      =>
                [
                    'symbol'    => 'HOT',
                    'minorUnit' => 8,
                ],
            'ICP'      =>
                [
                    'symbol'    => 'ICP',
                    'minorUnit' => 8,
                ],
            'ICX'      =>
                [
                    'symbol'    => 'ICX',
                    'minorUnit' => 8,
                ],
            'ID'       =>
                [
                    'symbol'    => 'ID',
                    'minorUnit' => 8,
                ],
            'IDEX'     =>
                [
                    'symbol'    => 'IDEX',
                    'minorUnit' => 8,
                ],
            'IDRT'     =>
                [
                    'symbol'    => 'IDRT',
                    'minorUnit' => 8,
                ],
            'ILV'      =>
                [
                    'symbol'    => 'ILV',
                    'minorUnit' => 8,
                ],
            'IMX'      =>
                [
                    'symbol'    => 'IMX',
                    'minorUnit' => 8,
                ],
            'INJ'      =>
                [
                    'symbol'    => 'INJ',
                    'minorUnit' => 8,
                ],
            'IOST'     =>
                [
                    'symbol'    => 'IOST',
                    'minorUnit' => 8,
                ],
            'IOTA'     =>
                [
                    'symbol'    => 'IOTA',
                    'minorUnit' => 8,
                ],
            'IOTX'     =>
                [
                    'symbol'    => 'IOTX',
                    'minorUnit' => 8,
                ],
            'IQ'       =>
                [
                    'symbol'    => 'IQ',
                    'minorUnit' => 8,
                ],
            'IRIS'     =>
                [
                    'symbol'    => 'IRIS',
                    'minorUnit' => 8,
                ],
            'JASMY'    =>
                [
                    'symbol'    => 'JASMY',
                    'minorUnit' => 8,
                ],
            'JOE'      =>
                [
                    'symbol'    => 'JOE',
                    'minorUnit' => 8,
                ],
            'JST'      =>
                [
                    'symbol'    => 'JST',
                    'minorUnit' => 8,
                ],
            'JTO'      =>
                [
                    'symbol'    => 'JTO',
                    'minorUnit' => 8,
                ],
            'JUV'      =>
                [
                    'symbol'    => 'JUV',
                    'minorUnit' => 8,
                ],
            'KAVA'     =>
                [
                    'symbol'    => 'KAVA',
                    'minorUnit' => 8,
                ],
            'KDA'      =>
                [
                    'symbol'    => 'KDA',
                    'minorUnit' => 8,
                ],
            'KEY'      =>
                [
                    'symbol'    => 'KEY',
                    'minorUnit' => 8,
                ],
            'KLAY'     =>
                [
                    'symbol'    => 'KLAY',
                    'minorUnit' => 8,
                ],
            'KMD'      =>
                [
                    'symbol'    => 'KMD',
                    'minorUnit' => 8,
                ],
            'KNC'      =>
                [
                    'symbol'    => 'KNC',
                    'minorUnit' => 8,
                ],
            'KP3R'     =>
                [
                    'symbol'    => 'KP3R',
                    'minorUnit' => 8,
                ],
            'KSM'      =>
                [
                    'symbol'    => 'KSM',
                    'minorUnit' => 8,
                ],
            'LAZIO'    =>
                [
                    'symbol'    => 'LAZIO',
                    'minorUnit' => 8,
                ],
            'LDO'      =>
                [
                    'symbol'    => 'LDO',
                    'minorUnit' => 8,
                ],
            'LEVER'    =>
                [
                    'symbol'    => 'LEVER',
                    'minorUnit' => 8,
                ],
            'LINA'     =>
                [
                    'symbol'    => 'LINA',
                    'minorUnit' => 8,
                ],
            'LINK'     =>
                [
                    'symbol'    => 'LINK',
                    'minorUnit' => 8,
                ],
            'LIT'      =>
                [
                    'symbol'    => 'LIT',
                    'minorUnit' => 8,
                ],
            'LOKA'     =>
                [
                    'symbol'    => 'LOKA',
                    'minorUnit' => 8,
                ],
            'LOOM'     =>
                [
                    'symbol'    => 'LOOM',
                    'minorUnit' => 8,
                ],
            'LPT'      =>
                [
                    'symbol'    => 'LPT',
                    'minorUnit' => 8,
                ],
            'LQTY'     =>
                [
                    'symbol'    => 'LQTY',
                    'minorUnit' => 8,
                ],
            'LRC'      =>
                [
                    'symbol'    => 'LRC',
                    'minorUnit' => 8,
                ],
            'LSK'      =>
                [
                    'symbol'    => 'LSK',
                    'minorUnit' => 8,
                ],
            'LTC'      =>
                [
                    'symbol'    => 'LTC',
                    'minorUnit' => 8,
                ],
            'LTO'      =>
                [
                    'symbol'    => 'LTO',
                    'minorUnit' => 8,
                ],
            'LUNA'     =>
                [
                    'symbol'    => 'LUNA',
                    'minorUnit' => 8,
                ],
            'LUNC'     =>
                [
                    'symbol'    => 'LUNC',
                    'minorUnit' => 8,
                ],
            'MAGIC'    =>
                [
                    'symbol'    => 'MAGIC',
                    'minorUnit' => 8,
                ],
            'MANA'     =>
                [
                    'symbol'    => 'MANA',
                    'minorUnit' => 8,
                ],
            'MANTA'    =>
                [
                    'symbol'    => 'MANTA',
                    'minorUnit' => 8,
                ],
            'MASK'     =>
                [
                    'symbol'    => 'MASK',
                    'minorUnit' => 8,
                ],
            'MATIC'    =>
                [
                    'symbol'    => 'MATIC',
                    'minorUnit' => 8,
                ],
            'MAV'      =>
                [
                    'symbol'    => 'MAV',
                    'minorUnit' => 8,
                ],
            'MBL'      =>
                [
                    'symbol'    => 'MBL',
                    'minorUnit' => 8,
                ],
            'MBOX'     =>
                [
                    'symbol'    => 'MBOX',
                    'minorUnit' => 8,
                ],
            'MDT'      =>
                [
                    'symbol'    => 'MDT',
                    'minorUnit' => 8,
                ],
            'MDX'      =>
                [
                    'symbol'    => 'MDX',
                    'minorUnit' => 8,
                ],
            'MEME'     =>
                [
                    'symbol'    => 'MEME',
                    'minorUnit' => 8,
                ],
            'MINA'     =>
                [
                    'symbol'    => 'MINA',
                    'minorUnit' => 8,
                ],
            'MKR'      =>
                [
                    'symbol'    => 'MKR',
                    'minorUnit' => 8,
                ],
            'MLN'      =>
                [
                    'symbol'    => 'MLN',
                    'minorUnit' => 8,
                ],
            'MOB'      =>
                [
                    'symbol'    => 'MOB',
                    'minorUnit' => 8,
                ],
            'MOVR'     =>
                [
                    'symbol'    => 'MOVR',
                    'minorUnit' => 8,
                ],
            'MTL'      =>
                [
                    'symbol'    => 'MTL',
                    'minorUnit' => 8,
                ],
            'MULTI'    =>
                [
                    'symbol'    => 'MULTI',
                    'minorUnit' => 8,
                ],
            'NEAR'     =>
                [
                    'symbol'    => 'NEAR',
                    'minorUnit' => 8,
                ],
            'NEO'      =>
                [
                    'symbol'    => 'NEO',
                    'minorUnit' => 8,
                ],
            'NEXO'     =>
                [
                    'symbol'    => 'NEXO',
                    'minorUnit' => 8,
                ],
            'NFP'      =>
                [
                    'symbol'    => 'NFP',
                    'minorUnit' => 8,
                ],
            'NKN'      =>
                [
                    'symbol'    => 'NKN',
                    'minorUnit' => 8,
                ],
            'NMR'      =>
                [
                    'symbol'    => 'NMR',
                    'minorUnit' => 8,
                ],
            'NTRN'     =>
                [
                    'symbol'    => 'NTRN',
                    'minorUnit' => 8,
                ],
            'NULS'     =>
                [
                    'symbol'    => 'NULS',
                    'minorUnit' => 8,
                ],
            'OAX'      =>
                [
                    'symbol'    => 'OAX',
                    'minorUnit' => 8,
                ],
            'OCEAN'    =>
                [
                    'symbol'    => 'OCEAN',
                    'minorUnit' => 8,
                ],
            'OG'       =>
                [
                    'symbol'    => 'OG',
                    'minorUnit' => 8,
                ],
            'OGN'      =>
                [
                    'symbol'    => 'OGN',
                    'minorUnit' => 8,
                ],
            'OM'       =>
                [
                    'symbol'    => 'OM',
                    'minorUnit' => 8,
                ],
            'OMG'      =>
                [
                    'symbol'    => 'OMG',
                    'minorUnit' => 8,
                ],
            'ONE'      =>
                [
                    'symbol'    => 'ONE',
                    'minorUnit' => 8,
                ],
            'ONG'      =>
                [
                    'symbol'    => 'ONG',
                    'minorUnit' => 8,
                ],
            'ONT'      =>
                [
                    'symbol'    => 'ONT',
                    'minorUnit' => 8,
                ],
            'OOKI'     =>
                [
                    'symbol'    => 'OOKI',
                    'minorUnit' => 8,
                ],
            'OP'       =>
                [
                    'symbol'    => 'OP',
                    'minorUnit' => 8,
                ],
            'ORDI'     =>
                [
                    'symbol'    => 'ORDI',
                    'minorUnit' => 8,
                ],
            'ORN'      =>
                [
                    'symbol'    => 'ORN',
                    'minorUnit' => 8,
                ],
            'OSMO'     =>
                [
                    'symbol'    => 'OSMO',
                    'minorUnit' => 8,
                ],
            'OXT'      =>
                [
                    'symbol'    => 'OXT',
                    'minorUnit' => 8,
                ],
            'PAXG'     =>
                [
                    'symbol'    => 'PAXG',
                    'minorUnit' => 8,
                ],
            'PENDLE'   =>
                [
                    'symbol'    => 'PENDLE',
                    'minorUnit' => 8,
                ],
            'PEOPLE'   =>
                [
                    'symbol'    => 'PEOPLE',
                    'minorUnit' => 8,
                ],
            'PEPE'     =>
                [
                    'symbol'    => 'PEPE',
                    'minorUnit' => 8,
                ],
            'PERP'     =>
                [
                    'symbol'    => 'PERP',
                    'minorUnit' => 8,
                ],
            'PHA'      =>
                [
                    'symbol'    => 'PHA',
                    'minorUnit' => 8,
                ],
            'PHB'      =>
                [
                    'symbol'    => 'PHB',
                    'minorUnit' => 8,
                ],
            'PIVX'     =>
                [
                    'symbol'    => 'PIVX',
                    'minorUnit' => 8,
                ],
            'PLA'      =>
                [
                    'symbol'    => 'PLA',
                    'minorUnit' => 8,
                ],
            'PNT'      =>
                [
                    'symbol'    => 'PNT',
                    'minorUnit' => 8,
                ],
            'POLS'     =>
                [
                    'symbol'    => 'POLS',
                    'minorUnit' => 8,
                ],
            'POLYX'    =>
                [
                    'symbol'    => 'POLYX',
                    'minorUnit' => 8,
                ],
            'POND'     =>
                [
                    'symbol'    => 'POND',
                    'minorUnit' => 8,
                ],
            'PORTO'    =>
                [
                    'symbol'    => 'PORTO',
                    'minorUnit' => 8,
                ],
            'POWR'     =>
                [
                    'symbol'    => 'POWR',
                    'minorUnit' => 8,
                ],
            'PROM'     =>
                [
                    'symbol'    => 'PROM',
                    'minorUnit' => 8,
                ],
            'PROS'     =>
                [
                    'symbol'    => 'PROS',
                    'minorUnit' => 8,
                ],
            'PSG'      =>
                [
                    'symbol'    => 'PSG',
                    'minorUnit' => 8,
                ],
            'PUNDIX'   =>
                [
                    'symbol'    => 'PUNDIX',
                    'minorUnit' => 8,
                ],
            'PYR'      =>
                [
                    'symbol'    => 'PYR',
                    'minorUnit' => 8,
                ],
            'QI'       =>
                [
                    'symbol'    => 'QI',
                    'minorUnit' => 8,
                ],
            'QKC'      =>
                [
                    'symbol'    => 'QKC',
                    'minorUnit' => 8,
                ],
            'QNT'      =>
                [
                    'symbol'    => 'QNT',
                    'minorUnit' => 8,
                ],
            'QTUM'     =>
                [
                    'symbol'    => 'QTUM',
                    'minorUnit' => 8,
                ],
            'QUICK'    =>
                [
                    'symbol'    => 'QUICK',
                    'minorUnit' => 8,
                ],
            'RAD'      =>
                [
                    'symbol'    => 'RAD',
                    'minorUnit' => 8,
                ],
            'RARE'     =>
                [
                    'symbol'    => 'RARE',
                    'minorUnit' => 8,
                ],
            'RAY'      =>
                [
                    'symbol'    => 'RAY',
                    'minorUnit' => 8,
                ],
            'RDNT'     =>
                [
                    'symbol'    => 'RDNT',
                    'minorUnit' => 8,
                ],
            'REEF'     =>
                [
                    'symbol'    => 'REEF',
                    'minorUnit' => 8,
                ],
            'REI'      =>
                [
                    'symbol'    => 'REI',
                    'minorUnit' => 8,
                ],
            'REN'      =>
                [
                    'symbol'    => 'REN',
                    'minorUnit' => 8,
                ],
            'REQ'      =>
                [
                    'symbol'    => 'REQ',
                    'minorUnit' => 8,
                ],
            'RIF'      =>
                [
                    'symbol'    => 'RIF',
                    'minorUnit' => 8,
                ],
            'RLC'      =>
                [
                    'symbol'    => 'RLC',
                    'minorUnit' => 8,
                ],
            'RNDR'     =>
                [
                    'symbol'    => 'RNDR',
                    'minorUnit' => 8,
                ],
            'ROSE'     =>
                [
                    'symbol'    => 'ROSE',
                    'minorUnit' => 8,
                ],
            'RPL'      =>
                [
                    'symbol'    => 'RPL',
                    'minorUnit' => 8,
                ],
            'RSR'      =>
                [
                    'symbol'    => 'RSR',
                    'minorUnit' => 8,
                ],
            'RUNE'     =>
                [
                    'symbol'    => 'RUNE',
                    'minorUnit' => 8,
                ],
            'RVN'      =>
                [
                    'symbol'    => 'RVN',
                    'minorUnit' => 8,
                ],
            'SAND'     =>
                [
                    'symbol'    => 'SAND',
                    'minorUnit' => 8,
                ],
            'SANTOS'   =>
                [
                    'symbol'    => 'SANTOS',
                    'minorUnit' => 8,
                ],
            'SC'       =>
                [
                    'symbol'    => 'SC',
                    'minorUnit' => 8,
                ],
            'SCRT'     =>
                [
                    'symbol'    => 'SCRT',
                    'minorUnit' => 8,
                ],
            'SEI'      =>
                [
                    'symbol'    => 'SEI',
                    'minorUnit' => 8,
                ],
            'SFP'      =>
                [
                    'symbol'    => 'SFP',
                    'minorUnit' => 8,
                ],
            'SHIB'     =>
                [
                    'symbol'    => 'SHIB',
                    'minorUnit' => 8,
                ],
            'SKL'      =>
                [
                    'symbol'    => 'SKL',
                    'minorUnit' => 8,
                ],
            'SLP'      =>
                [
                    'symbol'    => 'SLP',
                    'minorUnit' => 8,
                ],
            'SNT'      =>
                [
                    'symbol'    => 'SNT',
                    'minorUnit' => 8,
                ],
            'SNX'      =>
                [
                    'symbol'    => 'SNX',
                    'minorUnit' => 8,
                ],
            'SOL'      =>
                [
                    'symbol'    => 'SOL',
                    'minorUnit' => 8,
                ],
            'SPELL'    =>
                [
                    'symbol'    => 'SPELL',
                    'minorUnit' => 8,
                ],
            'SSV'      =>
                [
                    'symbol'    => 'SSV',
                    'minorUnit' => 8,
                ],
            'STEEM'    =>
                [
                    'symbol'    => 'STEEM',
                    'minorUnit' => 8,
                ],
            'STG'      =>
                [
                    'symbol'    => 'STG',
                    'minorUnit' => 8,
                ],
            'STMX'     =>
                [
                    'symbol'    => 'STMX',
                    'minorUnit' => 8,
                ],
            'STORJ'    =>
                [
                    'symbol'    => 'STORJ',
                    'minorUnit' => 8,
                ],
            'STPT'     =>
                [
                    'symbol'    => 'STPT',
                    'minorUnit' => 8,
                ],
            'STRAX'    =>
                [
                    'symbol'    => 'STRAX',
                    'minorUnit' => 8,
                ],
            'STX'      =>
                [
                    'symbol'    => 'STX',
                    'minorUnit' => 8,
                ],
            'SUI'      =>
                [
                    'symbol'    => 'SUI',
                    'minorUnit' => 8,
                ],
            'SUN'      =>
                [
                    'symbol'    => 'SUN',
                    'minorUnit' => 8,
                ],
            'SUPER'    =>
                [
                    'symbol'    => 'SUPER',
                    'minorUnit' => 8,
                ],
            'SUSHI'    =>
                [
                    'symbol'    => 'SUSHI',
                    'minorUnit' => 8,
                ],
            'SXP'      =>
                [
                    'symbol'    => 'SXP',
                    'minorUnit' => 8,
                ],
            'SYN'      =>
                [
                    'symbol'    => 'SYN',
                    'minorUnit' => 8,
                ],
            'SYS'      =>
                [
                    'symbol'    => 'SYS',
                    'minorUnit' => 8,
                ],
            'T'        =>
                [
                    'symbol'    => 'T',
                    'minorUnit' => 8,
                ],
            'TFUEL'    =>
                [
                    'symbol'    => 'TFUEL',
                    'minorUnit' => 8,
                ],
            'THETA'    =>
                [
                    'symbol'    => 'THETA',
                    'minorUnit' => 8,
                ],
            'TIA'      =>
                [
                    'symbol'    => 'TIA',
                    'minorUnit' => 8,
                ],
            'TKO'      =>
                [
                    'symbol'    => 'TKO',
                    'minorUnit' => 8,
                ],
            'TLM'      =>
                [
                    'symbol'    => 'TLM',
                    'minorUnit' => 8,
                ],
            'TRB'      =>
                [
                    'symbol'    => 'TRB',
                    'minorUnit' => 8,
                ],
            'TROY'     =>
                [
                    'symbol'    => 'TROY',
                    'minorUnit' => 8,
                ],
            'TRU'      =>
                [
                    'symbol'    => 'TRU',
                    'minorUnit' => 8,
                ],
            'TRX'      =>
                [
                    'symbol'    => 'TRX',
                    'minorUnit' => 8,
                ],
            'TUSD'     =>
                [
                    'symbol'    => 'TUSD',
                    'minorUnit' => 8,
                ],
            'TWT'      =>
                [
                    'symbol'    => 'TWT',
                    'minorUnit' => 8,
                ],
            'UFT'      =>
                [
                    'symbol'    => 'UFT',
                    'minorUnit' => 8,
                ],
            'UMA'      =>
                [
                    'symbol'    => 'UMA',
                    'minorUnit' => 8,
                ],
            'UNFI'     =>
                [
                    'symbol'    => 'UNFI',
                    'minorUnit' => 8,
                ],
            'UNI'      =>
                [
                    'symbol'    => 'UNI',
                    'minorUnit' => 8,
                ],
            'USDC'     =>
                [
                    'symbol'    => 'USDC',
                    'minorUnit' => 8,
                ],
            'USDP'     =>
                [
                    'symbol'    => 'USDP',
                    'minorUnit' => 8,
                ],
            'USDT'     =>
                [
                    'symbol'    => 'USDT',
                    'minorUnit' => 8,
                ],
            'USTC'     =>
                [
                    'symbol'    => 'USTC',
                    'minorUnit' => 8,
                ],
            'UTK'      =>
                [
                    'symbol'    => 'UTK',
                    'minorUnit' => 8,
                ],
            'VAI'      =>
                [
                    'symbol'    => 'VAI',
                    'minorUnit' => 8,
                ],
            'VANRY'    =>
                [
                    'symbol'    => 'VANRY',
                    'minorUnit' => 8,
                ],
            'VET'      =>
                [
                    'symbol'    => 'VET',
                    'minorUnit' => 8,
                ],
            'VGX'      =>
                [
                    'symbol'    => 'VGX',
                    'minorUnit' => 8,
                ],
            'VIB'      =>
                [
                    'symbol'    => 'VIB',
                    'minorUnit' => 8,
                ],
            'VIC'      =>
                [
                    'symbol'    => 'VIC',
                    'minorUnit' => 8,
                ],
            'VIDT'     =>
                [
                    'symbol'    => 'VIDT',
                    'minorUnit' => 8,
                ],
            'VITE'     =>
                [
                    'symbol'    => 'VITE',
                    'minorUnit' => 8,
                ],
            'VOXEL'    =>
                [
                    'symbol'    => 'VOXEL',
                    'minorUnit' => 8,
                ],
            'VTHO'     =>
                [
                    'symbol'    => 'VTHO',
                    'minorUnit' => 8,
                ],
            'WAN'      =>
                [
                    'symbol'    => 'WAN',
                    'minorUnit' => 8,
                ],
            'WAVES'    =>
                [
                    'symbol'    => 'WAVES',
                    'minorUnit' => 8,
                ],
            'WAXP'     =>
                [
                    'symbol'    => 'WAXP',
                    'minorUnit' => 8,
                ],
            'WBETH'    =>
                [
                    'symbol'    => 'WBETH',
                    'minorUnit' => 8,
                ],
            'WBTC'     =>
                [
                    'symbol'    => 'WBTC',
                    'minorUnit' => 8,
                ],
            'WIN'      =>
                [
                    'symbol'    => 'WIN',
                    'minorUnit' => 8,
                ],
            'WING'     =>
                [
                    'symbol'    => 'WING',
                    'minorUnit' => 8,
                ],
            'WLD'      =>
                [
                    'symbol'    => 'WLD',
                    'minorUnit' => 8,
                ],
            'WNXM'     =>
                [
                    'symbol'    => 'WNXM',
                    'minorUnit' => 8,
                ],
            'WOO'      =>
                [
                    'symbol'    => 'WOO',
                    'minorUnit' => 8,
                ],
            'WRX'      =>
                [
                    'symbol'    => 'WRX',
                    'minorUnit' => 8,
                ],
            'XAI'      =>
                [
                    'symbol'    => 'XAI',
                    'minorUnit' => 8,
                ],
            'XEC'      =>
                [
                    'symbol'    => 'XEC',
                    'minorUnit' => 8,
                ],
            'XEM'      =>
                [
                    'symbol'    => 'XEM',
                    'minorUnit' => 8,
                ],
            'XLM'      =>
                [
                    'symbol'    => 'XLM',
                    'minorUnit' => 8,
                ],
            'XMR'      =>
                [
                    'symbol'    => 'XMR',
                    'minorUnit' => 8,
                ],
            'XNO'      =>
                [
                    'symbol'    => 'XNO',
                    'minorUnit' => 8,
                ],
            'XRP'      =>
                [
                    'symbol'    => 'XRP',
                    'minorUnit' => 8,
                ],
            'XTZ'      =>
                [
                    'symbol'    => 'XTZ',
                    'minorUnit' => 8,
                ],
            'XVG'      =>
                [
                    'symbol'    => 'XVG',
                    'minorUnit' => 8,
                ],
            'XVS'      =>
                [
                    'symbol'    => 'XVS',
                    'minorUnit' => 8,
                ],
            'YFI'      =>
                [
                    'symbol'    => 'YFI',
                    'minorUnit' => 8,
                ],
            'YGG'      =>
                [
                    'symbol'    => 'YGG',
                    'minorUnit' => 8,
                ],
            'ZEC'      =>
                [
                    'symbol'    => 'ZEC',
                    'minorUnit' => 8,
                ],
            'ZEN'      =>
                [
                    'symbol'    => 'ZEN',
                    'minorUnit' => 8,
                ],
            'ZIL'      =>
                [
                    'symbol'    => 'ZIL',
                    'minorUnit' => 8,
                ],
            'ZRX'      =>
                [
                    'symbol'    => 'ZRX',
                    'minorUnit' => 8,
                ],
        ];
    }
}
