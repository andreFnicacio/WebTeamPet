<?php

namespace App\Helpers;

use App\Models\Vendas;
use App\Models\PetsPlanos;
use Carbon\Carbon;

class Utils
{
    const BRAZILIAN_DATE = 'd/m/Y';
    const BRAZILIAN_DATETIME = self::BRAZILIAN_DATE . ' H:i:s';
    const COMPETENCE = 'Y-m';
    const UTC_DATE = 'Y-m-d';
    const UTC_DATETIME = self::UTC_DATE . 'H:i:s';


    public static function ArrayToCsv($array=[], $columns=null, $separator=',')
    {
        $csv = [];
        if ($array===[]) return false;

        $array_columns = array_keys($array[0]);
        if (!$columns)
            $columns = $array_columns;

        /**
         * pega somente os resultados de columns
         * caso columns seja null, ele vai usar todos
         */
        foreach($array_columns as $key => $array_column)
        {
            if ($array_column && array_search($array_column, $columns) === false)
                unset($array_columns[$key]);

        }
        $array_columns = array_filter($array_columns);

        $csv[] = implode($separator,  $array_columns);

            foreach ($array as $arr) {

                $line = [];
                foreach ($array_columns as $array_column) {
                    $line[] = isset($arr[$array_column]) ? $arr[$array_column] : 'Nao Encontrado';
                }

                $csv[] = implode($separator, $line);

            }

        return implode(PHP_EOL, $csv);
    }

    /**
     * @param string $filename Path to the file
     * @param string $delimiter Delimiter used in CSV
     * @return array|bool
     */
    public static function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public static function ratio($decimal) {
        return self::decimal($decimal) . "%";
    }

    public static function money($input, $prefix = true)
    {
        $currency = "R$ ";
        if(!$prefix) {
            $currency = "";
        }
        return $currency . number_format($input, 2, ",", ".");
    }

    public static function moneyReverse($input)
    {
        $input = str_replace("R$ ", '', $input);
        $input = str_replace(".", '', $input);
        $input = str_replace(",", '.', $input);
        $input = number_format((float)$input, 2, ".", "");

        return floatval($input);
    }

    public static function decimal($input, $decimals = 2)
    {
        return number_format($input, $decimals, ",", ".");
    }

    public static function value($object, $field)
    {
        $value = $object->$field ? $object->$field : "\"\"";
        return "value=" . $value;
    }

    public static function petBadges(array $pets)
    {
        $badges = [];
        $used = [];
        foreach ($pets as $pet)
        {
            $color = $used[] = self::getBadgeColor($used);
            $badges[$pet->id] =  "<button type='button'
                             class='btn m-btn--pill btn-outline-{$color} btn-sm'
                             data-toggle='m-tooltip'
                             data-original-title='{$pet->nome_pet}'>
                             {$pet->inicial}
                    </button>";
        }

        return $badges;
    }

    private static function getBadgeColor(array $exclude = [])
    {
        $colors = [
            'focus',
            'accent',
            //'light',
            'metal',
            'brand',
            'primary',
            'success',
            'info',
            'danger',
            'warning'
        ];

        $remaining = array_diff($colors, $exclude);

        if(empty($remaining)) {
            $remaining = $colors;
        }

        return $remaining[array_rand($remaining)];
    }

    public static function shortDate(\Carbon\Carbon $date)
    {
        $now = new \Carbon\Carbon();
        $dozeHorasAtras = $now->copy()->subHours(12);
        $umaHoraAtras = $now->copy()->subMinutes(59);

        //Se for anterior a 12 horas:
        if($date->lt($dozeHorasAtras)) {
            return $date->format('d/m/Y h:i');
        } else {
            if($date->lt($umaHoraAtras)) {
                return $date->diffInHours($now) . ' hora(s)';
            }

            return $date->diffInMinutes($now) . ' mins';
        }
    }

    public static function dateTime(\Carbon\Carbon $date, $format = 'd/m/Y H:i:s')
    {
        return $date->format($format);
    }

    public static function excerpt($string, $limit = 80, $ending = '...')
    {
        $content = substr($string, 0, $limit);
        $pos = strrpos($content, " ");
        if ($pos>0) {
            $content = substr($content, 0, $pos) .  $ending;
        }

        return $content;
    }

    public static function getBancos() {
        return [
            "121"	 => "Banco Agibank S.A.",
            "025"	 => "Banco Alfa S.A.",
            "318"	 => "Banco BMG S.A.",
            "752"	 => "Banco BNP Paribas Brasil S.A.",
            "248"	 => "Banco Boavista Interatlântico S.A.",
            "218"	 => "Banco Bonsucesso S.A.",
            "065"	 => "Banco Bracce S.A.",
            "036"	 => "Banco Bradesco BBI S.A.",
            "204"	 => "Banco Bradesco Cartões S.A.",
            "394"	 => "Banco Bradesco Financiamentos S.A.",
            "237"	 => "Banco Bradesco S.A.",
            "225"	 => "Banco Brascan S.A.",
            "M15"	 => "Banco BRJ S.A.",
            "208"	 => "Banco BTG Pactual S.A.",
            "044"	 => "Banco BVA S.A.",
            "096"    => "Banco B3 S.A.",
            "263"	 => "Banco Cacique S.A.",
            "473"	 => "Banco Caixa Geral - Brasil S.A.",
            "412"	 => "Banco Capital S.A.",
            "040"	 => "Banco Cargill S.A.",
            "266"	 => "Banco Cédula S.A.",
            "739"    => "Banco Cetelem S.A.",
            "745"	 => "Banco Citibank S.A.",
            "M08"	 => "Banco Citicard S.A.",
            "241"	 => "Banco Clássico S.A.",
            "M19"	 => "Banco CNH Capital S.A.",
            "215"	 => "Banco Comercial e de Investimento Sudameris S.A.",
            "756"	 => "Banco Cooperativo do Brasil S.A. - BANCOOB",
            "748"	 => "Banco Cooperativo Sicredi S.A.",
            "075"	 => "Banco CR2 S.A.",
            "721"	 => "Banco Credibel S.A.",
            "222"	 => "Banco Credit Agricole Brasil S.A.",
            "505"	 => "Banco Credit Suisse (Brasil) S.A.",
            "229"	 => "Banco Cruzeiro do Sul S.A.",
            "336"    =>	"Banco C6 S.A – C6 Bank",
            "003"	 => "Banco da Amazônia S.A.",
            "083-3"	 => "Banco da China Brasil S.A.",
            "M21"	 => "Banco Daimlerchrysler S.A.",
            "707"	 => "Banco Daycoval S.A.",
            "300"	 => "Banco de La Nacion Argentina",
            "495"	 => "Banco de La Provincia de Buenos Aires",
            "494"	 => "Banco de La Republica Oriental del Uruguay",
            "M06"	 => "Banco de Lage Landen Brasil S.A.",
            "024"	 => "Banco de Pernambuco S.A. - BANDEPE",
            "456"	 => "Banco de Tokyo-Mitsubishi UFJ Brasil S.A.",
            "214"	 => "Banco Dibens S.A.",
            "335"    => "Banco Digio S.A.",
            "001"	 => "Banco do Brasil S.A.",
            "047"	 => "Banco do Estado de Sergipe S.A.",
            "037"	 => "Banco do Estado do Pará S.A.",
            "039"	 => "Banco do Estado do Piauí S.A. - BEP",
            "041"	 => "Banco do Estado do Rio Grande do Sul S.A.",
            "004"	 => "Banco do Nordeste do Brasil S.A.",
            "265"	 => "Banco Fator S.A.",
            "M03"	 => "Banco Fiat S.A.",
            "224"	 => "Banco Fibra S.A.",
            "626"	 => "Banco Ficsa S.A.",
            "M18"	 => "Banco Ford S.A.",
            "233"	 => "Banco GE Capital S.A.",
            "734"	 => "Banco Gerdau S.A.",
            "M07"	 => "Banco GMAC S.A.",
            "612"	 => "Banco Guanabara S.A.",
            "M22"	 => "Banco Honda S.A.",
            "063"	 => "Banco Ibi S.A. Banco Múltiplo",
            "M11"	 => "Banco IBM S.A.",
            "604"	 => "Banco Industrial do Brasil S.A.",
            "320"	 => "Banco Industrial e Comercial S.A.",
            "653"	 => "Banco Indusval S.A.",
            "630"	 => "Banco Intercap S.A.",
            "077"	 => "Banco Inter S.A.",
            "249"	 => "Banco Investcred Unibanco S.A.",
            "M09"	 => "Banco Itaucred Financiamentos S.A.",
            "184"	 => "Banco Itaú BBA S.A.",
            "479"	 => "Banco ItaúBank S.A",
            "376"	 => "Banco J. P. Morgan S.A.",
            "074"	 => "Banco J. Safra S.A.",
            "217"	 => "Banco John Deere S.A.",
            "076"	 => "Banco KDB S.A.",
            "757"	 => "Banco KEB do Brasil S.A.",
            "600"	 => "Banco Luso Brasileiro S.A.",
            "212"	 => "Banco Original S.A.",
            "M12"	 => "Banco Maxinvest S.A.",
            "389"	 => "Banco Mercantil do Brasil S.A.",
            "746"	 => "Banco Modal S.A.",
            "M10"	 => "Banco Moneo S.A.",
            "738"	 => "Banco Morada S.A.",
            "066"	 => "Banco Morgan Stanley S.A.",
            "243"	 => "Banco Máxima S.A.",
            "045"	 => "Banco Opportunity S.A.",
            "M17"	 => "Banco Ourinvest S.A.",
            "623"	 => "Banco Pan S.A.",
            "611"	 => "Banco Paulista S.A.",
            "613"	 => "Banco Pecúnia S.A.",
            "094-2"	 => "Banco Petra S.A.",
            "643"	 => "Banco Pine S.A.",
            "724"	 => "Banco Porto Seguro S.A.",
            "735"	 => "Banco Pottencial S.A.",
            "638"	 => "Banco Prosper S.A.",
            "M24"	 => "Banco PSA Finance Brasil S.A.",
            "747"	 => "Banco Rabobank International Brasil S.A.",
            "088-4"	 => "Banco Randon S.A.",
            "356"	 => "Banco Real S.A.",
            "633"	 => "Banco Rendimento S.A.",
            "741"	 => "Banco Ribeirão Preto S.A.",
            "M16"	 => "Banco Rodobens S.A.",
            "072"	 => "Banco Rural Mais S.A.",
            "453"	 => "Banco Rural S.A.",
            "422"	 => "Banco Safra S.A.",
            "033"	 => "Banco Santander (Brasil) S.A.",
            "250"	 => "Banco Schahin S.A.",
            "743"	 => "Banco Semear S.A.",
            "749"	 => "Banco Simples S.A.",
            "366"	 => "Banco Société Générale Brasil S.A.",
            "637"	 => "Banco Sofisa S.A.",
            "012"	 => "Banco Standard de Investimentos S.A.",
            "464"	 => "Banco Sumitomo Mitsui Brasileiro S.A.",
            "082-5"	 => "Banco Topázio S.A.",
            "M20"	 => "Banco Toyota do Brasil S.A.",
            "M13"	 => "Banco Tricury S.A.",
            "634"	 => "Banco Triângulo S.A.",
            "M14"	 => "Banco Volkswagen S.A.",
            "M23"	 => "Banco Volvo (Brasil) S.A.",
            "655"	 => "Banco Votorantim S.A.",
            "610"	 => "Banco VR S.A.",
            "348"    => "Banco XP S.A.",
            "370"	 => "Banco WestLB do Brasil S.A.",
            "021"	 => "BANESTES S.A. Banco do Estado do Espírito Santo",
            "719"	 => "Banif-Banco Internacional do Funchal (Brasil)S.A.",
            "755"	 => "Bank of America Merrill Lynch Banco Múltiplo S.A.",
            "744"	 => "BankBoston N.A.",
            "073"	 => "BB Banco Popular do Brasil S.A.",
            "078"	 => "BES Investimento do Brasil S.A.-Banco de Investimento",
            "069"	 => "BPN Brasil Banco Múltiplo S.A.",
            "070"	 => "BRB - Banco de Brasília S.A.",
            "092-2"	 => "Brickell S.A. Crédito, financiamento e Investimento",
            "104"	 => "Caixa Econômica Federal",
            "477"	 => "Citibank N.A.",
            "081-7"	 => "Concórdia Banco S.A.",
            "097-3"	 => "Cooperativa Central de Crédito Noroeste Brasileiro Ltda.",
            "085-x"	 => "Cooperativa Central de Crédito Urbano-CECRED",
            "099-x"	 => "Cooperativa Central de Economia e Credito Mutuo das Unicreds",
            "090-2"	 => "Cooperativa Central de Economia e Crédito Mutuo das Unicreds",
            "089-2"	 => "Cooperativa de Crédito Rural da Região de Mogiana",
            "087-6"	 => "Cooperativa Unicred Central Santa Catarina",
            "098-1"	 => "Credicorol Cooperativa de Crédito Rural",
            "487"	 => "Deutsche Bank S.A. - Banco Alemão",
            "751"	 => "Dresdner Bank Brasil S.A. - Banco Múltiplo",
            "064"	 => "Goldman Sachs do Brasil Banco Múltiplo S.A.",
            "062"	 => "Hipercard Banco Múltiplo S.A.",
            "399"	 => "HSBC Bank Brasil S.A. - Banco Múltiplo",
            "168"	 => "HSBC Finance (Brasil) S.A. - Banco Múltiplo",
            "492"	 => "ING Bank N.V.",
            "652"	 => "Itaú Unibanco Holding S.A.",
            "341"	 => "Itaú Unibanco S.A.",
            "079"	 => "JBS Banco S.A.",
            "488"	 => "JPMorgan Chase Bank",
            "014"	 => "Natixis Brasil S.A. Banco Múltiplo",
            "753"	 => "NBC Bank Brasil S.A. - Banco Múltiplo",
            "260"    => "Nu Pagamentos S.A. - Nubank",
            "086-8"	 => "OBOE Crédito Financiamento e Investimento S.A.",
            "290"    => "PagSeguro Internet S.A.",
            "254"	 => "Paraná Banco S.A.",
            "380"    =>	"PicPay Serviços S.A.",
            "197"    =>	"Stone Pagamentos S.A.",
            "340"    => "Super Pagamentos S/A (Superdital)",
            "409"	 => "UNIBANCO - União de Bancos Brasileiros S.A.",
            "230"	 => "Unicard Banco Múltiplo S.A.",
            "091-4"	 => "Unicred Central do Rio Grande do Sul",
            "136"    => "Unicred Cooperativa LTDA",
            "084"	 => "Unicred Norte do Paraná",
            "102"    => "XP Investimentos S.A."
        ];
    }

    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return null;
        }

        return $text;
    }

    public static function colorForLetter($letter) {
        $colorMatch = [
            'A' => 'blue',
            'B' => 'green',
            'C' => 'grey',
            'D' => 'red',
            'E' => 'yellow',
            'F' => 'purple',
            'G' => 'dark',
            'H' => 'blue-madison',
            'I' => 'blue-chambray',
            'J' => 'green-meadow',
            'K' => 'gray-salsa',
            'L' => 'gray-gallery',
            'M' => 'red-pink',
            'N' => 'yellow-gold',
            'O' => 'purple-plum',
            'P' => 'purple-intense',
            'Q' => 'red-itense',
            'R' => 'grey-cararra',
            'S' => 'yellow-soft',
            'T' => 'green-sharp',
            'U' => 'blue-hoki',
            'V' => 'blue-ebonyclay',
            'X' => 'gray-cascade',
            'W' => 'red-flamingo',
            'Y' => 'yellow-casablanca',
            'Z' => 'red-haze',
            '0' => 'grey-salt',
            '@' => 'purple-soft'
        ];

        $l = substr($letter, 0, 1);
        if($l === "") {
            return false;
        }
        $l = strtoupper($l);
        $index = '@';
        if (preg_match('/[A-Z]/', $l)) {
            $index = $l;
        } else if(preg_match('/\d/', $l)) {
            $index = '0';
        }

        return [
            'color' => $colorMatch[$index],
            'letter' => $l
        ];
    }

    public static function getMonthName($month)
    {
        switch ($month) {
            case 1:
                return "Janeiro";
                break;
            case 2:
                return "Fevereiro";
                break;
            case 3:
                return "Março";
                break;
            case 4:
                return "Abril";
                break;
            case 5:
                return "Maio";
                break;
            case 6:
                return "Junho";
                break;
            case 7:
                return "Julho";
                break;
            case 8:
                return "Agosto";
                break;
            case 9:
                return "Setembro";
                break;
            case 10:
                return "Outubro";
                break;
            case 11:
                return "Novembro";
                break;
            case 12:
                return "Dezembro";
                break;
        }
    }

    public static function getWeekName($month)
    {
        switch ($month) {
            case 0:
                return "Domingo";
                break;
            case 1:
                return "Segunda";
                break;
            case 2:
                return "Terça";
                break;
            case 3:
                return "Quarta";
                break;
            case 4:
                return "Quinta";
                break;
            case 5:
                return "Sexta";
                break;
            case 6:
                return "Sábado";
                break;
        }
    }

    public static function secondsToFormattedHours($duracao)
    {
        $hours = floor($duracao / 3600);
        $minutes = floor(($duracao / 60) % 60);
        $seconds = $duracao % 60;
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }

    public static function registrarVendasDoMes($ano = null, $mes = null)
    {
        if(!$ano) {
            $ano = Carbon::now()->year;
        }
        if(!$mes) {
            $mes = Carbon::now()->month;
        }

        $date = Carbon::createFromFormat('Y-m', "$ano" . "-" . "$mes");
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        /**
         * @var $vendas PetsPlanos[]
         */
        $vendas = PetsPlanos::whereBetween('data_inicio_contrato', [$start, $end])->get();

        foreach($vendas as $venda) {
            $exists = Vendas::where('id_pet', $venda->id_pet)
                            ->where('id_plano', $venda->id_plano)
                            ->whereBetween('data_inicio_contrato', [$start, $end])->exists();

            if(!$exists) {
                //O create já lança a comissão e a pontuação com triggers.
                (new Vendas())->create([
                    'id_cliente' => $venda->pet->cliente->id,
                    'id_vendedor' => $venda->id_vendedor,
                    'id_pet' => $venda->id_pet,
                    'id_plano' => $venda->id_plano,
                    'adesao' => 0,
                    'valor' => $venda->plano->preco_plano_individual,
                    'data_inicio_contrato' => $venda->data_inicio_contrato
                ]);
            }
        }
    }

    public static function calcRealPrice($price)
    {
        $day = Carbon::now()->day;
        $endMonth = 30.0;
        if($day >= $endMonth-1.0) {
            return $price;
        }
        $monthPercentage = (($endMonth-1.0 - $day)*100.0)/(float) $endMonth;
        return $price * ($monthPercentage/100.0);
    }

    public static function verificaCPF($cpf) {

        // Verifica se um número foi informado
        if(empty($cpf)) {
            return false;
        }
    
        // Elimina possivel mascara
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        
        // Verifica se o numero de digitos informados é igual a 11 
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se nenhuma das sequências invalidas abaixo 
        // foi digitada. Caso afirmativo, retorna falso
        else if ($cpf == '00000000000' || 
            $cpf == '11111111111' || 
            $cpf == '22222222222' || 
            $cpf == '33333333333' || 
            $cpf == '44444444444' || 
            $cpf == '55555555555' || 
            $cpf == '66666666666' || 
            $cpf == '77777777777' || 
            $cpf == '88888888888' || 
            $cpf == '99999999999') {
            return false;
            // Calcula os digitos verificadores para verificar se o
            // CPF é válido
            } else {   
            
            for ($t = 9; $t < 11; $t++) {
                
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) {
                    return false;
                }
            }
    
            return true;
        }
    }

    public static function dateToBrazilianDate($date, $format = 'Y-m-d', $ifEmpty = '')
    {
        if(empty($date)) {
            return '';
        }

        return Carbon::createFromFormat($format, $date)->format(self::BRAZILIAN_DATE);
    }

    public static function remove_accents($string) {
        if ( !preg_match('/[\x80-\xff]/', $string) )
            return $string;

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
            chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
            chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
            chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
            chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
            chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
            chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
            chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
            chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
            chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
            chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
            chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
            chr(195).chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
        );

        $string = strtr($string, $chars);

        return $string;
    }

    public static function getServerInfoToLog($server)
    {
        return [
            "USER" => isset($server["USER"]) ? $server["USER"] : null,
            "HOME" => isset($server["HOME"]) ? $server["HOME"] : null,
            "HTTP_COOKIE" => isset($server["HTTP_COOKIE"]) ? $server["HTTP_COOKIE"] : null,
            "HTTP_ACCEPT_LANGUAGE" => isset($server["HTTP_ACCEPT_LANGUAGE"]) ? $server["HTTP_ACCEPT_LANGUAGE"] : null,
            "HTTP_ACCEPT_ENCODING" => isset($server["HTTP_ACCEPT_ENCODING"]) ? $server["HTTP_ACCEPT_ENCODING"] : null,
            "HTTP_REFERER" => isset($server["HTTP_REFERER"]) ? $server["HTTP_REFERER"] : null,
            "HTTP_SEC_FETCH_DEST" => isset($server["HTTP_SEC_FETCH_DEST"]) ? $server["HTTP_SEC_FETCH_DEST"] : null,
            "HTTP_SEC_FETCH_MODE" => isset($server["HTTP_SEC_FETCH_MODE"]) ? $server["HTTP_SEC_FETCH_MODE"] : null,
            "HTTP_SEC_FETCH_SITE" => isset($server["HTTP_SEC_FETCH_SITE"]) ? $server["HTTP_SEC_FETCH_SITE"] : null,
            "HTTP_ORIGIN" => isset($server["HTTP_ORIGIN"]) ? $server["HTTP_ORIGIN"] : null,
            "HTTP_CONTENT_TYPE" => isset($server["HTTP_CONTENT_TYPE"]) ? $server["HTTP_CONTENT_TYPE"] : null,
            "HTTP_USER_AGENT" => isset($server["HTTP_USER_AGENT"]) ? $server["HTTP_USER_AGENT"] : null,
            "HTTP_SEC_CH_UA_MOBILE" => isset($server["HTTP_SEC_CH_UA_MOBILE"]) ? $server["HTTP_SEC_CH_UA_MOBILE"] : null,
            "HTTP_X_REQUESTED_WITH" => isset($server["HTTP_X_REQUESTED_WITH"]) ? $server["HTTP_X_REQUESTED_WITH"] : null,
            "HTTP_ACCEPT" => isset($server["HTTP_ACCEPT"]) ? $server["HTTP_ACCEPT"] : null,
            "HTTP_SEC_CH_UA" => isset($server["HTTP_SEC_CH_UA"]) ? $server["HTTP_SEC_CH_UA"] : null,
            "HTTP_CACHE_CONTROL" => isset($server["HTTP_CACHE_CONTROL"]) ? $server["HTTP_CACHE_CONTROL"] : null,
            "HTTP_PRAGMA" => isset($server["HTTP_PRAGMA"]) ? $server["HTTP_PRAGMA"] : null,
            "HTTP_CONTENT_LENGTH" => isset($server["HTTP_CONTENT_LENGTH"]) ? $server["HTTP_CONTENT_LENGTH"] : null,
            "HTTP_CONNECTION" => isset($server["HTTP_CONNECTION"]) ? $server["HTTP_CONNECTION"] : null,
            "HTTP_HOST" => isset($server["HTTP_HOST"]) ? $server["HTTP_HOST"] : null,
            "REDIRECT_STATUS" => isset($server["REDIRECT_STATUS"]) ? $server["REDIRECT_STATUS"] : null,
            "SERVER_NAME" => isset($server["SERVER_NAME"]) ? $server["SERVER_NAME"] : null,
            "SERVER_PORT" => isset($server["SERVER_PORT"]) ? $server["SERVER_PORT"] : null,
            "SERVER_ADDR" => isset($server["SERVER_ADDR"]) ? $server["SERVER_ADDR"] : null,
            "REMOTE_PORT" => isset($server["REMOTE_PORT"]) ? $server["REMOTE_PORT"] : null,
            "REMOTE_ADDR" => isset($server["REMOTE_ADDR"]) ? $server["REMOTE_ADDR"] : null,
            "SERVER_SOFTWARE" => isset($server["SERVER_SOFTWARE"]) ? $server["SERVER_SOFTWARE"] : null,
            "GATEWAY_INTERFACE" => isset($server["GATEWAY_INTERFACE"]) ? $server["GATEWAY_INTERFACE"] : null,
            "HTTPS" => isset($server["HTTPS"]) ? $server["HTTPS"] : null,
            "REQUEST_SCHEME" => isset($server["REQUEST_SCHEME"]) ? $server["REQUEST_SCHEME"] : null,
            "SERVER_PROTOCOL" => isset($server["SERVER_PROTOCOL"]) ? $server["SERVER_PROTOCOL"] : null,
            "DOCUMENT_ROOT" => isset($server["DOCUMENT_ROOT"]) ? $server["DOCUMENT_ROOT"] : null,
            "DOCUMENT_URI" => isset($server["DOCUMENT_URI"]) ? $server["DOCUMENT_URI"] : null,
            "REQUEST_URI" => isset($server["REQUEST_URI"]) ? $server["REQUEST_URI"] : null,
            "SCRIPT_NAME" => isset($server["SCRIPT_NAME"]) ? $server["SCRIPT_NAME"] : null,
            "CONTENT_LENGTH" => isset($server["CONTENT_LENGTH"]) ? $server["CONTENT_LENGTH"] : null,
            "CONTENT_TYPE" => isset($server["CONTENT_TYPE"]) ? $server["CONTENT_TYPE"] : null,
            "REQUEST_METHOD" => isset($server["REQUEST_METHOD"]) ? $server["REQUEST_METHOD"] : null,
            "QUERY_STRING" => isset($server["QUERY_STRING"]) ? $server["QUERY_STRING"] : null,
            "SCRIPT_FILENAME" => isset($server["SCRIPT_FILENAME"]) ? $server["SCRIPT_FILENAME"] : null,
            "FCGI_ROLE" => isset($server["FCGI_ROLE"]) ? $server["FCGI_ROLE"] : null,
            "PHP_SELF" => isset($server["PHP_SELF"]) ? $server["PHP_SELF"] : null,
            "REQUEST_TIME_FLOAT" => isset($server["REQUEST_TIME_FLOAT"]) ? $server["REQUEST_TIME_FLOAT"] : null,
            "REQUEST_TIME" => isset($server["REQUEST_TIME"]) ? $server["REQUEST_TIME"] : null
        ];
    }

    public static function brazilianFloat($string)
    {
        $number = floatval(str_replace(',', '.', str_replace('.', '', $string)));

        return $number;
    }



    public static function dateExploded($date)
    {

        $possibilities = ['-', '/'];        
        $separator = null;
        
        foreach($possibilities as $possibilitie)
        {
            if(strpos($date, $possibilitie) !== false) {
                $separator = $possibilitie;
                break;
            }
        }

        if ($separator)
        {
            return explode($separator, $date);
        }

        return $date;

    }

    public static function numberOnly($string)
    {
        return preg_replace('/[^0-9]/', '', $string);
    }

    public static function formataTelefone($numero){
        $numero = explode('/', $numero)[0];
        $numero = self::numberOnly($numero);
        $tamanhoTelefone = strlen($numero);
        switch ($tamanhoTelefone) {
            case 10: //tel fixo
                return '(' . $numero[0] . $numero[1] . ') ' . substr($numero, -8, 4) . '-' . substr($numero, -4, 4);
                break;
            case 11: //celular
                return '(' . $numero[0] . $numero[1] . ') ' . substr($numero, -9, 5) . '-' . substr($numero, -4, 4);
                break;
            default:
                return $numero;
                break;
        }
    }

    public static function formataCPF($numero){
        $numero = self::numberOnly($numero);
        $tamanhoNumero = strlen($numero);
        switch ($tamanhoNumero) {
            case 11: //CPF
                return substr($numero,0,3) . '.' . substr($numero,3,3) . '.' . substr($numero,6,3) . '-' . substr($numero,-2);
                break;
            case 14: //CNPJ
                return substr($numero,0,2) . '.' . $bloco_2 = substr($numero,2,3) . '.' . substr($numero,5,3) . '/' . substr($numero,8,4) . '-' . substr($numero,-2);
                break;
            default:
                return $numero;
                break;
        }
    }
}
